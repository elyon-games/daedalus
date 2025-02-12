<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');

    if (empty($userInfo))
        header('location: /login');

    getPageHead('Level creation', 'play');

    $lvl;

    if (isset($_GET['id'])) {
        $stmt = $connection->prepare('SELECT level.id, level.creator_id, level.difficulty, level.timestamp, level.name, player.tag FROM `level` LEFT JOIN player ON player.id = creator_id WHERE level.id = ?');
        $stmt->execute([$_GET['id']]);
        $lvl = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (empty($lvl)) {
        $lvl = [
            'id' => '',
            'creator_id' => $userInfo['id'],
            'name' => 'New level',
            'tag' => $userInfo['tag'],
            'difficulty' => 1
        ];
    }

    if($lvl['creator_id'] != $userInfo['id'])
        header('location: /level/' . $lvl['id']);
    ?>
    <script src="/js/main.js"></script>
    <script src="/js/create.js"></script>
    <script>
        const levelName = '<?= $lvl['id'] ?>',
        levelMode = 'create',
        userTag = '<?= $userInfo['tag'] ?>';;
    </script>
</head>

<body>
    <?php getPageHeader('create', $userInfo) ?>
    <div id="popup">
        Popup
    </div>
    <main>
        <h1><?= $lvl['name'] ?></h1>
        <h2>Created by <a href="/profile/<?= $lvl['creator_id'] ?>"><?= $lvl['tag'] ?></a></h2>
        <div id="tooltip" class="glowingBox">
            <div>
                <h4>Test</h4>
                <hr>
                <p>Test object</p>
            </div>
        </div>
        <div id="creativeWrapper" class="container horizontal center">
            <div id="creativeMenu" class="glowingBox">
                <h3>Creative menu</h3>
                <hr>
                <h4 class="visible">Tiles</h4>
                <div id="tiles">
                    <?php loadImagesFromFolder("/images/tiles", "TILE"); ?>
                </div>
                <hr>
                <h4 class="visible">Objects</h4>
                <div id="objects">
                    <?php loadImagesFromFolder("/images/objects", "OBJECT"); ?>
                </div>
                <hr>
                <h4 class="visible">Room</h4>
                <div id="room">
                    <div id="roomDimensions">
                        <label>Dimensions : </label>
                        <input id="roomWidth" type="number" min="5" max="64">
                        X
                        <input id="roomHeight" type="number" min="5" max="64">
                        <button onclick="changeRoomDimensions()" class="apply">Apply</button>
                    </div>
                    <button onclick="deleteRoom()" class="cancel">Delete room</button>
                </div>
                <hr>
                <h4 class="visible">Floor</h4>
                <div id="floor">
                    <div id="floorDimensions">
                        <label>Dimensions : </label>
                        <input id="nbRoomsX" type="number" min="0" max="5">
                        X
                        <input id="nbRoomsY" type="number" min="0" max="5">
                        <button onclick="changeFloorDimensions()" class="apply">Apply</button>
                    </div>
                    <button onclick="deleteFloor()" class="cancel">Delete floor</button>
                    <h5>Rooms :</h5>
                    <div id="roomSelect">
                        <button id="addRoom" class="room">
                            +
                        </button>
                        <button class="room selected">
                            1
                        </button>
                        <button class="room">
                            2
                        </button>
                    </div>
                </div>
                <hr>
                <h4 class="visible">Level</h4>
                <div id="level">
                    <div id="levelInfo">
                        <label>Name</label>
                        <input type="text" id="levelName" value="<?= $lvl['name'] ?>">
                        <label>Difficulty</label>
                        <input id="levelDifficulty" type="number" min="1" max="3" value="<?= $lvl['difficulty'] ?>">

                    </div>
                    <h5>Floors :</h5>
                    <div id="floorSelect">
                        <button id="addFloor" class="floor">
                            +
                        </button>
                        <button class="floor selected">
                            1
                        </button>
                        <button class="floor">
                            2
                        </button>
                    </div>
                    <button id="submit" class="submit" onclick="checkLevel()">Publish level</button>
                </div>
                <hr>
                <h4 class="visible">Objectives</h4>
                <div id="objectives">
                    <button id="addObjective" onclick="addObjective()">+</button>
                </div>
            </div>
            <div id="canvasContainer" class="glowingBox">
                <canvas id="overlayCanvas"></canvas>
                <canvas id="effectsCanvas"></canvas>
                <canvas id="playerCanvas"></canvas>
                <canvas id="tileCanvas"></canvas>
            </div>
        </div>
    </main>
    <div class="sourcesContainer">
        <?php
        loadImagesFromFolder("/images/effects", "EFFECT");
        ?>
    </div>
</body>

</html>