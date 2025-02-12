<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <title>Accueil</title>

    <?php
    getPageHead("index", "index"); ?>
</head>

<body>
    <div id="banner">Neon Nexus</div>
    <div class="gros-container">
        <div class="container">
            <div class="glowingBox">
                After signing an agreement to become a test subject for the pharmaceutical company “Live.inc”, you
                discover all the inhumane and unethical things they do in this place. Your one and only goal: find an
                exit and take down this evil corporation in the process. <br>Welcome to Neon Nexus

            </div>
        </div>
        <div class="container2">
            <a href="/menu" class="glowbtn">Play</a>
        </div>
</body>
<script>
    window.addEventListener("DOMContentLoaded", (event) => {
        animate_text();
    });
    function animate_text() {
        let delay = 30,
            delay_start = 0,
            contents,
            letters;

        document.querySelectorAll(".glowingBox").forEach(function (elem) {
            contents = elem.textContent.trim();
            elem.textContent = "";
            letters = contents.split("");
            elem.style.visibility = 'visible';

            letters.forEach(function (letter, index_1) {
                setTimeout(function () {

                    elem.textContent += letter;
                }, delay_start + delay * index_1);
            });
            delay_start += delay * letters.length;
        })
    };</script>

</html>