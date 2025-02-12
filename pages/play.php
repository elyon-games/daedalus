<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');

if (empty($userInfo))
    header('location: /login');

if($_GET['mode'] == 'infinite') $_GET['id'] = 'infinite';

if (empty($_GET['mode']) || empty($_GET['id'])) {
    require_once ('404.php');
    echo 'feur';
    die();
}
$query = 'SELECT level.id, level.creator_id, level.difficulty, level.timestamp, level.name, player.tag FROM `level` LEFT JOIN player ON player.id = creator_id WHERE level.id = ?';
if($_GET['mode'] == 'adventure') $query = "SELECT * FROM adventure WHERE adventure.id = ?";

$stmt = $connection->prepare($query);
$stmt->execute([$_GET['id']]);
$lvl = $stmt->fetch(PDO::FETCH_ASSOC);

if($_GET['mode'] == 'infinite') $lvl = ['name' => 'Infinite Mode'];

if (empty($lvl)) {
    require_once ('404.php');
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php getPageHead('Play !', 'play') ?>
    <script src="/js/main.js"></script>
    <script src="/js/play.js"></script>
    <script>
        const levelName = '<?= $_GET['id'] ?>',
            levelMode = '<?= $_GET['mode'] ?>',
            userTag = '<?= $userInfo['tag'] ?>';
    </script>
</head>

<body class="container vertical">
    <?php getPageHeader('Play !', $userInfo) ?>
    <div id="popup">
        Popup
    </div>
    <main>
        <h1><?= $lvl['name'] ?></h1>
        <?php
        if ($_GET['mode'] == 'level')
            echo '<h2>Created by <a href="/profile/' . $lvl['creator_id'] . '">' . $lvl['tag'] . '</a></h2>'
                ?>
            <div id="fullscreenWrapper" class="container horizontal center">
                <div id="tooltip" class="glowingBox">
                    <div>
                        <h4>Test</h4>
                        <hr>
                        <p>Test object</p>
                    </div>
                </div>
                <div id="infoContainer" class="glowingBox">
                    <h3>Menu</h3>
                    <h5 id="jetpackUses">Jetpack charges : 0</h5>
                    <h5 id="lives"></h5>
                    <?php
                    if($_GET['mode'] == 'infinite') { ?>
                        <h5 id="maxMoves"></h5>
                        <h5 id="moves">Current moves : 0</h5>
                        <button class='submit' onclick='saveInfiniteState()'>Save</button>
                    <?php } ?>
                    <hr>
                    <h4>Inventory</h4>
                    <div id="inventory">
                    </div>
                    <hr>
                    <h4>Objectives</h4>
                    <ul id="objectives">

                    </ul>
                </div>
                <div id="canvasContainer" class="glowingBox">
                    <button id="guiBtn" onclick="document.getElementById('gui').classList.add('visible')">
                        <svg width="50" height="50">
                            <line x1="5" y1="25%" x2="45" y2="25%" />
                            <line x1="5" y1="50%" x2="45" y2="50%" />
                            <line x1="5" y1="75%" x2="45" y2="75%" />
                        </svg>
                    </button>
                    <div id="gui">
                        <button class="glowingBox"
                            onclick="this.parentElement.classList.remove('visible')">Continue</button>
                        <button class="glowingBox" onclick="p.die()">Checkpoint</button>
                        <button class="glowingBox" onclick="fullscreen()">Fullscreen</button>
                        <button class="glowingBox" onclick="window.location.reload()">Restart</button>
                        <div id="audioRange"><input oninput="changeVolume(this.value)" type="range" min="0" max="100" value="100"><span>100</span><img src="">
                        </div>
                    </div>
                    <canvas id="overlayCanvas"></canvas>
                    <canvas id="effectsCanvas"></canvas>
                    <canvas id="playerCanvas"></canvas>
                    <canvas id="tileCanvas"></canvas>
                </div>
            </div>
            <button onclick="document.getElementById('fullscreenWrapper').requestFullscreen()">Fullscreen</button>
        </main>
        <div class="sourcesContainer">
            <?php
        loadImagesFromFolder("/images/tiles", "TILE");
        loadImagesFromFolder("/images/effects", "EFFECT");
        loadImagesFromFolder("/images/objects", "OBJECT");
        ?>
        <img id="PLAYER" src="/images/PLAYER.png">
    </div>
</body>

</html>