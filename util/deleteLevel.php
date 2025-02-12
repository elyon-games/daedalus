<?php

require_once('common.php');
require_once('connection.php');

if(empty($_GET['id'])){
    require($_SERVER['DOCUMENT_ROOT'] . '/pages/404.php');
    die();
}

$stmt = $connection->prepare("DELETE FROM `level` WHERE id = ? AND creator_id = ?");
$stmt->execute([$_GET['id'], $userInfo['id']]);

unlink($_SERVER['DOCUMENT_ROOT'] . '/data/levels/' . $_GET['id'] . '.json');
unlink($_SERVER['DOCUMENT_ROOT'] . '/images/preview/' . $_GET['id'] . '.png');

header('location: /levels');