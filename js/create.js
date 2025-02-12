Parameters.CREATIVE = true;

const CreateTooltips = {
    EMPTY: {
        h: 'Empty',
        p: 'Acts as a wall, but can be transformed into a walking tile with a plank'
    },
    GROUND: {
        h: 'Ground',
        p: 'Basic tile on which the player can walk'
    },
    WALL: {
        h: 'Wall',
        p: 'Acts as an obstacle. Players cannot pass through this tile'
    },
    ENTRANCE: {
        h: 'Entrance',
        p: 'Acts as a passageway between rooms.<br>Must be placed on an edge of the room.<br>Cannot be placed in corners<br>'
    },
    STAIRS_UP: {
        h: 'Stairs - Up',
        p: 'Allows access to the next floor'
    },
    STAIRS_DOWN: {
        h: 'Stairs - Down',
        p: 'Denies access to the previous floor'
    },

    FENCE: {
        h: 'Fence - Basic',
        p: 'Acts like a wall. Can be cut with a lightsaber',
    },
    FENCE_ELECTRIFIED: {
        h: 'Fence - Electrified',
        p: 'Acts like a wall. Cannot be cut while being powered'
    },
    FENCE_CUT: {
        h: 'Fence - Cut',
        p: 'A fence which have been already cut'
    },
    DOOR_RED_CLOSED: {
        h: 'Door - Red - Closed',
        p: 'A red door that can be opened with a red key'
    },
    DOOR_RED_OPENED: {
        h: 'Door - Red - Opened',
        p: 'A red door that has already been opened'
    },
    ELECTRIC_ON: {
        h: 'Electric - ON',
        p: 'A powered electric tile, killing the player when they step on it'
    },
    ELECTRIC_OFF: {
        h: 'Electric - OFF',
        p: 'An unpowered electric tile, completely safe'
    },

    CONVEYOR_BELT_NORTH: {
        h: 'Conveyor belt - North',
        p: 'Pushes the player to the north. Player unable to move while being pushed'
    },
    CONVEYOR_BELT_EAST: {
        h: 'Conveyor belt - East',
        p: 'Pushes the player to the east. Player unable to move while being pushed'
    },
    CONVEYOR_BELT_SOUTH: {
        h: 'Conveyor belt - South',
        p: 'Pushes the player to the south. Player unable to move while being pushed'
    },
    CONVEYOR_BELT_WEST: {
        h: 'Conveyor belt - West',
        p: 'Pushes the player to the west. Player unable to move while being pushed'
    },

    LEVEL_START: {
        h: 'Start',
        p: 'Starting point of the level. The player will spawn here. Must be placed in the first floor of the level<br><i>The beginning of a long journey...(no)</i>'
    },
    LEVEL_END: {
        h: 'End',
        p: 'Ending point of the level. The player has to get here. Must be placed in the last floor of the level<br><i>Maybe the true friends were the treasure we met along the way</i>'
    },

    GENERATOR: {
        h: 'Power generator',
        p: 'Inverts electrical current : Powered tiles will become unpowered, and inversely<br><i>Infinite power !</i>'
    },

    DOOR_GREEN_CLOSED: {
        h: 'Door - Green - Closed',
        p: 'A green door that can be opened with a green key'
    },
    DOOR_GREEN_OPENED: {
        h: 'Door - Green - Opened',
        p: 'A green door that has already been opened'
    },
    DOOR_YELLOW_CLOSED: {
        h: 'Door - Yellow - Closed',
        p: 'A yellow door that can be opened with a yellow key'
    },
    DOOR_YELLOW_OPENED: {
        h: 'Door - Yellow - Opened',
        p: 'A yellow door that has already been opened'
    },
    DOOR_BLUE_CLOSED: {
        h: 'Door - Blue - Closed',
        p: 'A blue door that can be opened with a blue key'
    },
    DOOR_BLUE_OPENED: {
        h: 'Door - Blue - Opened',
        p: 'A blue door that has already been opened'
    }
}

let jsonObject;

async function start() {
    if (levelName != '') {
        const text = await fetch(`/data/levels/${levelName}.json`).then(r => r.text());
        level = new Level(JSON.parse(text), true);
    } else level = new Level(TemplateLevel, true);

    if(!level.id) level.getCurrentFloor().getCurrentRoom().setTile(0, 0, Tiles.LEVEL_START);
    level.setCurrentFloor(0);
    updateFloors();
    updateRooms();

    document.getElementById('TILE_GROUND').classList.add('selected');

    document.getElementById('tileCanvas').addEventListener('click', e => {
        e.preventDefault();
        var rect = e.target.getBoundingClientRect(),
            r = level.getCurrentFloor().getCurrentRoom();
        var x = Math.floor(r.width * (e.clientX - rect.left) / e.target.clientWidth); //x position within the element.
        var y = Math.floor((r.height + 1) * (e.clientY - rect.top) / e.target.clientHeight) - 1;  //y position within the element.
        r.getTile(x, y)?.destroy();
        r.setTile(x, y, document.querySelector('#tiles > .selected, #objects > .selected').id.match(/(?<=.+_).+/));
        r.draw();
        return false;
    })

    document.querySelectorAll('#creativeMenu > div > img').forEach((i) => {
        const t = CreateTooltips[i.id.match(/(?<=.+_).+/)] ?? Tooltips[i.id.match(/(?<=.+_).+/)];
        i.setAttribute('name', t?.h);
        i.setAttribute('description', t?.p);

        i.onclick = function () {
            document.querySelector('img.selected')?.classList.remove('selected');
            this.classList.add('selected');
        }
        attachTooltipListeners(i);
    });

    document.querySelectorAll('#creativeMenu > h4').forEach(i =>
        i.onclick = function () {
            this.classList[this.classList.contains('visible') ? 'remove' : 'add']('visible');
        });
}

