"use strict";

let inputs = {
    north: false,
    south: false,
    west: false,
    east: false,
    specAct: false,
    esc: false
}, time;

const GUIAnim = [{
    opacity: 0,
    display: 'flex'
}, {
    opacity: 1,
    display: 'flex'
}];

const start = async () => {
    let response;

    if(levelName == '1') updatePopup(`
        <h3>Controls</h3>
        <h5>Use WASD / ZQSD / Arrow Keys to move the player around<br><br>Use the Space key to interact with some objects<br><br>Good luck !</h5>
        <div class="container horizontal center">
        <button class="submit" onclick="hidePopup()">OK</button>
        <a class="submit" href="/util/finishTuto.php">Skip tutorial</a>
        </div>
        `)

    if (Parameters.INFINITE) {
        document.getElementById('lives').innerHTML = 'Lives left : ' + p.lives;
        updatePopup();
        await p.setNextRoom();
        hidePopup();
        setTimeout(() => p.setNextRoom(), 500);
    } else {
        response = await fetch(levelMode == 'level' ? `/data/levels/${levelName}.json` : `/data/adventure/${levelName}.json`).then(r => r.json());
        level = new Level(response);
        let output = '';
        for (let o of response.objectives) output += `<li>${o}</li>`;
        document.getElementById('objectives').innerHTML = output;
    }

    p.movable = true;

    document.querySelector('#audioRange > input').oninput = function () {
        var value = Math.round((this.value - this.min) / (this.max - this.min) * 100);
        document.querySelector('#audioRange > span').textContent = value;
        this.style.background = 'linear-gradient(to right, var(--accent-theme-color-2) 0%, var(--accent-theme-color-2) ' + value + '%, gray ' + value + '%, gray 100%)'
    };

    time = Date.now();

    document.querySelector('#audioRange > input').addEventListener('input', (e) => changeVolume(e.target.value));
}

function fullscreen() {
    if (document.fullscreenElement) {
        document.exitFullscreen();
        document.body.appendChild(document.getElementById('popup'));
    } else {
        document.getElementById('fullscreenWrapper').requestFullscreen();
        document.getElementById('fullscreenWrapper').appendChild(document.getElementById('popup'));
    }
}

document.addEventListener('keydown', (e) => {

    // console.log(e);
    if (document.getElementById('gui').classList.contains('visible') && e.key != 'Escape') return;

    switch (e.key) {
        case 'Escape':
            inputs.esc = true;
            e.preventDefault();
            break;
        case 'w':
        case 'z':
        case 'ArrowUp':
            inputs.north = true;
            e.preventDefault();
            break;
        case 's':
        case 'ArrowDown':
            inputs.south = true;
            e.preventDefault();
            break;
        case 'a':
        case 'q':
        case 'ArrowLeft':
            inputs.west = true;
            e.preventDefault();
            break;
        case 'd':
        case 'ArrowRight':
            inputs.east = true;
            e.preventDefault();
            break;
        case ' ':
            inputs.specAct = true;
            e.preventDefault();
            break
        default:
            return false;
    }

    if (inputs.esc) document.getElementById('gui').classList[document.getElementById('gui').classList.contains('visible') ? 'remove' : 'add']('visible');
    else if (inputs.specAct) {
        p.specialAction();
        if(Parameters.INFINITE) document.getElementById('moves').innerHTML = 'Current moves : '+p.movesNb;
    } else {
        if(Parameters.INFINITE) document.getElementById('moves').innerHTML = 'Current moves : '+p.movesNb;
        p.moveTowards(p.x - inputs.west + inputs.east, p.y - inputs.north + inputs.south);
    }
    inputs = {
        north: false,
        south: false,
        west: false,
        east: false,
        specAct: false,
        esc: false
    };
});

function changeVolume(volume) {
    Parameters.VOLUME = volume / 100;
    ost.volume = Parameters.VOLUME;
}

async function endGame() {
    updatePopup();

    const form = new FormData();
    let dt = Date.now() - time,
        t = `${Math.floor(dt / 3_600_000).toString().padStart(2, '0')}:${(Math.floor(dt / 60_000) % 60).toString().padStart(2, '0')}:${(Math.floor(dt / 1000) % 60).toString().padStart(2, '0')}`;
    if (levelMode == 'level' || levelMode == 'adventure') {
        form.append('time', t);
        form.append('moves', p.movesNb);
        form.append('mode', levelMode);
        form.append('level_id', level.id);
    } else if (levelMode == 'infinite') {
        form.append('floors');
    }

    const response = await fetch('/util/registerPerformance.php', {
        method: 'POST',
        body: form
    }).then(r => r.text());

    if (response != 'ok') {
        console.log(response);
        return updatePopup('<h3>Error</h3><p>An unknown error occured, and your performanced was not saved :(</p><button onclick="hidePopup()">OK</button>');
    }

    if(levelMode == 'infinite'){
        return updatePopup(`<h3>Congrats !</h3>
            <h4>You made it to floor n°${p.infiniteFloorsNb} !</h4>
            <p>
            Number of moves : <span>${p.movesNb}</span><br>
            Total time taken : <span>${t}</span><br>
            </p>
            <a class="submit" href="/infinite/">OK</a>`);
    }

    updatePopup(`<h3>Congrats !</h3>
        <h4>You finished this level !</h4>
        <p>
        Number of moves : <span>${p.movesNb}</span><br>
        Total time taken : <span>${t}</span><br>
        </p>
        <a class="submit" href="${levelMode == 'level' ? './' : '/adventure' }">OK</a>`);
}

function usePlank() {
    let i = p.inventory.indexOf('PLANK');
    if (i < 0) return;
    p.currentRoom.setTile(p.x + (p.direction == Directions.EAST ? 1 : (p.direction == Directions.WEST ? -1 : 0)), p.y + (p.direction == Directions.SOUTH ? 1 : (p.direction == Directions.NORTH ? -1 : 0)), Tiles.WOOD);
    p.inventory.splice(i, 1);
    document.querySelector('#inventory .OBJECT_PLANK').remove();
    hidePopup();
}

function useJetpack() {
    let i = p.inventory.indexOf('JETPACK'), j = 1;
    if (i < 0) return;
    const dx = (p.direction == Directions.EAST ? 1 : (p.direction == Directions.WEST ? -1 : 0)),
        dy = (p.direction == Directions.SOUTH ? 1 : (p.direction == Directions.NORTH ? -1 : 0));

    while (p.currentRoom.getTile(p.x + dx * j, p.y + dy * j)?.code == Tiles.EMPTY) j++;
    let t = p.currentRoom.getTile(p.x + dx * j, p.y + dy * j)
    if (!t
        || t.code == Tiles.WALL
        || t.type.includes('CLOSED')
        || t.code == Tiles.FENCE
        || t.code == Tiles.FENCE_ELECTRIFIED
    ) j--;
    hidePopup();
    p.jetpackUses = (p.jetpackUses + 1) % 3;
    if (p.jetpackUses == 0) {
        p.inventory.splice(i, 1);
        document.querySelector('#inventory .OBJECT_JETPACK').remove();
    }
    document.getElementById('jetpackUses').innerHTML = `Jetpack charges : ${p.inventory.filter(o => o == 'JETPACK').length * 3 - p.jetpackUses}`;
    p.playerState = 2;
    p.moveTowards(p.x + dx * j, p.y + dy * j, true);
}