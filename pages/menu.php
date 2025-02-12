<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/menu.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');
        if (empty($userInfo))
            header('location: /login');
            getPageHead("Menu" , "Menu");
    ?>
    <title>Menu</title>
</head>
<body>
    
    <div id="banner">
        <p class="gamename">Neon Nexus</p>
        <div class="pfp">
        <div class="usertag"><?php echo $userInfo['tag']?></div>
            <div>
                <span id="img-rond" class="rond nav-opened" style="opacity: 0;">
                    <?php echo '
                        <a href="profile"><img src="/images/profil.png" alt="Profile Icon"> Profile</a>
                        <a href="settings"><img src="/images/settings.png" alt="Settings Icon"> Settings</a>';
                        if($userInfo['admin'] == 1):
                            echo '<a href="admin"><img src="/images/admin.png" alt="Admin Icon">Admin</a>';
                        endif;
                            echo '<a href="disconnect"><img src="/images/logout.png" alt="Logout Icon">Log Out</a>';
                    ?>
                </span>
                <img src="/images/pfp/<?php echo $userInfo['id']?>.<?php echo $userInfo['pfp_extension']?>" alt="user" class="profile-image">
            </div>
    </div>
    </div>
    
    <p class="modes">Game Modes</p>
    <div class="container">
        <div class="dropdown">
            <a href="adventure" class="dropbtn adventu">Adventure</a>
            <img src="/images/adventure.png" alt="adventure Mode" class="img">
            <div class="dropdown-content adve">
                <div>Go on an adventure and beat our 9 levels with its 3 different difficulties</div>
            </div>
        </div>

        <div class="dropdown">
            <a href="levels" class="dropbtn creativ">Creative Mode</a>
            <img src="/images/creative.png" alt="creative Mode" class="img">
            <div class="dropdown-content crea">
                <div>Get creative and bring your own levels to life from scratch</div>
            </div>
        </div>
        
        <div class="dropdown">
            <a href="infinite" class="dropbtn infini">Infinite</a>
            <img src="/images/infini.png" alt="Infinite Mode" class="img">
            <div class="dropdown-content inf">
                <div>Set a new record or try to beat other people's scores in this infinite mode</div>
            </div>
        </div>
    </div>
    <div class="container2">
        <a href="classement" class="dropbtn">Leaderboard</a>
        <a href="wiki" class="dropbtn">Wiki</a>
    </div>

</body>
</html>
