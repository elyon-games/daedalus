<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/common.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/util/connection.php');

global $connection;

if ($connection) {
    $sqlNbMouvements = "SELECT player_id, level_id, adventure_leaderboard.moves, player.tag
                        FROM adventure_leaderboard
                            LEFT JOIN player
                            ON adventure_leaderboard.player_id = player.id
                        ORDER BY adventure_leaderboard.moves
                        LIMIT 9;";
    $stmtNbMouvements = $connection->prepare($sqlNbMouvements);
    $stmtNbMouvements->execute();
    $minNbMouvements = $stmtNbMouvements->fetchAll(PDO::FETCH_ASSOC);

    $sqlTimeScore = "SELECT player_id, level_id, adventure_leaderboard.time, player.tag
                    FROM adventure_leaderboard
                        LEFT JOIN player
                        ON adventure_leaderboard.player_id = player.id
                    ORDER BY adventure_leaderboard.time
                    LIMIT 9;";
    $stmtTimeScore = $connection->prepare($sqlTimeScore);
    $stmtTimeScore->execute();
    $minTimeScore = $stmtTimeScore->fetchAll(PDO::FETCH_ASSOC);

    $biggestInfScore = "SELECT * FROM player ORDER BY max_inf DESC LIMIT 10";
    $stmtInfScore = $connection->prepare($biggestInfScore);
    $stmtInfScore->execute();
    $InfScore = $stmtInfScore->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "Erreur de connexion à la base de données.";
}

$levels = [];
foreach ($minNbMouvements as $row) {
    $level_id = $row['level_id'];
    if (!isset($levels[$level_id])) {
        $levels[$level_id] = [];
    }

    $levels[$level_id][] = $row;
}

$levelsTs = [];
foreach ($minTimeScore as $row) {
    $level_id = $row['level_id'];
    if (!isset($levelsTs[$level_id])) {
        $levelsTs[$level_id] = [];
    }
    $levelsTs[$level_id][] = $row;
}

$levelData = [
    'level1' => $levels[1] ?? null,
    'level2' => $levels[2] ?? null,
    'level3' => $levels[3] ?? null,
    'level4' => $levels[4] ?? null,
    'level5' => $levels[5] ?? null,
    'level6' => $levels[6] ?? null,
    'level7' => $levels[7] ?? null,
    'level8' => $levels[8] ?? null,
    'level9' => $levels[9] ?? null
];

$levelDataTs = [
    'level1' => $levelsTs[1] ?? null,
    'level2' => $levelsTs[2] ?? null,
    'level3' => $levelsTs[3] ?? null,
    'level4' => $levelsTs[4] ?? null,
    'level5' => $levelsTs[5] ?? null,
    'level6' => $levelsTs[6] ?? null,
    'level7' => $levelsTs[7] ?? null,
    'level8' => $levelsTs[8] ?? null,
    'level9' => $levelsTs[9] ?? null
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php getPageHead('Leaderboard', 'classement') ?>
</head>
<body>
    <?php getPageHeader('classement', $userInfo) ?>
    <p class="modes">Leaderboard</p>
    <div class="tableauClassement">
        <div class="title" onclick="toggleTableau()">Adventure Mode</div>
        <div class="levelsTitle">Levels</div>
        <div class="nbMovesTitle">Number of moves</div>
        <div class="timeScoreTitle">Time score</div>
        <div class="levels">
            <div class="levelTitle underlined" data-level="1">Level 1</div>
            <div class="levelTitle" data-level="2">Level 2</div>
            <div class="levelTitle" data-level="3">Level 3</div>
            <div class="levelTitle" data-level="4">Level 4</div>
            <div class="levelTitle" data-level="5">Level 5</div>
            <div class="levelTitle" data-level="6">Level 6</div>
            <div class="levelTitle" data-level="7">Level 7</div>
            <div class="levelTitle" data-level="8">Level 8</div>
            <div class="levelTitle" data-level="9">Level 9</div>
        </div>
        <div class="nbMoves">
        <?php
        for ($i = 1; $i <= 9; $i++) {
            $levelKey = 'level' . $i;
            $levelClass = $i === 1 ? 'lv' . $i : 'lv' . $i . ' hidden';
            echo "<div class='$levelClass'>";
            if (isset($levelData[$levelKey])) {
                $firstNine = array_slice($levelData[$levelKey], 0, 9);
                for ($j = 0; $j <= 8; $j++) {
                    if (isset($firstNine[$j])) {
                        echo '<div class="level-moves">' . htmlspecialchars($firstNine[$j]["tag"]) . " - " . htmlspecialchars($firstNine[$j]["moves"]) . " moves" . '</div>';
                    } else {
                        echo '<div class="level-moves"></div>';
                    }
                }
            } else {
                for ($j = 0; $j < 9; $j++) {
                    echo '<div class="level-moves"></div>';
                }
            }
            echo '</div>';
        }
        ?>
        </div>
        <div class="timeScore">
        <?php
        for ($i = 1; $i <= 9; $i++) {
            $levelKey = 'level' . $i;
            $levelClass = $i === 1 ? 'lvTs' . $i : 'lvTs' . $i . ' hidden';
            echo "<div class='$levelClass'>";
            if (isset($levelDataTs[$levelKey])) {
                $firstNine = array_slice($levelDataTs[$levelKey], 0, 9);
                for ($j = 0; $j <= 8; $j++) {
                    if (isset($firstNine[$j])) {
                        echo '<div class="level-ts">' . htmlspecialchars($firstNine[$j]["tag"]) . " - " . htmlspecialchars($firstNine[$j]["time"]) . '</div>';
                    } else {
                        echo '<div class="level-ts"></div>';
                    }
                }
            } else {
                for ($j = 0; $j < 9; $j++) {
                    echo '<div class="level-ts"></div>';
                }
            }
            echo '</div>';
        }
        ?>
        </div>
    </div>

    <div class="tableauClassementInfini">
        <div class="title" onclick="toggleTableau()">Infinite Mode</div>
        <div class="level_max_title">Level max</div>
        <div class="level_max">
            <?php
            foreach ($InfScore as $row) {
                echo '<div class="max_inf">' . htmlspecialchars($row['tag']) . " - Level " . htmlspecialchars($row['max_inf']) . '</div>';
            }
            ?>
        </div>
    </div>
</body>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const levelTitles = document.querySelectorAll('.levelTitle');
    const levels = document.querySelectorAll('[class^="lv"]');
    const levelsTs = document.querySelectorAll('[class^="lvTs"]');

    levelTitles.forEach(title => {
        title.addEventListener('click', function() {

            levels.forEach(level => {
                level.classList.add('hidden');
            });
            levelsTs.forEach(levelTs => {
                levelTs.classList.add('hidden');
            });

            levelTitles.forEach(title => {
                title.classList.remove('underlined');
            });

            const levelToShow = document.querySelector(`.lv${this.dataset.level}`);
            if (levelToShow) {
                levelToShow.classList.remove('hidden');
            }

            const levelToShowTs = document.querySelector(`.lvTs${this.dataset.level}`);
            if (levelToShowTs) {
                levelToShowTs.classList.remove('hidden');
            }

            this.classList.add('underlined');
        });
    });
});
</script>
</html>