function updateFloors() {
    let output = level.nbFloors < 7 ? '<button id="addFloor" class="floor" onclick="level.addFloor()">+</button>' : '';
    for (let i = 0; i < level.nbFloors; i++) output += `<button class='floor ${i == level.currentFloorI ? 'selected' : ''}' onclick='changeFloor(this, ${i})'>${i}</button>`;
    document.getElementById('floorSelect').innerHTML = output;
}

function changeFloor(e, i) {
    document.querySelector('.floor.selected').classList.remove('selected');
    e.classList.add('selected');
    level.setCurrentFloor(i);
}

function updateRooms() {
    var f = level.getCurrentFloor();
    document.getElementById('roomSelect').setAttribute('style', `--nbRoomsX: ${f.width}; --nbRoomsY: ${f.height};`);
    let output = '';
    for (let y = 0; y < f.height; y++) {
        for (let x = 0; x < f.width; x++) {
            if (!f.getRoom(x, y)) output += `<button class="room" onclick="level.getCurrentFloor().addRoom(${x}, ${y})">+</button>`
            else output += `<button class="room ${(x == f.currentRoomX && y == f.currentRoomY) ? 'selected' : ''}" onclick="changeRoom(${x}, ${y})">${y * f.width + x}</button>`
        }
    }
    document.getElementById('roomSelect').innerHTML = output;
}

function changeRoom(x, y) {
    level.getCurrentFloor().setCurrentRoom(x, y);
}

function addObjective(){
    var div = document.createElement('div'),
    txt = document.createElement('textarea'),
    img = document.createElement('img');

    txt.value = 'New objective';
    img.src = '/images/supp.png';
    div.append(txt, img);
    document.getElementById('objectives').appendChild(div);
}

function changeFloorDimensions() {
    level.getCurrentFloor().changeSize(parseInt(document.getElementById('nbRoomsX')?.value ?? '0'), parseInt(document.getElementById('nbRoomsY')?.value ?? '1'));
}

function changeRoomDimensions(){
    level.getCurrentFloor().getCurrentRoom().changeSize(parseInt(document.getElementById('roomWidth')?.value ?? '0'), parseInt(document.getElementById('roomHeight')?.value ?? '1'));
}

function deleteFloor(){
    if(level.nbFloors <= 1) return;
    level.nbFloors--;
    level.floors.splice(level.currentFloorI, 1);
    level.setCurrentFloor(0);
    updateFloors();
}

function deleteRoom(){
    const f = level.getCurrentFloor();
    if(f.rooms.filter(r => r?.width).length <= 1) return;
    f.setRoom(f.currentRoomX, f.currentRoomY, null);
    let i = f.rooms.findIndex(r => r?.width);
    f.setCurrentRoom(i % f.width, Math.floor(i / f.width));
    updateRooms();
}

async function checkLevel(){
    const worker = new Worker('/js/checkLevel.js'),
        json = level.toJSON();
    updatePopup();
    worker.postMessage([json, Tiles]);

    console.log(level.toJSON());
    let t = Date.now();
    console.log('Worker starting');

    worker.onmessage = (e) => {
        if(typeof e.data != 'string'){
            updatePopup(`
            <div id="publishForm" class="glowingBox container vertical center">
                <h3>Your level is ready to be published !</h3>
                <p>You will have to finish the level a first time to make it acessible to all players. Once you click on "publish", you will be redirected to your level to play it. If you edit your level afterwards, you'll have to re-finish it to make it acessible again</p>
                <div class="container horizontal">
                    <button class="cancel" onclick="hidePopup()">Cancel</button>
                    <button class="submit" onclick="publishLevel()">Publish !</button>
                </div>
            </div>
            `);
            jsonObject = e.data;
            console.log(jsonObject);
        }
        else updatePopup(`
            <div class="glowingBox container vertical center">
                <h3>Your level has a problem !</h3>
                <p>${e.data}</p>
                <button class="submit" onclick="hidePopup()">OK</button>
            </div>
            `);
        console.log('Worker finished.\nTotal time :', Date.now()-t, 'ms');
    }

    worker.onerror = e => {
        console.log('Error : ', e);
    }
}

async function publishLevel(){
    updatePopup();

    level.setCurrentFloor(0);
    level.getCurrentFloor().setCurrentRoom(0, 0);
    tileCtx.drawImage(effectsCtx.canvas, 0, 0);
    tileCtx.drawImage(overlayCtx.canvas, 0, 0);

    const image = tileCtx.canvas.toDataURL('png');

    jsonObject.name = document.getElementById('levelName').value;
    jsonObject.difficulty = document.getElementById('levelDifficulty').value;
    jsonObject.objectives = Array.from(document.querySelectorAll('#objectives > div > textarea')).map(o => o.value);

    console.log(jsonObject);

    const form = new FormData()
    form.append('level', JSON.stringify(jsonObject));
    form.append('preview', image);

    const response = await fetch('/util/registerLevel.php', {
        method: "POST",
        body: form
    }).then(r => r.text());

    let test = document.createElement('a');
    test.href = response;
    test.click();
}