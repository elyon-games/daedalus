<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adventure Mode</title>
    <?php 
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');
        if (empty($userInfo))
            header('location: ../index.php');
        getPageHead("Adventure");
    ?>
    <link rel="stylesheet" href="/css/adventure.css">
</head>

<body>
    <?php getPageHeader("Adventure", $userInfo); ?>
    <p class="modes">Adventure Mode</p>
    <label class="switch">
        <input type="checkbox" class="checkbox" id="sandboxToggle">SandBox
        <div class="slider"></div>
    </label>
    <div class="container">
        <div class="easy">
            <p class="backeasy">Easy</p>
            <div class="lvl">
                <a href="/adventure/1/play" data-level="1">Level 1</a>
                <a href="/adventure/2/play" data-level="2">Level 2</a>
                <a href="/adventure/3/play" data-level="3">Level 3</a>
            </div>
        </div>
        <div class="medium">
            <p class="backmed">Medium</p>
            <div class="lvl">
                <a href="/adventure/4/play" data-level="4">Level 4</a>
                <a href="/adventure/5/play" data-level="5">Level 5</a>
                <a href="/adventure/6/play" data-level="6">Level 6</a>
            </div>
        </div>
        <div class="hard">
            <p class="backhard">Hard</p>
            <div class="lvl">
                <a href="/adventure/7/play" data-level="7">Level 7</a>
                <a href="/adventure/8/play" data-level="8">Level 8</a>
                <a href="/adventure/9/play" data-level="9">Level 9</a>
            </div>
        </div>
    </div>
    <a href="/adventure/play?id=10" class="diff" data-level="10">Challenge</a>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sandboxToggle = document.getElementById("sandboxToggle");
            const links = document.querySelectorAll(".lvl a, .diff");
            const lastLvl = <?php echo $userInfo['last_lvl']; ?>;

            function updateLinks() {
                if (sandboxToggle.checked) {
                    links.forEach(link => {
                        link.style.color = "";
                        link.style.pointerEvents = "";
                        link.style.backgroundColor = "";
                    });
                } else {
                    links.forEach(link => {
                        const level = parseInt(link.getAttribute("data-level"));
                        if (level > lastLvl + 1) {
                            link.style.color = "grey";
                            link.style.pointerEvents = "none";
                            link.style.backgroundColor = "#f0f0f0"; // Grey out the background
                        } else {
                            link.style.color = "";
                            link.style.pointerEvents = "";
                            link.style.backgroundColor = "";
                        }
                    });
                }
            }

            sandboxToggle.addEventListener("change", updateLinks);
            updateLinks();
        });
    </script>
</body>
</html>
