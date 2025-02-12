<?php

require_once ('common.php');
require_once ('connection.php');

if(empty($userInfo)) header('location: /login');

$stmt = $connection->prepare("UPDATE player SET tuto_finished = 1 WHERE player.id = ?");
$stmt->execute([$userInfo['id']]);

header('location: /menu');