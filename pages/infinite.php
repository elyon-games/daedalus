<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');
        if (empty($userInfo)) {
            header('location: /login');
            exit;
        }
        getPageHead("Infinite", "infinite");
    ?>
    <script src="/js/infinite.js"></script>
    <title>Infinite</title>
</head>
<body>
    <?php getPageHeader("Infinite", $userInfo); ?>
    
    <?php 
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset'])) {
        $playerId = $userInfo['id'];

        if ($playerId > 0) {
            global $connection;
            if ($connection) {
                $query = "UPDATE player SET current_inf = 0 WHERE id = ?";
                $stmt = $connection->prepare($query);
                $result = $stmt->execute([$playerId]);
                if ($result) {
                    $userInfo['current_inf'] = 0;
                    echo "<div class='notification error'>Level have been reset.</div>";
                    $playerId = null;
                    header('location: /infinite');
                } else {
                    echo "<div class='notification error'>Failed to reset level.</div>";
                }
            }
        }
    }
    ?>
    <p class="title">Infinite Mode</p>
    <div class="container">
        <div class="scores">
            <p class="curr_lvl">Current level : <?php echo $userInfo['current_inf']; ?></p>
            <p class="max_lvl">Max Level : <?php echo $userInfo['max_inf']; ?></p>
        </div>
        <a href="/infinite/play" class="play">Play</a>
        <button onclick="showModal()" class="resetbtn">Reset</button>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <p class="txtconf">Are you sure you want to reset your current level?</p>
            <form method="POST" style="display:inline;">
                <input type="hidden" name="reset" value="true">
                <button type="submit" class="confirmbtn">Confirm</button>
            </form>
            <button class="cancelbtn" onclick="closeModal()">Cancel</button>
        </div>
    </div>
</body>
</html>
