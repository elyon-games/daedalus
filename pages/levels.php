<!DOCTYPE html>
<html lang="en">
<head>
<?php 
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');

    if (empty($userInfo))
        header('location: /login');

    getPageHead('Levels', 'levels');

    $query =   "SELECT  level.id,
                        level.name,
                        level.creator_id,
                        level.validated,
                        level.objects,
                        player.tag
                FROM `level`
                LEFT JOIN `player`
                    ON player.id = creator_id
                WHERE validated > 0 OR creator_id = ?";

    $params = [$userInfo['id']];

    $stmt = $connection->prepare($query);
    $stmt->execute($params);
    $levels = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
</head>
<body>
    <?php getPageHeader('Levels', $userInfo); ?>
    <main class="container vertical center">
        <h1>Levels list</h1>
        <a id="create" class="glowingBox" href="/levels/create">
            <svg width="24" height="24" stroke-linecap="round" stroke-width="4" stroke="gray">
                <line x1="12" y1="2" x2="12" y2="22"/>
                <line x1="2" y1="12" x2="22" y2="12"/>
            </svg>
            Create a level
        </a>
        <div id="levelsContainer">
            <?php
            foreach($levels as $lvl) {
            ?>
            <div class="level">
                <h3><a href="/levels/<?= $lvl['id'] ?>"><?= $lvl['name'] ?></a></h3>
                <h4>By <a href="/profile/<?= $lvl['creator_id'] ?>"><?= $lvl['tag'] ?></a></h4>
                <img src="/images/preview/<?= $lvl['id'] ?>.png">
                <h5>Objects :</h5>
                <span class="objects">
                    <?php
                        foreach(explode(',', $lvl['objects']) as $obj) if($obj != '') echo "<img src='/images/objects/$obj.png'>";
                    ?>
                </span>
            </div>
            <?php } ?>
        </div>
    </main>
</body>
</html>