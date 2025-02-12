<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/admin.css">
    <?php 
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');
        if (empty($userInfo)) {
            header('location: /login');
            exit();
        }
        getPageHead("Admin", "Admin");
    ?>
    <script src="/js/admin.js" defer></script>
</head>
<body>
    <?php getPageHeader("Admin", $userInfo); 
    $playerId = null;?>
    

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_id']) && !isset($_POST['ban_action'])) {
        $playerId = $_POST['player_id'];

        if ($playerId > 0) {
            global $connection;
            if ($connection) {
                try {
                    $sql = "DELETE FROM player WHERE id = :id";
                    $stmt = $connection->prepare($sql);
                    
                    if ($stmt->execute(['id' => $playerId])) {
                        $profileImagePath = $_SERVER['DOCUMENT_ROOT'] . "/images/pfp/" . $playerId . '.' . $userInfo['pfp_extension'];
                        unlink($profileImagePath);
                        echo "<div class='notification success'>Player account successfully deleted.</div>";
                        $playerId = null;
                        header('location: /admin');
                    } else {
                        echo "<div class='notification error'>Error deleting player account.</div>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='notification error'>Error: " . $e->getMessage() . "</div>";
                }
            } else {
                echo "<div class='notification error'>Database connection not available.</div>";
            }
        } else {
            echo "<div class='notification error'>Invalid player ID.</div>";
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_id']) && isset($_POST['ban_action'])) {
        $playerId = $_POST['player_id'];
        $banAction = intval($_POST['ban_action']);

        if ($playerId > 0) {
            global $connection;
            if ($connection) {
                try {
                    $sql = "UPDATE player SET banned = :banned WHERE id = :id";
                    $stmt = $connection->prepare($sql);

                    if ($stmt->execute(['banned' => $banAction, 'id' => $playerId])) {
                        echo "<div class='notification success'>Player account successfully " . ($banAction ? "banned" : "unbanned") . ".</div>";
                    } else {
                        echo "<div class='notification error'>Error updating player account.</div>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='notification error'>Error: " . $e->getMessage() . "</div>";
                }
            } else {
                echo "<div class='notification error'>Database connection not available.</div>";
            }
        } else {
            echo "<div class='notification error'>Invalid player ID.</div>";
        }
    }
    ?>

    <main>
        <p class="modes">Admin Panel</p>
        <div class="container">
            <p class="pacc">Players accounts</p>
            <div class="info">
                <?php
                    global $connection;
                    if ($connection) {
                        $sql = "SELECT * FROM player";
                        $stmt = $connection->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if (count($result) > 0) {
                            foreach ($result as $row) {
                                $infoId = 'info_' . $row['id'];
                                $isBanned = $row['banned'] == 1;
                                echo "<div class='pinfo " . ($isBanned ? 'infoban' : '') ."' onclick='toggleInfo(\"$infoId\")'>". htmlspecialchars($row['tag']) ."<div class='container_img'><img src='/images/ban.png' alt='ban_icon' class='ban_img' onclick='openBanModal(event, \"". htmlspecialchars($row['tag']) ."\", \"". htmlspecialchars($row['id']) ."\", $isBanned)'><img src='/images/supp.png' alt='supp_icon' class='supp_img' onclick='openSuppModal(event, \"". htmlspecialchars($row['tag']) ."\", \"". htmlspecialchars($row['id']) ."\")'></div></div>";
                                echo "<div class='dropdown' style='display:none;' data-info='$infoId'>";
                                    echo "<p>Id : " . htmlspecialchars($row['id']) . "</p>";
                                    echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
                                    echo "<p>Achievements: " . htmlspecialchars($row['achievements']) . "</p>";
                                    echo "<p>Adventure Level: " . htmlspecialchars($row['last_lvl']) . "</p>";
                                    echo "<p>Administrator: " . ($row['admin'] == 1 ? 'Yes' : 'No') . "</p>";
                                    echo "<p>Banned: " . ($isBanned ? 'Yes' : 'No') . "</p>";
                                echo "</div>";
                            }
                        } else {
                            echo "No players found.";
                        }
                    } else {
                        echo "Database connection not available.";
                    }
                ?>
            </div>
        </div>
    </main>

    <div id="banModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to <span id="banActionText"></span>:</p>
            <p><span id="banPlayerName"></span>?</p>
            <form action="" method="post">
                <input type="hidden" id="banPlayerId" name="player_id">
                <input type="hidden" id="banAction" name="ban_action">
                <button type="submit" class="Ban_btn" id="banConfirmButton"></button>
                <button type="button" onclick="closeModal('banModal')" class="Ban_cancel">Cancel</button>
            </form>
        </div>
    </div>

    <div id="suppModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to delete the account:</p>
            <p><span id="suppPlayerName"></span>?</p>
            <form action="" method="post">
                <input type="hidden" id="suppPlayerId" name="player_id">
                <button type="submit" class="Del_btn">Delete</button>
                <button type="button" onclick="closeModal('suppModal')" class="Del_cancel">Cancel</button>
            </form>
        </div>
    </div>
</body>
</html>
