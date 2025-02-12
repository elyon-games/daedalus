<?php
// File used to incude HTML Elements common to multiple pages, such as the header and the footer
define('DB_HOSTNAME', "panel.younity-mc.fr");
define('DB_NAME', "s1_daedalus-elyon");
define('DB_USERNAME', "u1_5rlOMFf1Q1");
define('DB_PASSWORD', "KWzOD1Uc+KJQwBDJ+!KVHl2h");


function getPageHeader($pageName = '', $uInfo = null)
{
    if (empty($uInfo) && is_array($pageName)) {
        $uInfo = $pageName;
        $pageName = null;
    }
    ?>
    <header>
        <p class="GameName">Neon Nexus</p>
        <div class="headbtn">
            <a href="/adventure"><img src="/images/adv.png" class="imggrad"></a>
            <a href="/levels"><img src="/images/crea.png" class="imggrad"></a>
            <a href="/infinite"><img src="/images/infi.png" class="imggrad"></a>
        </div>
        <a href="/menu" class="menu"><img src="/images/menu.png" class="imggrad"></a>
        <?php if (isset($uInfo)) { ?>
            <div class="pfp">
                <div class="usertag"><?php echo $uInfo['tag'] ?></div>
                <div>
                    <span id="img-rond" class="rond nav-opened" style="opacity: 0;">
                        <a href="/profile"><img src="/images/profil.png" alt="Profile-Icon"> Profile</a>
                        <a href="/settings"><img src="/images/settings.png" alt="Settings-Icon"> Settings</a>
                        <?php
                        if ($uInfo['admin'] == 1)
                            echo '<a href="/admin"><img src="/images/admin.png" alt="Admin-Icon">Admin</a>';
                        ?>
                        <a href="/disconnect"><img src="/images/logout.png" alt="Logout-Icon"> Log Out</a>
                    </span>
                    <img src="/images/pfp/<?php echo $uInfo['id'] ?>.<?php echo $uInfo['pfp_extension'] ?>" alt="user"
                        class="profile-image">
                </div>
            </div>
        <?php } ?>
    </header>
    <?php
}

function getPageFooter()
{
    ?>
    <footer>
        CIR1 Web Project - April 2024
        <a href="/conditions">Terms & conditions</a>
    </footer>
    <?php
}

function getPageHead(string $title, string|null $cssFileName = '')
{
    ?>
    <title><?= $title ?></title>
    <link rel="stylesheet" href="/css/main.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kaushan+Script&family=Kelly+Slab&display=swap" rel="stylesheet">
    <script>
        let ost;
        document.addEventListener('DOMContentLoaded', () => {
            const e = document.getElementById('img-rond');
            ost = document.getElementById('soundtrack');
            if (e) {
                e.style.setProperty('--height', e.clientHeight + 'px');
                e.style.setProperty('--width', e.clientWidth + 'px');
                e.classList.remove('nav-opened');
                setTimeout(() => e.style.opacity = 1, 300);
            }
        });

        document.addEventListener("visibilitychange", (event) => {
            if (document.visibilityState == "visible") {
                playOST();
            } else {
                pauseOST();
            }
        });

        document.addEventListener('click', (e) => {
            if ((!e.target.classList?.contains('profile-image') && !document.getElementById('img-rond')?.contains(e.target)) || (document.getElementById('img-rond')?.classList.contains('nav-opened') && e.target.classList.contains('profile-image'))) {
                document.getElementById('img-rond')?.classList.remove('nav-opened');
            } else if (e.target?.classList.contains('profile-image')) {
                document.getElementById('img-rond')?.classList.add('nav-opened');
            }
        });

        function playOST() {
            if (ost.paused) try { ost.play(); } catch (e) { console.log('Error while playing audio : ', e); };
        }

        function pauseOST() {
            if (!ost.paused) try { ost.pause(); } catch (e) { console.log('Error while pausing audio : ', e); };
        }
    </script>
    <audio id="soundtrack" autoplay loop>
        <source src="/sounds/soundtrack.mp3" type="audio/mp3">
    </audio>
    <?php
    if ($cssFileName)
        echo '<link rel="stylesheet" href="/css/' . $cssFileName . '.css">';


}

// Convertit une couleur HSL en RGB
function ColorHSLToRGB($h, $s, $l)
{
    // Définition des valeurs rgb
    $r = $l;
    $g = $l;
    $b = $l;
    // Valeur HSL
    $v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
    if ($v > 0) {
        // Définition variables nécessaires
        $m = $sv = $sextant = $fract = $vsf = $mid1 = $mid2 = 0;

        $m = $l + $l - $v;
        $sv = ($v - $m) / $v;
        $h *= 6.0;
        $sextant = floor($h);
        $fract = $h - $sextant;
        $vsf = $v * $sv * $fract;
        $mid1 = $m + $vsf;
        $mid2 = $v - $vsf;

        switch ($sextant) {
            case 0:
                $r = $v;
                $g = $mid1;
                $b = $m;
                break;
            case 1:
                $r = $mid2;
                $g = $v;
                $b = $m;
                break;
            case 2:
                $r = $m;
                $g = $v;
                $b = $mid1;
                break;
            case 3:
                $r = $m;
                $g = $mid2;
                $b = $v;
                break;
            case 4:
                $r = $mid1;
                $g = $m;
                $b = $v;
                break;
            case 5:
                $r = $v;
                $g = $m;
                $b = $mid2;
                break;
        }
    }
    return array('r' => $r * 255.0, 'g' => $g * 255.0, 'b' => $b * 255.0);
}

define("PIXEL_SIZE", 20);
define("PIXEL_NB", 8);

function createPfp(string $userId): bool|null
{
    // Crée une image avec les dimensions données
    $im = imagecreate(PIXEL_NB * PIXEL_SIZE, PIXEL_SIZE * PIXEL_NB)
        or die("Cannot Initialize new GD image stream");

    // Définit une couleur RGB à partir d'une couleur HSL. On utilise HSL pour obtenir une couleur pastel,
    // que l'on décode ensuite en RGB pour l'allocation sur l'image
    $randomRGB = ColorHSLToRGB(random_int(0, 359) / 360, 1, 0.8);

    $bgColor = imagecolorallocate($im, 255, 255, 255);
    $rectColor = imagecolorallocate($im, $randomRGB['r'], $randomRGB['g'], $randomRGB['b']);

    $sd = random_int(0, 8);

    for ($y = 0; $y < PIXEL_NB; $y++) {
        for ($x = 0; $x < PIXEL_NB; $x++) {
            if (hexdec(substr($userId, $sd + abs(3.5 - $x) * 5 + $y, 1)) % 2) {
                imagefilledrectangle($im, $x * PIXEL_SIZE, $y * PIXEL_SIZE, ($x + 1) * PIXEL_SIZE - 1, ($y + 1) * PIXEL_SIZE - 1, $rectColor);
            }
        }
    }

    // Enregistrement de l'image sous format PNG
    imagepng($im, '../images/pfp/' . $userId . '.png');

    // Image non nécessaire ; on la détruit
    imagedestroy($im);

    // L'opération s'est terminée ; on renvoiei true
    return true;
}

function loadImagesFromFolder($path, $prefix)
{
    foreach (scandir($_SERVER['DOCUMENT_ROOT'] . $path) as $img) {
        $imgName = explode('.', $img)[0];
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $path . $img) && strlen($imgName) > 1)
            echo "<img src='$path/$img' id='" . $prefix . "_" . $imgName . "'>";
    }
}

function timeVal(string $time)
{
    $t = explode(':', $time);
    return intval($t[0] . $t[1] . $t[2]);
}

?>
