<?php

require_once ('common.php');
require_once ('connection.php');

if (empty($userInfo['id'])) {
    echo 'Connection problem';
    die();
}

if (
    empty($_POST['mode'])
    || ($_POST['mode'] == 'level' && (empty($_POST['moves']) || empty($_POST['time'])) && !isset($_POST['level_id']))
    || ($_POST['mode'] == 'infinite' && !isset($_POST['floors']))
    || ($_POST['mode'] == 'adventure' && (empty($_POST['moves']) || empty($_POST['time'])) && !isset($_POST['level_id']))
    || ($_POST['mode'] != 'adventure' && $_POST['mode'] != 'level' && $_POST['mode'] != 'infinite')
) {
    echo 'error';
    print_r($_POST);
    die();
}

if ($_POST['mode'] == 'level') {
    $stmt = $connection->prepare("SELECT * FROM leaderboard WHERE level_id = ? AND player_id = ?");
    $stmt->execute([$_POST['level_id'], $userInfo['id']]);
    $row = $stmt->fetch();

    if (empty($row) || $row['moves'] > $_POST['moves'] || ($row['moves'] == $_POST['moves'] && timeVal($row['time']) > timeVal($_POST['time']))) {

        $stmt = $connection->prepare("DELETE FROM leaderboard WHERE level_id = ? AND player_id = ?");
        $stmt->execute([$_POST['level_id'], $userInfo['id']]);

        $formattedTime = substr($_POST['time'], 0, 8); 
        $stmt = $connection->prepare("INSERT INTO leaderboard (player_id, level_id, moves, time) VALUES (?, ?, ?, STR_TO_DATE(?, '%H:%i:%s'))");
        $stmt->execute([$userInfo['id'], $_POST['level_id'], $_POST['moves'], $formattedTime]);

        $stmt = $connection->prepare("UPDATE level SET validated = 1 WHERE id = ? AND creator_id = ?");
        $stmt->execute([$_POST['level_id'], $userInfo['id']]);
    }
} else if ($_POST['mode'] == 'adventure' && empty($_POST['sandbox'])) {
    $achievements = $userInfo['achievements'] ?? '';
    $levelId = max([intval($_POST['level_id']), $userInfo['last_lvl']]);
    if ($levelId >= 1 && !in_array('7', explode(',', $achievements)))
        $achievements .= ',7';
    if ($levelId >= 10 && !in_array('1', explode(',', $achievements)))
        $achievements .= ',1';

    $stmt = $connection->prepare("SELECT * FROM leaderboard WHERE level_id = ? AND player_id = ?");
    $stmt->execute([$_POST['level_id'], $userInfo['id']]);
    $row = $stmt->fetch();

    if (empty($row) || $row['moves'] > $_POST['moves'] || ($row['moves'] == $_POST['moves'] && timeVal($row['time']) > timeVal($_POST['time']))) {

        $stmt = $connection->prepare("DELETE FROM adventure_leaderboard WHERE level_id = ? AND player_id = ?");
        $stmt->execute([$_POST['level_id'], $userInfo['id']]);

        $formattedTime = substr($_POST['time'], 0, 8); 
        $stmt = $connection->prepare("INSERT INTO adventure_leaderboard (player_id, level_id, moves, time) VALUES (?, ?, ?, STR_TO_DATE(?, '%H:%i:%s'))");
        $stmt->execute([$userInfo['id'], $_POST['level_id'], $_POST['moves'], $formattedTime]);
    }

    $stmt = $connection->prepare("UPDATE player SET last_lvl = ?, achievements = ?, tuto_finished = 1 WHERE id = ?");
    $stmt->execute([$_POST['level_id'], $userInfo['id'], $_POST['level_id']]);
} else if ($_POST['mode'] == 'infinite') {
    $achievements = $userInfo['achievements'] ?? '';
    $floors = intval($_POST['floors']);
    if ($floors >= 25 && !in_array('2', explode(',', $achievements)))
        $achievements .= ',2';
    if ($floors >= 50 && !in_array('3', explode(',', $achievements)))
        $achievements .= ',3';
    if ($floors >= 75 && !in_array('4', explode(',', $achievements)))
        $achievements .= ',4';
    if ($floors >= 100 && !in_array('5', explode(',', $achievements)))
        $achievements .= ',5';

    if($achievements[0] == ',') ltrim($achievements, $achievements[0]);

    $stmt = $connection->prepare("UPDATE player SET current_inf = ?, achievements = ? WHERE id = ?");
    $stmt->execute([(empty($_POST['end']) ? $floors : 0), $achievements, $userInfo['id']]);

    $stmt = $connection->prepare("UPDATE player SET max_inf = ? WHERE id = ? AND player.max_inf < ?");
    $stmt->execute([$floors, $userInfo['id'], $floors]);
}

echo "ok";