
<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <link rel="stylesheet" href="/css/wiki.css">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Wiki</title>
            <?php 
                include $_SERVER['DOCUMENT_ROOT'].'/util/common.php';
                include $_SERVER['DOCUMENT_ROOT'].'/util/connection.php'; 
                if (empty($userInfo))
                    header('location: ../index.php');
                    getPageHead("Wiki", "Wiki");
            ?>
    </head>
    <body>
        <div>
            <?php getPageHeader("Wiki", $userInfo); ?>
        </div>
        <div class="title-box">
            <p id="title">Wiki</p>
        </div>
        <div class="container">
            <div class="medium-box">
                <div class="small-box">
                    <button id="bouton-regles">Modes</button>
                </div>
                <div class="small-box">
                    <button id="bouton-pieges">Obstacles</button>
                </div>
                <div class="small-box">
                    <button id="bouton-objets">Objets</button>
                </div>
            </div>
            <div class="text-box">
                <div id="text">Welcome to the Neon Nexus wiki !</div>
            </div>  
        </div>
    <script>
        var regles = document.getElementById('bouton-regles');
        var pieges = document.getElementById('bouton-pieges');
        var objets = document.getElementById('bouton-objets');
        var text = document.getElementById('text');
            regles.addEventListener('click', function(){
                console.log("click");
                text.innerHTML = 
                "<div id='regles'><ul><h4>The following modes are available :</h4><br><br> <li>Adventure mode, in which the hero tries to escape from the place in which he's stuck.... A variant of this adventure mode is Sandbox mode, where all levels are unlocked, and you don't have to finish one to access the next. </li><br><li>The infinite mode, in which the hero must pass as many floors as possible without dying, and your score will be published in the general ranking at the end. There are no levels in this mode, as the floors are all randomly generated to infinity. </li><br><li>The creative mode allows you to create your own level and modify it at will, with the aim of publishing them for everyone to play.</li><br><li>Challenge mode: a mode that pushes even the strongest players to their limits, with floor sizes up to 4 times larger than you've ever seen before.</li></ul></div>";
                regles.classList.add("activated");
                pieges.classList.remove("activated");
                objets.classList.remove("activated");
            });

            pieges.addEventListener('click', function(){
                text.innerHTML = "<div id='pieges'><ul><h4>The available traps are the following :</h4><br><br><li><img src='/images/tiles/EMPTY.png'> The holes: they're impassable if you don't have any equipment, so keep looking if you can find a Jetpack or a board.</li><br><li><img src='/images/wiki/door.gif'> The doors : they're opened with an item, either a key or an access card, which you can find on the room of the level.</li><br><li><img src='/images/wiki/fence.gif'> Electric fences: they, too, won't let your character through, if you step on them, you'll die. Activate a lever on the floor to render them totally harmless, but they'll still block you so it still require an objet to cut through</li><br><li><img src='/images/wiki/tapis.gif'> Treadmills: If you step on one, you can't get off it until you meet a tile that isn't a treadmill.</li></ul></div>";
                regles.classList.remove("activated");
                pieges.classList.add("activated");
                objets.classList.remove("activated");
            });

            objets.addEventListener('click', function(){
                text.innerHTML = "<div id='objets'><ul><h4>The objects are the following :</h4><br><br><li><img src='/images/wiki/cartes.gif'> Access cards: available in 4 different colors, each card opens a door of the same color.</li><br><li><img src='/images/wiki/lightsaber.png'> LightSaber : allow you to cut the wire fence blocking part of the floor, allowing you to go through.</li><br><li><img src='/images/objects/plank.png'> Planks: A simple wooden plank that can be found everywhere. These are in limited supply and are single-use, so once you've put it down you can't get it back.</li><br><li><img src='images/objects/jetpack.png'> Jetpack: An advanced scientific equipment that allows you to cross holes several squares wide in a straight line. After 3 uses, the jetpack is no longer usable.</li><br><li><img src='/images/wiki/gene.gif'> Generator: generates a permanent current; when activated, it alternates the current over the entire stage; it is in a fixed position and cannot be moved.</li></ul></div>";
                regles.classList.remove("activated");
                pieges.classList.remove("activated");
                objets.classList.add("activated");
            });        
    </script>
    </body>
</html>
