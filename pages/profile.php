<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="/css/profile.css">
    <?php 
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');
        if (empty($userInfo)) {
            header('location: /login');
            exit(); // Ensure the script stops executing after the redirect
        }

        $user;

        if(empty($_GET['id'])) {
            $user = $userInfo;
        } else {
            $stmt = $connection->prepare("SELECT * FROM player WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $user = $stmt->fetch();
            if(empty($user['id'])) {
                require_once('404.php');
                die();
            }
        }
        getPageHead("Profile");
    ?>
</head>
<body>
    <?php getPageHeader("Profile" , $userInfo); ?>
    <div class="container">
        <div class="profile">
            <?php 
                echo "<img src='/images/pfp/" . htmlspecialchars($user['id']) . '.' . htmlspecialchars($user['pfp_extension']) . "' alt='Profile Picture' class='pfp-image'>";
                echo "<p class='pfp-user'>" . htmlspecialchars($user['tag']) . "</p>";
            ?>
        </div>
        <div class="info">
            <?php
                // Show the player's levels
                global $connection;
                if ($connection) {
                    $playerId = $user['id'];
                    // Fetch the player information
                    $sql = "SELECT * FROM player WHERE id = :playerId";
                    $stmt = $connection->prepare($sql);
                    $stmt->bindParam(':playerId', $playerId, PDO::PARAM_INT);
                    $stmt->execute();
                    $player = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $inf_record = $player['max_inf'];
                    $inf_level = $player['current_inf'];
                    $adv_record = $player['last_lvl'];

                    echo "<table class='tab_info'>";
                    echo "<tr><td>Infinite record : $inf_record</td><td>Current Infinite level : $inf_level</td><td>Adventure record : $adv_record</td></tr>";
                    echo "</table>";
                }

                // Show the player's achievements
                if ($player && !empty($player['achievements'])) {
                    echo "<div class='achievements'>";
                    echo "<table class='tab_achievement'>";
                    echo "<tr class='achivement_title'><th>Achievements</th><th>Description</th></tr>";

                    $achievementIds = explode(',', $player['achievements']);
                    foreach ($achievementIds as $achievementId) {
                        // Fetch each achievement by ID
                        $achievementSql = "SELECT * FROM achievement WHERE id = :achievementId";
                        $achievementStmt = $connection->prepare($achievementSql);
                        $achievementStmt->bindParam(':achievementId', $achievementId, PDO::PARAM_INT);
                        $achievementStmt->execute();
                        $achievement = $achievementStmt->fetch(PDO::FETCH_ASSOC);

                        if ($achievement) {
                            $achievementName = htmlspecialchars($achievement['name']);
                            $achievementDescription = htmlspecialchars($achievement['description']);
                            echo "<tr class='achievement_row'><td>$achievementName</td><td>$achievementDescription</td></tr>";
                        }
                    }
                    echo "</table>"; // End of table
                    echo "</div>"; // End of achievements div
                } else {
                    echo "<p class='center'>No achievements found.</p>";
                }
            ?>
        </div>
        <table id="levels">
            <?php
            $stmt = $connection->prepare("SELECT * FROM level WHERE creator_id = ?");
            $valid = $stmt->execute([$user['id']]);
            $lvls = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(isset($lvls[0]) && $valid){
            ?>
            <tr class="level">
                    <th>Level name</th>
                    <th>Objects</th>
                    <th>Difficulty</th>
            </tr>
            <?php
                foreach($lvls as $level) {
            ?>
                <tr class="level">
                    <td><a href="/levels/<?= $level['id'] ?>"><?= $level['name'] ?></a></td>
                    <td><?php
                        foreach(explode(',', $level['objects']) as $obj) if($obj != '') echo "<img src='/images/objects/$obj.png'>";
                    ?></td>
                    <td><?= $level['difficulty'] ?></td>
            </tr>
            <?php }} else echo "<p class='center'>No levels created by this player.<p>" ?>
        </table>
    </div>
</body>
</html>
