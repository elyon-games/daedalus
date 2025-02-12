<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php getPageHead('Page not found', '404') ?>
</head>
<body>
    <?php getPageHeader('404', $userInfo) ?>
    <main>
    <h1>404</h1>
    <h2>The page you're searching for doesn't exist</h2>
    </main>
</body>
</html>