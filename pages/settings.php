<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="/css/settings.css">
    <?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');

    // Si l'utilisateur n'est pas connecté, on le redirge vers la page de connexion
    if (empty($userInfo))
        header('location:./login');

    // Messages de confirmation
    $updateConfirm = null;
    $sessionConfirm = null;
    $warning = null;

    // Définit les messages d'erreur à utiliser plus tard
    $tagError = null;
    $emailError = null;
    $pfpError = null;

    // Définit les valeurs à utiliser pour auto-remplir la page (ou pour la vérification)
    $tag = null;
    $email = null;
    $pfp = null;

    // Affiche le message de confirmation des changements
    if (isset($_SESSION['updateAccount'])) {
        $updateConfirm = 'Changes saved !';
        $_SESSION['updateAccount'] = null;
    }

    // Si l'utilisateur a rempli le form pour modifier son compte :
    if (isset($_POST['updateAccount'])) {
        // Définit les données à vérifier
        if (array_key_exists('tag', $_POST))
            $tag = cleanData($_POST['tag']);
        if (array_key_exists('email', $_POST))
            $email = $_POST['email'];

        // Vérifie si la photo de profil a été donnée parmi les infos
        if (array_key_exists('pfp', $_FILES) && $_FILES['pfp']['error'] != 4) {
            $pfp = $_FILES['pfp'];
            // On vérifie que le fichier soit d'une taille acceptable
            if ($pfp['error'] == 2 || $pfp['error'] == 1 || $pfp['size'] > 30_000_000) {
                $pfp = null;
                $pfpError = 'The picture\'s size must not exceed 30MB';
            } else {
                [$type, $extension] = explode('/', $pfp['type']);
                // On vérifie que le fichier soit bien une image
                if ($type != 'image') {
                    $pfp = null;
                    $pfpError = 'This file is not a valid image !';
                } else {
                    $invalid = true;
                    $pfpName = '../images/pfp/' . $userInfo['id'] . '.';

                    // Supprime la photo de profil précédente si elle existe
                    if (file_exists($pfpName . $userInfo['pfp_extension']))
                        $invalid = unlink($pfpName . $userInfo['pfp_extension']);

                    // Enregistre la nouvelle extension de photo de profil
                    $stmt = $connection->prepare("UPDATE player SET pfp_extension = ? WHERE id = ?");
                    $invalid = $invalid && $stmt->execute([$extension, $userInfo['id']]);

                    // Déplace l'image au bon endroit
                    $invalid = $invalid && move_uploaded_file($pfp['tmp_name'], $pfpName . $extension);

                    // Si une erreur est apparue
                    if (!$invalid)
                        $pfpError = 'Sorry, an unknown error occured';
                }
            }
        }

        // ----------------------- Tests de validité des différentes valeurs données -----------------------
    
        // Permet d'éviter l'injection en cas de valeurs possibles modifiées côté client
        if (!validateTag($tag))
            $tagError = 'This name is invalid. Names must only contain word characters or ideograms, hyphens and apostrophes, and be shorter than 64 characters';

        if (!validateEmail($email))
            $emailError = 'The given email adress is invalid, or it is longer than 64 characters';


        // S'il n'y a aucune erreur, alors on modifie les paramètres du compte
        // Le compte existe forcément déjà, car userInfo est défini
        if (
            !isset($tagError)
            && !isset($emailError)
            && !isset($pfpError)
        ) {
            // On enregistre les infos de l'utilisateur dans la BDD
            $stmt = $connection->prepare("UPDATE player SET `tag` = ?, `password` = ?, `email` = ?, `admin` = ? WHERE id = ?");
            $res = $stmt->execute([$tag, $userInfo['password'], $email, $userInfo['admin'], $userInfo['id']]);
            $_SESSION['updateAccount'] = true;
            header('location:#account-info');
        }
    }

    // Si l'utilisateur désire se déconnecter
    if (isset($_POST['disconnect']))
        disconnect();

    // Si l'utilisateur désire supprimer son compte
    if (isset($_POST['deleteAccount']))
        $warning = 'Are you sure you want to delete your account ? This operation cannot be undone.';

    // Si l'utilisateur a confirmé la suppression de son compte
    if (isset($_POST['deleteAccountConfirm'])) {

        // Puis la photo de profil
        unlink(realpath('../images/pfp/' . $userInfo['id'] . '.' . $userInfo['pfp_extension']));

        // Et enfin, les données utilisateur
        $stmt = $connection->prepare("DELETE FROM player WHERE id = ?");
        $stmt->execute([$userInfo['id']]);
        disconnect();
        header('location:/login');
    }

    getPageHead('Account - Parameters', 'account');
    ?>
    <script src="/util/imageChange.js"></script>
