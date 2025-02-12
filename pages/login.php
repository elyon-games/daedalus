<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');

if (isset($userInfo))
    header('location:/menu');

$tagError = null;
$passwordError = null;
$passwordConfirmError = null;
$emailError = null;
$acceptError = null;
$bannedError = null;

$tag = null;
$password = null;
$passwordConfirm = null;
$email = null;
$accept = null;

if (isset($_POST['signup'])) {
    $tag = trim($_POST['tag'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $email = $_POST['email'] ?? '';
    $accept = $_POST['accept'] ?? '';

    if (!validateTag($tag))
        $tagError = 'This tag is invalid. Nametags must only contain lower and uppercase latin letters, numbers and the following special characters: _-#$€';
    if (!validatePassword($password))
        $passwordError = 'The password must be between 8 and 64 characters long and can only include letters, numbers, and the following special characters: _-#$€';
    if ($password !== $passwordConfirm)
        $passwordConfirmError = 'The confirmation password does not match the original password';
    if (!validateEmail($email))
        $emailError = 'The given email address is invalid, or it is longer than 64 characters';
    if (!$accept)
        $acceptError = 'You must accept the terms and conditions to continue';

    if (!$tagError && !$passwordError && !$passwordConfirmError && !$emailError && !$acceptError) {
        $hashedPwd = password_hash($password, PASSWORD_ARGON2I);
        $check = $connection->prepare("SELECT email FROM player WHERE email = ?");
        $check->execute([$email]);
        if (count($check->fetchAll()) > 0) {
            $emailError = "This email address is already linked to an account";
        } else {
            $clientID = str_replace(".", "", uniqid("", true));
            createPfp($clientID);
            $stmt = $connection->prepare("INSERT INTO player (`tag`, `password`, `email`, `pfp_extension`, `id`, `admin`, `tuto_finished`) VALUES (?, ?, ?, ?, ?, 0, 0)");
            $stmt->execute([$tag, $hashedPwd, $email, 'png', $clientID]);
            setcookie('id', $clientID, ['path' => '/']);
            $_SESSION['password'] = $hashedPwd;
            header('location:/adventure/1/play');        
        }
    }
}

if (isset($_POST['login'])) {
    $email = cleanData($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!validateEmail($email))
        $emailError = 'The given email address is invalid, or it is longer than 64 characters';
    if (!validatePassword($password))
        $passwordError = 'The password must be between 8 and 64 characters long and can only include letters, numbers, and the following special characters: _-#$€';

    if (!$emailError && !$passwordError) {
        try {
            $stmt = $connection->prepare("SELECT `password`, `id`, `banned`, `tuto_finished` FROM player WHERE `email` = ?");
            $stmt->execute([$email]);
            $line = $stmt->fetch();
            if (isset($line['password']) && password_verify($password, $line['password'])) {
                if ($line['banned'] == 1) {
                    $bannedError = 'Your account has been banned. Please contact support for more information.';
                } else {
                    setcookie('id', $line['id'], ['path' => '/']);
                    $_SESSION['password'] = $line['password'];
                    if ($line['tuto_finished'] == 0) {
                        header('location:/adventure/1/play');
                    } else {
                        header('location:/menu');
                    }
                }
            } else {
                $emailError = empty($line['password']) ? "This email address is not linked to any account" : 'Wrong password !';
            }
        } catch (Exception $e) {
            echo "<span class='error'>An error occurred</span>";
        }
    }
}

getPageHead('Log in | Sign up', 'login');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="/css/login.css">
    <script>
        function onToggle(toggle) {
            document.getElementById('switch').classList.toggle('toggled', toggle);
            document.querySelector('.flip-card-inner').style.height = document.querySelector(`.flip-card-${toggle ? 'signup' : 'login'}`).clientHeight + 'px';
        }
        document.addEventListener('DOMContentLoaded', () => onToggle(<?= isset($_GET['signup']) ? 'true' : 'false' ?>));
    </script>
</head>

<body>
    <div id="banner">Neon Nexus</div>
    <div class="diverror">
        <?php if ($emailError && isset($_POST['login'])): ?>
            <span class='error'><?= $emailError ?></span>
        <?php endif; ?>
        <?php if ($passwordError && isset($_POST['login'])): ?>
            <span class='error'><?= $passwordError ?></span>
        <?php endif; ?>
        <?php if ($bannedError): ?>
            <span class='error'><?= $bannedError ?></span>
        <?php endif; ?>
        <?php if ($tagError): ?>
            <span class='error'><?= $tagError ?></span>
        <?php endif; ?>
        <?php if ($emailError && isset($_POST['signup'])): ?>
            <span class='error'><?= $emailError ?></span>
        <?php endif; ?>
        <?php if ($passwordError && isset($_POST['signup'])): ?>
            <span class='error'><?= $passwordError ?></span>
        <?php endif; ?>
        <?php if ($passwordConfirmError): ?>
            <span class='error'><?= $passwordConfirmError ?></span>
        <?php endif; ?>
        <?php if ($acceptError): ?>
            <span class='error'><?= $acceptError ?></span>
        <?php endif; ?>
    </div>
    <main class="container vertical center">
        <div class="container horizontal center" id="login-signup-wrapper">
            <div class="form-wrapper login">
                <h6 class="title">Log in</h6>
                <form class="form-generic center" action="#" method="post">
                    <div class="error-wrapper">
                        <div class="input-box">
                            <label class="center">Email </label>
                            <input type="email" name="email" class="email" placeholder=" " value="<?= $email ?? '' ?>">
                        </div>
                    </div>
                    <div class="error-wrapper">
                        <div class="input-box">
                            <label class="psw1">Password </label>
                            <input type="password" class="password" name="password" placeholder=" ">
                        </div>
                    <a href='reset-password-send' class="forgetpwd">Forgot your password?</a>
                    </div>
                    <input type="submit" name="login" value="Log in" class="signupbtn">
                </form>
            </div>

            <div class="form-wrapper signup">
                <h6 class="title">Sign up</h6>
                <form class="form-generic center" action="?signup#" method="post" onsubmit="return validateForm()">
                    <div class="error-wrapper">
                        <div class="input-box">
                            <label class="username">Username</label>
                            <input type="text" name="tag" class="name" placeholder=" " value="<?= $tag ?? '' ?>" required>
                        </div> 
                    </div>
                    <div class="error-wrapper">
                        <div class="input-box">
                            <label class="center">Email</label>
                            <input type="email" name="email" class="email" placeholder=" " value="<?= $email ?? '' ?>" required>
                        </div>
                    </div>
                    <div class="error-wrapper">
                        <div class="input-box">
                            <label class="psw2">Password</label>
                            <input type="password" class="password" name="password" placeholder=" " required>
                        </div>
                    </div>
                    <div class="error-wrapper">
                        <div class="input-box confpsw">
                            <label class="psw3">Confirm Password</label>
                            <input type="password" class="password" name="password_confirm" placeholder=" " required>
                        </div>
                    </div>
                    <div class="conditions-accept">
                        <label class="check-box accept">
                            <input type="checkbox" name="accept">
                            <svg viewBox="0 -5 64 64" class="accept">
                                <path
                                    d="M 0 16 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 16 L 32 48 L 64 16 V 8 A 8 8 90 0 0 56 0 H 8 A 8 8 90 0 0 0 8 V 56 A 8 8 90 0 0 8 64 H 56 A 8 8 90 0 0 64 56 V 16"
                                    pathLength="575.0541381835938" class="path">
                                </path>
                            </svg>
                        </label>
                        <p>I accept the <a href="/conditions">terms & conditions</a></p>
                    </div>
                    <input type="submit" name="signup" value="Sign up" class="signupbtn">
                </form>
            </div>
        </div>
    </main>
</body>

</html>
