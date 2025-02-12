<!DOCTYPE html>
<html lang="en">
<head>
<?php 
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');

    if (empty($userInfo))
        header('location: /login');
    if (empty($_GET['id'])){
        require_once('404.php');
        die();
    }


    $query =   "SELECT  level.id,
                        level.name,
                        level.creator_id,
                        level.validated,
                        level.difficulty,
                        level.objects,
                        level.timestamp,
                        player.tag
                FROM `level`
                LEFT JOIN `player`
                    ON player.id = creator_id
                WHERE (validated > 0 
                    OR level.creator_id = ?)
                    AND level.id = ?";

    $params = [$userInfo['id'], $_GET['id']];

    $stmt = $connection->prepare($query);
    $stmt->execute($params);
    $level = $stmt->fetch(PDO::FETCH_ASSOC);

    if(empty($level['id'])){
        require_once('404.php');
        die();
    }

    getPageHead('Level - ' . $level['name'], 'levels');
    ?>
</head>
<body>
    <?php getPageHeader('Level - ' . $level['name'], $userInfo); ?>
    <main class="container vertical center">
        <h1><?= $level['name'] ?></h1>
        <h2>Made by <a href="/profile/<?= $level['creator_id'] ?>"><?= $level['tag'] ?></a></h2>
        <div id="level">
            <img src="/images/preview/<?= $level['id'] ?>.png">
            <div>
                <h3>Name : <span><?= $level['name'] ?></span></h3>
                <h3>Creator : <a href="/profile/<?= $level['creator_id'] ?>"><?= $level['tag'] ?></a></h3>
                <h3>Difficulty : <span><?= $level['difficulty'] ?></span></h3>
                <h3>Last edited on : <span><?= $level['timestamp'] ?></span></h3>
                <h3>Objects :</h3>
                <span class="objects">
                    <?php
                        foreach(explode(',', $level['objects']) as $obj) if($obj != '') echo "<img src='/images/objects/$obj.png'>";
                    ?>
                </span>
                <?php if($level['creator_id'] == $userInfo['id']){ ?>
                    <h3>Published? : <span class="<?= $level['validated'] ? 'valid"> Yes' : 'invalid"> No' ?></span></h3>
                <?php } ?>
                <div class="container horizontal center">
                    <a class="play" href="/levels/<?= $level['id'] ?>/play">Play !</a>
                    <?php if($userInfo['id'] == $level['creator_id']) { ?>
                        <a class="play" href='/levels/<?= $level['id'] ?>/edit'>Edit</a>
                        <a class="delete" href='/levels/<?= $level['id'] ?>/delete'>Delete</a>
                    <?php }?>
                </div>
            </div>
        </div>
        <hr>
        <h2>Leaderboard</h2>
        <?php
            $stmt = $connection->prepare("SELECT leaderboard.*, player.tag FROM leaderboard
            JOIN player
                ON player.id = leaderboard.player_id
            WHERE level_id = ?
            ORDER BY leaderboard.moves, leaderboard.time");
            $stmt->execute([$level['id']]);
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($records) > 0) {
        ?>
        <table id="leaderboard">
            <thead>
                <th>Player</th>
                <th>Number of moves</th>
                <th>Time taken</th>
            </thead>
            <tbody>
                <?php
                foreach($records as $row){ ?>
                    <tr>
                        <td><a href="/profile/<?= $row['player_id'] ?>"><?= $row['tag'] ?></a></td>
                        <td><?= $row['moves'] ?></td>
                        <td><?= $row['time'] ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
            <h4>No records for this level yet</h4>
        <?php } ?>
    </main>
</body>
</html>