</head>

<body>
    <?php getPageHeader('Settings', $userInfo); ?>
    <main>
        <id="main-object">
            <div id="info">
                <form class="form-generic" enctype="multipart/form-data" action="#account-info" method="post">
                    <div class="title">
                        <h3 id="account-info"><?php echo 'Profile informations' ?></h3>
                    </div>
                    <?php if (isset($updateConfirm))
                        echo '<span class="confirm">' . $updateConfirm . '</span>'; ?>
                    <div class="info">
                        <div id="pfp-container">
                            <img id="pfp-preview"
                                src="/images/pfp/<?= $userInfo['id'] . '.' . $userInfo['pfp_extension'] ?>">
                            <div id="pfp-overlay" onclick="document.getElementById('pfp-input').click()">
                                <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
                                <input id="pfp-input" type="file" name="pfp" hidden
                                    onchange="updateImage(this.files, 'pfp-preview')"
                                    accept="image/jpg, image/jpeg, image/png, image/webp, image/gif">
                                <label><?= 'Change profile picture' ?></label>
                            </div>
                            <?php if ($pfpError)
                                echo "<span class='error'>$pfpError</span>" ?>
                            </div>
                            <div class="changes">
                                <div class="input-box">
                                    <label for="tag">Usertag :</label>
                                    <input type="text" name="tag" id="tag" placeholder=" " <?php if (isset($userInfo['tag']))
                                echo "value='$userInfo[tag]'"; ?> required>
                                <input id="sub" type="submit" name="updateAccount" value="Modify">
                            </div><?php if ($tagError)
                                echo "<span class='error'>$tagError</span>"; ?>
                        </div>

                        <div class="changes">
                            <div class="input-box">
                                <label for="email">Email : </label>
                                <input type="email" name="email" id="email" placeholder=" " <?php if (isset($userInfo['email']))
                                    echo "value='$userInfo[email]'"; ?> required>
                                <input id="sub" type="submit" name="updateAccount" value="Modify">
                            </div><?php if ($emailError)
                                echo "<span class='error'>$emailError</span>"; ?>
                        </div>

                    </div>
                </form>
            </div>

            <div class="other">
                <form id="danger-zone" action="#danger-zone" method="post">
                    <?php
                    if (isset($warning)) { ?>
                        <span class="warn"> <?= $warning ?></span>
                        <input id="deleteAccount" type="submit" name="deleteAccountConfirm" value="<?= 'Confirm deletion' ?>">
                    <?php } else { ?>
                        <input id="deleteAccount" type="submit" name="deleteAccount" value="<?= 'Delete account' ?>">
                    <?php } ?>
                </form>

                <form id="disco" class="form-generic" action="#session" method="post">
                    <?php if (isset($sessionConfirm))
                        echo '<span class="confirm">' . $sessionConfirm . '</span>'; ?>
                    <a id="disc-btn" href="disconnect"><img src="/images/logout.png" alt="Logout Icon" class="logout"> Log Out</a>
                </form>
            </div>
</body>

</html>