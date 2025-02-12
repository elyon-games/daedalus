<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php getPageHead('Reset Password', 'Reset Password') ?>
    <link rel="stylesheet" href="/css/forgetpassword.css">
</head>
<body>
<?php
function base64UrlEncode($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function createJWT($header, $payload, $secret) {
    $base64UrlHeader = base64UrlEncode(json_encode($header));
    $base64UrlPayload = base64UrlEncode(json_encode($payload));
    $signatureInput = $base64UrlHeader . "." . $base64UrlPayload;
    $signature = hash_hmac('sha256', $signatureInput, $secret, true);
    $base64UrlSignature = base64UrlEncode($signature);
    $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    return $jwt;
}

function base64UrlDecode($data) {
    $base64 = strtr($data, '-_', '+/');
    return base64_decode($base64 . str_repeat('=', 3 - (3 + strlen($data)) % 4));
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

$secret = '578f095e-e4a6-45bc-ad96-3d6fd2aaa7b8';

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    echo '<div id="banner"><p class="gamename">Neon Nexus</p></div>';
    echo '<form action="" method="post" class="info"><p class="title">Password Reset</p>';
    echo '<label for="email"class="email">Email </label><input type="email" id="email" name="email" class="email" required>';
    echo '<label for="confirm_email" class="email gap">Confirm Email </label><input type="email" id="confirm_email" name="confirm_email" class="email" required>';
    echo '<button type="submit" class="send">Envoyer</button>';
    echo '</form>';
    echo'<a href="/login" class="back">Retour</a>';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $confirmEmail = filter_var($_POST['confirm_email'], FILTER_SANITIZE_EMAIL);
        if (filter_var($confirmEmail, FILTER_VALIDATE_EMAIL)) {
            if ($email === $confirmEmail) {
                // Emails match, continue with the rest of the code
            } else {
                echo "The emails do not match.";
            }
        } else {
            echo "Invalid confirm email address.";
        }
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $checkEmail = $connection->prepare("SELECT id, tag FROM player WHERE email = ?");
        $checkEmail->execute([$email]);
        $resultCheck = $checkEmail->fetch(PDO::FETCH_ASSOC);

        if ($resultCheck) {
            $id = $resultCheck['id'];
            $tag = $resultCheck['tag'];

            $curl = curl_init();

            $jwt = createJWT([
                'alg' => 'HS256',
                'typ' => 'JWT'
            ], [
                'iss' => 'daedalus',
                'id' => $id,
                'exp' => time() + 3600,
                'iat' => time()
            ], $secret);

            $link = 'https://daedalus.younity-mc.fr/reset-password-valid?token=' . $jwt;

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://api.younity-mc.fr/athlas/mail?code=" . $secret . "&appID=daedalus",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'title' => 'Reset Password',
                    'name' => 'daedalus',
                    'html' => '<h1>Salut '.$tag.'</h1><br>' . $link,
                    'email' => $email
                ]),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                echo "cURL Error #:" . $err;
            } else {
                echo "L'email a été envoyé";
            }
        } else {
            echo "The email does not exist in the database.";
        }
    } else {
        echo "Invalid email address.";
    }
} else {
    echo "void";
}
?>
</body>
</html>
