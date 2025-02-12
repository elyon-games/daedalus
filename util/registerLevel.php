<?php

require_once('common.php');
require_once('connection.php');

if(empty($_POST['level']) || empty($_POST['preview'])){
    echo 'error';
    die();
}

$level = json_decode($_POST['level'], true);
if(empty($level['id'])) $level['id'] = str_replace('.', '', uniqid('', true));

$preview = base64_decode(str_replace(' ', '+', str_replace('data:image/png;base64,', '', $_POST['preview'])));
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/images/preview/" . $level['id'] . '.png', $preview);

$stmt = $connection->prepare('DELETE FROM `level` WHERE creator_id = :userId AND id = :levelId');
$stmt->execute(['userId' => $userInfo['id'], 'levelId' => $level['id']]);

$stmt = $connection->prepare('INSERT INTO `level`(`id`, `creator_id`, `difficulty`, `objects`, `timestamp`, `name`) VALUES (:id, :creator_id, :difficulty, :objects, CURRENT_TIMESTAMP, :name)');
$stmt->execute(['id' => $level['id'], 'creator_id' => $userInfo['id'], 'difficulty' => $level['difficulty'], 'objects' => $level['objects'], 'name' => $level['name']]);

$jsonData = json_encode($level);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/data/levels/" . $level['id'] . '.json', $jsonData);

if(empty($userInfo['achievements']) || !in_array('6', explode(',', $userInfo['achievements']))) {
    $stmt = $connection->prepare("UPDATE player SET achievements = ? WHERE id = ?");
    $stmt->execute([($userInfo['achievements'] ? $userInfo['achievements'] . ',6' : '6'), $userInfo['id']]);
}

echo "/level/$level[id]/play";

?>