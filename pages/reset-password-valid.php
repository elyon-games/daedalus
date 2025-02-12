<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php getPageHead('Reset Password', 'Reset Password') ?>
</head>
<body>
<?php

function base64UrlDecode($data) {
    $base64 = strtr($data, '-_', '+/');
    return base64_decode($base64 . str_repeat('=', 3 - (3 + strlen($data)) % 4));
}

function decodeJWT($jwt) {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        return false;
    }
    $payload = base64_decode($parts[1]);
    $decodedPayload = json_decode($payload, true);
    return $decodedPayload;
}

function verifyJWT($jwt, $secret) {
    list($base64UrlHeader, $base64UrlPayload, $base64UrlSignature) = explode('.', $jwt);
    $header = json_decode(base64UrlDecode($base64UrlHeader), true);
    $payload = json_decode(base64UrlDecode($base64UrlPayload), true);
    $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;
    $signature = base64UrlDecode($base64UrlSignature);
    $expectedSignature = hash_hmac('sha256', $signatureInput, $secret, true);
    if (hash_equals($expectedSignature, $signature)) {
        $currentTime = time();
        if (isset($payload['exp']) && $payload['exp'] < $currentTime) {
            return false;
        }
        if (isset($payload['iat']) && $payload['iat'] > $currentTime) {
            return false;
        }
        return true;
    } else {
        return false;
    }
}

$secret = 'f052b99a-c48b-4b68-af7c-92dda5a74e2f';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['token'])) {

    $token = $_GET['token'];
    $verify = verifyJWT($token, $secret);
    if($verify){
        echo '<form action="" method="post"><label for="password">Nouveau Mots de passe :</label><input type="password" id="password" name="password" required><button type="submit">Envoyer</button></form>';
    } else {
        echo "Votre demande a expiré ou est invalide";
    }

} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $token = $_GET['token'];
    $verify = verifyJWT($token, $secret);
    if($verify){
        $data = decodeJWT($token);
        $hashedPwd = password_hash($_POST['password'], PASSWORD_ARGON2I);
        $updatePassword = $connection->prepare("UPDATE player SET password = ? WHERE id = ?");
        $updatePassword->execute([$hashedPwd, $data['id']]);
        if ($updatePassword->rowCount() > 0) {
            echo "Mot de passe mis à jour avec succès.";
        } else {
            echo "Erreur lors de la mise à jour du mot de passe.";
        }
    } else {
        echo "Votre demande a expiré ou est invalide";
    }

} else {
    echo "RIEN";
}
?>
</body>
</html>