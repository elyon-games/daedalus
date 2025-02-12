"use strict";

/** @type {CanvasRenderingContext2D} */
var tileCtx,
    /** @type {CanvasRenderingContext2D} */
    playerCtx,
    /** @type {CanvasRenderingContext2D} */
    effectsCtx,
    /** @type {CanvasRenderingContext2D} */
    overlayCtx,
    /** @type {CanvasRenderingContext2D} */
    previewCtx,
    /** @type {HTMLDivElement} */
    canvasContainer;

const Tiles = {
    EMPTY: 0,
    GROUND: 1,
    WALL: 2,
    ENTRANCE: 3,
    STAIRS_UP: 4,
    STAIRS_DOWN: 5,

    FENCE: 6,
    FENCE_ELECTRIFIED: 7,
    FENCE_CUT: 8,
    DOOR_RED_CLOSED: 9,
    DOOR_RED_OPENED: 10,

    PLANK: 11,
    WOOD: 12,
    SIGN: 13,
    ELECTRIC_ON: 14,
    ELECTRIC_OFF: 15,
    JETPACK: 16,

    CONVEYOR_BELT_NORTH: 17,
    CONVEYOR_BELT_EAST: 18,
    CONVEYOR_BELT_SOUTH: 19,
    CONVEYOR_BELT_WEST: 20,

    KEY_RED: 21,
    KEY_BLUE: 22,
    KEY_GREEN: 23,
    KEY_YELLOW: 24,

    LEVEL_START: 25,
    LEVEL_END: 26,

    GENERATOR: 27,

    DOOR_GREEN_CLOSED: 28,
    DOOR_GREEN_OPENED: 29,
    DOOR_YELLOW_CLOSED: 30,
    DOOR_YELLOW_OPENED: 31,
    DOOR_BLUE_CLOSED: 32,
    DOOR_BLUE_OPENED: 33,

    LIGHTSABER: 34,

    NONEXISTANT: 255
};

const PlayerStates = {
    POISON_IMMUNE: 0b1,
    FIRE: 0b10,
    WATER: 0b100,
    ELECTRIC: 0b1000,
    KEY_RED: 0b10000,
    KEY_BLUE: 0b1000000,
    KEY_YELLOW: 0b10000000,
    KEY_GREEN: 0b100000000,
}

const Directions = {
    NORTH: 0,
    EAST: 1,
    SOUTH: 2,
    WEST: 3
}

const colors = ["RED", "GREEN", "YELLOW", "BLUE"];

const Tooltips = {
    'KEY_RED': { h: 'Red Card', p: 'Opens red doors<br>Infinite uses<br><i>Will this get me a discount ?</i>' },
    'KEY_GREEN': { h: 'Green Card', p: 'Opens green doors<br>Infinite uses<br><i>Will this get me a discount ?</i>' },
    'KEY_BLUE': { h: 'Blue Card', p: 'Opens blue doors<br>Infinite uses<br><i>Will this get me a discount ?</i>' },
    'KEY_YELLOW': { h: 'Yellow Card', p: 'Opens yellow doors<br>Infinite uses<br><i>Will this get me a discount ?</i>' },
    'LIGHTSABER': { h: 'Lightsaber', p: 'A mighty plasma blade that can cut through any fence !<br>Well, except when they are powered.<br><i>It\'s over Anakin !</i>' },
    'PLANK': { h: 'Plank', p: "Can cover a hole in the ground to make it walkable.<br>Consumable<br><i></i>" },
    'JETPACK': { h: 'Jet pack', p: "Allows to glide in the air (empty tiles) for up to three movements.<br>Movement initiated by a jetpack is not stopped while in the air, allowing to move multiple tiles at once<br>Consumable<br><i>Thank you, Halfbrick</i>" }
}


const TemplateLevel = {
    nbFloors: 1,
    objectives: [
        "Example objective"
    ],
    floors: [
        {
            width: 1,
            height: 1,
            startRoomX: 0,
            startRoomY: 0,
            rooms: [
                {
                    x: 0,
                    y: 0,
                    width: 10,
                    height: 10,
                    grid: [
                        1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
                        1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
                        1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
                        1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
                        1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
                        1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
                        1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
                        1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
                        1, 1, 1, 1, 1, 1, 1, 1, 1, 1,
                        1, 1, 1, 1, 1, 1, 1, 1, 1, 1
                    ]
                }],
        }]
};

//#region Tiles

class StaticEffect {
    prefix = "EFFECT";
    preview = false;
    overlay = false;
    framesName = "";
    x = 0;
    y = 0;
    id;
    drawBounds = {
        x: 0,
        y: 0
    }
    /** @type {Tile} */
    tile;

    constructor(tile, x, y, framesName, prefix = "EFFECT", overlay = false, drawBounds = { x: 0, y: 0 }) {
        this.tile = tile;
        this.x = x;
        this.y = y;
        this.framesName = framesName;
        this.prefix = prefix;
        this.overlay = overlay;
        this.drawBounds = drawBounds;
        this.id = this.tile.visualEffects.length;
        this.draw();
    }

    draw() {
        (this.overlay ? overlayCtx : effectsCtx).drawImage(
            document.getElementById(this.prefix + "_" + this.framesName),
            this.drawBounds.x * Parameters.TILE_SIZE,
            this.drawBounds.y * Parameters.TILE_SIZE,
            Parameters.TILE_SIZE,
            Parameters.TILE_SIZE,
            this.x * Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            (this.y + 1) * Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER
        );
    }

    destroy() {
        (this.overlay ? overlayCtx : effectsCtx).clearRect(this.x * Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER, (this.y + 1) * Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER, Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER, Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER);
        this.tile.visualEffects[this.id] = null;
        this.tile = null;
    }

    clone(t) {
        return new StaticEffect(t, this.x, this.y, this.framesName, this.prefix, this.overlay, this.drawBounds);
    }
}

class AnimatedEffect extends StaticEffect {
    framesNb = 0;
    currentFrame = 0;
    loopsRemaining = 1;

    constructor(tile, x, y, framesName, framesNb, nbTimes = 1, prefix = "EFFECT", overlay = false, drawBounds = { x: 0, y: 0 }) {
        super(tile, x, y, framesName, prefix, overlay, drawBounds);
        this.loopsRemaining = nbTimes;
        this.framesNb = framesNb;
    }

    draw() {
        (this.overlay ? overlayCtx : effectsCtx).drawImage(
            document.getElementById(this.prefix + "_" + this.framesName),
            this.drawBounds.x * Parameters.TILE_SIZE,
            (this.drawBounds.y + this.currentFrame) * Parameters.TILE_SIZE,
            Parameters.TILE_SIZE,
            Parameters.TILE_SIZE,
            this.x * Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            (this.y + 1) * Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER
        );
        this.currentFrame++;
        if (this.currentFrame >= this.framesNb && this.loopsRemaining <= 0) this.destroy();
        this.currentFrame = this.currentFrame % this.framesNb;
        this.loopsRemaining--;
    }

    clone(t) {
        return new AnimatedEffect(t, this.x, this.y, this.framesName, this.framesNb, this.loopsRemaining, this.prefix, this.overlay)
    }
}

class Tile {
    type = "EMPTY";
    code = Tiles.EMPTY;
    /** @type {Room} */
    parentRoom;
    x = 0;
    y = 0;
    imgName;
    text = '';
    randomFrame = 0;
    /** @type {StaticEffect[]} */
    visualEffects = [];
    preview = false;
    drawBounds = {
        offsetX: 0,
        offsetY: 0,
        x: 0,
        y: 0
    }

    constructor(x, y, tileCode, parentRoom, preview = false) {
        if (x instanceof Tile) {
            this.x = x.x;
            this.y = x.y;
            this.parentRoom = y;
            this.type = x.type;
            this.code = x.code;
            this.imgName = x.imgName;
            this.randomFrame = x.randomFrame;
            this.visualEffects = x.visualEffects.map(e => e?.clone(this));
            this.drawBounds = {
                offsetX: x.drawBounds.offsetX,
                offsetY: x.drawBounds.offsetY,
                x: x.drawBounds.x,
                y: x.drawBounds.y
            }
            return this;
        }

        this.x = x;
        this.y = y;
        this.parentRoom = parentRoom;
        this.set(tileCode, true);
        this.preview = preview;
    }

    update() {
        this.updateDrawingVariables();
        this.draw();
    }

    updateDrawingVariables() {
        if(this.code != Tiles.SIGN) this.drawBounds = {
            offsetX: 0,
            offsetY: 0,
            x: 0,
            y: 0
        }

        const up = this.parentRoom.getTile(this.x, this.y - 1),
            down = this.parentRoom.getTile(this.x, this.y + 1),
            left = this.parentRoom.getTile(this.x - 1, this.y),
            right = this.parentRoom.getTile(this.x + 1, this.y);

        this.imgName = 'TILE_' + this.type;

        if (this.type.includes('CONVEYOR')) {
            if (up?.code == this.code || up?.code == Tiles.CONVEYOR_BELT_SOUTH || (up?.code != Tiles.CONVEYOR_BELT_NORTH && up?.type.includes('CONVEYOR') && this.code == Tiles.CONVEYOR_BELT_NORTH)) this.drawBounds.y += 1;
            if (right?.code == this.code || right?.code == Tiles.CONVEYOR_BELT_WEST || (right?.code != Tiles.CONVEYOR_BELT_EAST && right?.type.includes('CONVEYOR') && this.code == Tiles.CONVEYOR_BELT_EAST)) this.drawBounds.y += 2;
            if (down?.code == this.code || down?.code == Tiles.CONVEYOR_BELT_NORTH || (down?.code != Tiles.CONVEYOR_BELT_SOUTH && down?.type.includes('CONVEYOR') && this.code == Tiles.CONVEYOR_BELT_SOUTH)) this.drawBounds.y += 4;
            if (left?.code == this.code || left?.code == Tiles.CONVEYOR_BELT_EAST || (left?.code != Tiles.CONVEYOR_BELT_WEST && left?.type.includes('CONVEYOR') && this.code == Tiles.CONVEYOR_BELT_WEST)) this.drawBounds.y += 8;
        }

        if (this.code == Tiles.WALL || this.type.includes('DOOR')) {
            let upValid = down && ((down?.code != Tiles.WALL || down?.type.includes('DOOR')) ?? false),
                leftValid = (left?.code == Tiles.WALL || left?.type.includes('DOOR')) ?? false,
                rightValid = (right?.code == Tiles.WALL || right?.type.includes('DOOR')) ?? false;

            if (this.code == Tiles.WALL) {
                this.drawBounds.x = upValid ? 1 : 0;
                this.drawBounds.y = 2 * leftValid + rightValid;
            } else {
                this.randomFrame = 2 * leftValid + rightValid;
                this.imgName = 'TILE_GROUND';
            }

            if (leftValid && rightValid && this.drawBounds.x > 0) this.drawBounds.x += this.randomFrame;
        }

        switch (this.code) {
            case Tiles.GROUND:
                this.drawBounds.x += this.randomFrame;
                break;
            case Tiles.GENERATOR:
            case Tiles.STAIRS_UP:
            case Tiles.KEY_BLUE:
            case Tiles.KEY_GREEN:
            case Tiles.KEY_RED:
            case Tiles.KEY_YELLOW:
            case Tiles.LEVEL_START:
            case Tiles.LEVEL_END:
            case Tiles.LIGHTSABER:
            case Tiles.PLANK:
            case Tiles.SIGN:
            case Tiles.FENCE_ELECTRIFIED:
            case Tiles.FENCE:
            case Tiles.FENCE_CUT:
            case Tiles.JETPACK:
                this.imgName = 'TILE_GROUND'
                break;
            case Tiles.EMPTY:
                if (up == null || up?.code == Tiles.EMPTY) this.drawBounds.y = 1;
                break;
            case Tiles.ENTRANCE:
                this.drawBounds.y = tileEdgeDirection(this.x, this.y, this.parentRoom);
            default: break;
        }

        this.setupVisualEffects();

        if(this.code == Tiles.SIGN) this.drawBounds = {
            offsetX: 0,
            offsetY: 0,
            x: 0,
            y: 0
        }

        return this.drawBounds;
    }

    updateNeighbours() {
        this.parentRoom.getTile(this.x, this.y - 1)?.update();
        this.parentRoom.getTile(this.x, this.y + 1)?.update();
        this.parentRoom.getTile(this.x - 1, this.y)?.update();
        this.parentRoom.getTile(this.x + 1, this.y)?.update();
    }

    draw(effect = false) {
        if (effect && this.visualEffects) for (let ve of this.visualEffects) {
            ve?.draw();
        }

        (this.preview ? previewCtx : tileCtx).clearRect(
            this.x * Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            (this.y + 1) * Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER
        );

        (this.preview ? previewCtx : tileCtx).drawImage(
            document.getElementById(this.imgName),
            this.drawBounds.x * Parameters.TILE_SIZE,
            this.drawBounds.y * Parameters.TILE_SIZE,
            Parameters.TILE_SIZE + this.drawBounds.offsetX,
            Parameters.TILE_SIZE + this.drawBounds.offsetY,
            (this.x * Parameters.TILE_SIZE + this.drawBounds.offsetX) * Parameters.SIZE_MULTIPLIER,
            ((this.y + 1) * Parameters.TILE_SIZE + this.drawBounds.offsetY) * Parameters.SIZE_MULTIPLIER,
            Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER);
    }

    set(tileCode, first = false) {
        this.code = tileCode;
        this.type = Object.entries(Tiles).find((e, i) => e[1] == tileCode);

        if (!this.type) {
            console.log(tileCode);
            let t = tileCode.match(/(.+?)...(.+)/);
            this.text = t[2].replaceAll('%player%', userTag);
            this.type = "SIGN";
            this.code = Tiles.SIGN;
            this.drawBounds.y = parseInt(t[1]);
            console.log(this)
            this.imgName = 'TILE_GROUND';
        } else this.type = this.type[0];

        if (this.visualEffects) {
            this.visualEffects.forEach(v => v?.destroy());
            this.visualEffects = [];
        }

        if (!first) {
            this.updateDrawingVariables();
            this.updateNeighbours();
        }

        if (this.code == Tiles.WALL) {
            this.randomFrame = Math.floor(Math.random() * 13);
            if (this.randomFrame >= 7) this.randomFrame = 0;
        }
        else if (this.code == Tiles.GROUND) {
            this.randomFrame = Math.floor(Math.random() * 20);
            if (this.randomFrame >= 5) this.randomFrame = 0;
        }
    }

    setupVisualEffects() {
        if (this.visualEffects?.length > 0) this.visualEffects.forEach(v => v?.destroy());
        this.visualEffects = [];

        switch (this.code) {
            case Tiles.LEVEL_END:
            case Tiles.LEVEL_START:
                this.visualEffects.push(new AnimatedEffect(this, this.x, this.y, this.type, 10, Infinity, 'EFFECT', false));
                break;
            case Tiles.ELECTRIC_ON:
                this.visualEffects.push(new AnimatedEffect(this, this.x, this.y, this.type, 2, Infinity));
                break;
            case Tiles.FENCE_ELECTRIFIED:
                this.visualEffects.push(new AnimatedEffect(this, this.x, this.y - 0.7, this.type, 2, Infinity, 'EFFECT', true));
                this.visualEffects.push(new AnimatedEffect(this, this.x, this.y + 0.3, this.type, 2, Infinity, 'EFFECT', true, { x: 1, y: 0 }));
                break;
            case Tiles.FENCE:
            case Tiles.FENCE_CUT:
                this.visualEffects.push(new StaticEffect(this, this.x, this.y - 1, this.type, 'EFFECT', true));
                this.visualEffects.push(new StaticEffect(this, this.x, this.y, this.type, 'EFFECT', false, { x: 0, y: 1 }));
                break;
            case Tiles.LIGHTSABER:
            case Tiles.KEY_RED:
            case Tiles.KEY_BLUE:
            case Tiles.KEY_GREEN:
            case Tiles.KEY_YELLOW:
            case Tiles.GENERATOR:
            case Tiles.PLANK:
            case Tiles.JETPACK:
                this.visualEffects.push(new StaticEffect(this, this.x, this.y - (this.code == Tiles.GENERATOR ? 0.4 : 0), this.type, 'OBJECT'));
                break;
            case Tiles.SIGN:
                this.visualEffects.push(new StaticEffect(this, this.x, this.y - 0.4, this.type, 'OBJECT', false, {x: 0, y: this.drawBounds.y }));
                break;
            case Tiles.STAIRS_DOWN:
            case Tiles.STAIRS_UP:
                this.visualEffects.push(new StaticEffect(this, this.x, this.y, this.type, 'TILE'));
                break;
            case Tiles.DOOR_BLUE_CLOSED:
            case Tiles.DOOR_BLUE_OPENED:
            case Tiles.DOOR_YELLOW_CLOSED:
            case Tiles.DOOR_YELLOW_OPENED:
            case Tiles.DOOR_RED_CLOSED:
            case Tiles.DOOR_RED_OPENED:
            case Tiles.DOOR_GREEN_CLOSED:
            case Tiles.DOOR_GREEN_OPENED:
                this.visualEffects.push(new StaticEffect(this, this.x, this.y - 1, 'WALL', 'EFFECT', true, false, { x: 0, y: this.randomFrame }));
                this.visualEffects.push(new StaticEffect(this, this.x, this.y, this.type, 'TILE', this.type.includes('OPENED'), { x: 0, y: 0 }));
                break;
            case Tiles.WALL:
                this.visualEffects.push(new StaticEffect(this, this.x, this.y - 1, 'WALL', 'EFFECT', true, { x: 0, y: this.drawBounds.y }));
                break;
            default: break;
        }
    }

    applyEffect() {
        if (this.type.startsWith("CONVEYOR_BELT")) {
            p.moveTowards(this.x - (this.type == "CONVEYOR_BELT_WEST") + (this.type == "CONVEYOR_BELT_EAST"), this.y + (this.type == "CONVEYOR_BELT_SOUTH") - (this.type == "CONVEYOR_BELT_NORTH"), true);
        } else if (this.code == Tiles.LEVEL_END) endGame(true);
        else if (this.code == Tiles.ELECTRIC_ON
            || (this.code == Tiles.EMPTY && !p.inventory.includes('JETPACK'))
        ) p.die();
        else if (this.code == Tiles.STAIRS_UP) {
            p.changingFloor = true;
            p.from = -1;
            p.inventory = [];
            document.getElementById('inventory').innerHTML = '';
            p.jetpackUses = 0;
            if (Parameters.INFINITE) p.setNextRoom();
            else level.setCurrentFloor(level.currentFloorI + 1);
        } else if (this.visualEffects?.length > 0) {

            for (let v of this.visualEffects) if (v?.prefix == "OBJECT"
                && this.code != Tiles.GENERATOR
                && this.code != Tiles.SIGN) {
                p.addToInventory(this.type);
                v.destroy();
                this.set(Tiles.GROUND);
                this.draw();
            }
        }

        return null
    }

    destroy() {
        for (let v of this.visualEffects) {
            if (v) v?.destroy();
        }
        (this.preview ? previewCtx : tileCtx).clearRect(this.x * Parameters.TILE_SIZE, this.y * Parameters.TILE_SIZE, Parameters.TILE_SIZE, Parameters.TILE_SIZE);
    }
}

//#endregion

class Room {
    /** @type {Tile[]} */
    tiles = [];
    loaded = false;
    /** @type {Tile[]} */
    upperWall = [];
    x = 0;
    y = 0;
    width = 24;
    height = 24;

    constructor(jsonObject) {
        this.x = jsonObject.x;
        this.y = jsonObject.y;
        this.width = jsonObject.width;
        this.height = jsonObject.height;

        if (jsonObject instanceof Room) {
            for (var t of jsonObject.tiles) this.tiles.push(new Tile(t, this, null, null));
            for (var t of jsonObject.upperWall) this.upperWall.push(new Tile(t, this, null, null));
            return this;
        }

        for (var i = 0; i < jsonObject.width * jsonObject.height; i++) this.tiles.push(new Tile(i % this.width, Math.floor(i / this.width), jsonObject.grid[i], this));

        for (let i = 0; i < this.width; i++) {
            let t = this.getTile(i, 0);
            if (t.code != Tiles.ENTRANCE) this.upperWall.push(new Tile(i, -1, Tiles.WALL, this));
            else this.upperWall.push(new Tile(i, -1, Tiles.EMPTY, this));
        }

        for (var t of this.tiles) t.updateDrawingVariables();

        for (var wall of this.upperWall) wall.updateDrawingVariables();

        return this;
    }

    getTile(x, y) {
        if (this.width > x && x >= 0 && y == -1) return this.upperWall[x];
        else if (this.width <= x || this.height <= y || x < 0 || y < 0) return null;
        return this.tiles[y * this.width + x];
    }

    setTile(x, y, tileCode, first = false) {
        if (isNaN(tileCode)) tileCode = Tiles[tileCode];
        this.tiles[y * this.width + x].set(tileCode, first);
        this.tiles[y * this.width + x].draw();
    }

    changeSize(newX, newY) {
        const newTiles = [], newWall = [];
        for (let i = 0; i < newY; i++) {
            for (let j = 0; j < newX; j++) {
                newTiles.push((j >= this.width || i >= this.height) ? new Tile(j, i, Tiles.GROUND, this) : this.getTile(j, i));
            }
        }
        for (let j = 0; j < newX; j++) newWall.push((j >= this.width) ? new Tile(j, -1, Tiles.WALL, this) : this.getTile(j, -1));
        this.tiles = newTiles;
        this.upperWall = newWall;
        this.width = newX;
        this.height = newY;
        for (let t of this.tiles) t.updateDrawingVariables();
        for (let t of this.upperWall) t.updateDrawingVariables();
        this.load();
    }

    async load() {
        canvasContainer.style.aspectRatio = this.width + ' / ' + (this.height + 1);
        canvasContainer.style.width = (this.width > (this.height + 1) ? 'min(60vw, 90vh)' : 'auto');
        canvasContainer.style.height = (this.width <= (this.height + 1) ? 'min(60vw, 90vh)' : 'auto');

        document.querySelectorAll('canvas').forEach(c => {
            c.width = this.width * Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER;
            c.height = (this.height + 1) * Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER;
            c.getContext('2d').clearRect(0, 0, c.width, c.height);
        });

        this.loaded = true;
        await this.draw();

        if (!Parameters.CREATIVE) return;
        document.getElementById('roomWidth').value = this.width;
        document.getElementById('roomHeight').value = this.height;
    }

    draw() {
        return new Promise((res, rej) => {
            drawables = [];
            for (var wall of this.upperWall) wall.draw();
            for (var tile of this.tiles) tile.draw(true);
            res();
        });
    }

    toJSON() {
        return {
            x: this.x,
            y: this.y,
            width: this.width,
            height: this.height,
            grid: this.tiles.map(t => t.code == Tiles.SIGN ? t.drawBounds.y + Parameters.SIGN_DELIMITER + t.text : t.code)
        };
    }

    static randomGen(width, height) {
        let tab = [], key = false, door = false;
        for (var i = 0; i < width * height; i++) {
            var r = Math.random();
            let type = Tiles.GROUND;
            if (r < 0.1) type = Tiles.WALL;
            else if (r < 0.15) type = Tiles.EMPTY;
            else if (r < 0.2) type = Tiles.ELECTRIC_ON;
            else if (r < 0.25) type = Tiles.CONVEYOR_BELT_NORTH + Math.floor(Math.random() * 3.99);
            else if (r < 0.3 && !key) { type = Tiles.KEY_RED; key = true; }
            else if (r < 0.4 && !door) { type = Tiles.DOOR_RED_CLOSED; door = true; }
            tab.push(type);
        }
        return new Room({ x: 0, y: 0, width: width, height: height, tiles: tab });
    }
}

class Floor {
    /** @type {Room[]} */
    rooms = [];
    width = 1;
    height = 1;
    currentRoomX = 0;
    currentRoomY = 0;

    constructor(jsonObject) {
        this.width = jsonObject.width;
        this.height = jsonObject.height;
        this.currentRoomX = jsonObject.startRoomX ?? 0;
        this.currentRoomY = jsonObject.startRoomY ?? 0;
        for (let r of jsonObject.rooms) this.rooms[r.y * this.width + r.x] = new Room(r);
    }

    getRoom(x, y) {
        if (this.width <= x || this.height <= y || x < 0 || y < 0) return null;
        return this.rooms[y * this.width + x];
    }

    setRoom(x, y, r) {
        if (this.width <= x || this.height <= y || x < 0 || y < 0) return;
        this.rooms[y * this.width + x] = r;
    }

    addRoom(x, y) {
        if (this.getRoom(x, y) || !Parameters.CREATIVE) return;
        this.setRoom(x, y, new Room(TemplateLevel.floors[0].rooms[0]));
        this.setCurrentRoom(x, y);
        this.getCurrentRoom().x = x;
        this.getCurrentRoom().y = y;
    }

    setCurrentRoom(x, y) {
        if (x >= Parameters.MAX_FLOOR_WIDTH || x < 0 || y >= Parameters.MAX_FLOOR_HEIGHT || y < 0) return;
        this.currentRoomX = x;
        this.currentRoomY = y;
        this.getCurrentRoom().load();
        if (!Parameters.CREATIVE) return;
        updateRooms();
    }

    getCurrentRoom() {
        return this.rooms[this.currentRoomY * this.width + this.currentRoomX];
    }

    changeSize(newX, newY) {
        if (newX > Parameters.MAX_FLOOR_WIDTH || newX <= 0 || newY > Parameters.MAX_FLOOR_HEIGHT || newY <= 0) return;
        const newRooms = [];
        for (let i = 0; i < newY; i++) {
            for (let j = 0; j < newX; j++) {
                newRooms.push(j >= this.x ? new Room(TemplateLevel.floors[0].rooms[0]) : this.getRoom(j, i));
            }
        }
        this.width = newX;
        this.height = newY;
        this.rooms = newRooms;

        updateRooms();
    }

    async changeRoom(rx, ry) {
        drawables = [];
        this.getCurrentRoom().loaded = false;
        this.currentRoomX = rx;
        this.currentRoomY = ry;
        if (!Parameters.CREATIVE) p.enterRoom(this.getRoom(rx, ry));
        await this.getRoom(rx, ry).load();
        return;
    }

    toJSON() {
        const startRoom = this.rooms.find(r => r?.tiles.some(t => t.code == Tiles.STAIRS_DOWN));
        return {
            height: this.height,
            width: this.width,
            startRoomX: startRoom?.x,
            startRoomY: startRoom?.y,
            rooms: this.rooms.map(r => r?.toJSON()).filter(t => t != null)
        }
    }
}


class Level {
    id;
    /** @type {Floor[]} */
    floors = [];
    currentFloorI = 0;
    nbFloors = 1;

    constructor(jsonObject) {
        this.id = jsonObject.id;
        this.nbFloors = jsonObject.nbFloors;

        for (let f of jsonObject.floors) this.floors.push(new Floor(f));

        let output = '';
        for (let str of jsonObject.objectives) output += (Parameters.CREATIVE ? `<div><textarea>${str}</textarea><img src="/images/supp.png" onclick="this.parentElement.outerHTML = '';"></div>` : `<li>${str}</li>`);
        if (document.getElementById('objectives')) document.getElementById('objectives').innerHTML += output;

        this.floors[0].changeRoom(this.floors[0].currentRoomX, this.floors[0].currentRoomX);
    }

    getFloor(i) {
        return this.floors[i];
    }

    setFloor(i, f) {
        this.floors[i] = f;
    }

    addFloor() {
        if (!Parameters.CREATIVE) return;
        this.floors.push(new Floor(TemplateLevel.floors[0]));
        this.setCurrentFloor(this.nbFloors++);
        updateFloors();
    }

    getCurrentFloor() {
        return this.floors[this.currentFloorI];
    }

    setCurrentFloor(i) {
        if (i >= this.nbFloors) return;
        this.currentFloorI = i;
        var f = this.floors[this.currentFloorI];
        f.changeRoom(f.currentRoomX, f.currentRoomY);
        if (!Parameters.CREATIVE) return;
        document.getElementById('nbRoomsX').value = f.width;
        document.getElementById('nbRoomsY').value = f.height;
        updateRooms();
    }

    toJSON() {
        return {
            id: this.id,
            nbFloors: this.nbFloors,
            floors: this.floors.map(f => f.toJSON())
        }
    }
}

class Player {
    x = 0;
    y = 0;
    playerState = 0;
    lives = 3;
    /** @type {Room} */
    currentRoom;
    /** @type {Room} */
    nextRoom;
    roomWorker = new Worker('/js/fetchRoom.js');
    jetpackUses = 0;
    changingFloor = false;
    direction = Directions.EAST;
    from = Directions.NORTH;
    movable = false;
    movesNb = 0;
    infiniteFloorsNb = -1;
    /** @type {HTMLImageElement} */
    sprite;
    lastMoveTime;
    inventory = [];

    checkPoint = {
        x: 0,
        y: 0,
        playerState: 0,
        inventory: [],
        room: null
    }

    constructor() {
        this.roomWorker.onerror = (e) => {
            console.log('Error received while fetching new room : ', e.error);
        }
    }

    updateSprite() {
        this.sprite = document.getElementById('PLAYER');
    }

    checkMove(x, y, force = false) {
        const tile = this.currentRoom.getTile(x, y);
        if (!tile || y < 0) return false;
        let returnValue = null;

        for (let c of colors) {
            if (tile.type == "DOOR_" + c + "_CLOSED") {
                if (this.inventory.includes("KEY_" + c)) p.currentRoom.setTile(x, y, Tiles['DOOR_' + c + '_OPENED'])
                return false;
            }
        }

        if (tile.code == Tiles.FENCE) {
            if (this.inventory.includes("LIGHTSABER")) {
                tile.set(Tiles.FENCE_CUT);
                tile.update();
            }
            return false;
        }

        if (tile.code == Tiles.EMPTY && !force) {
            if (this.inventory.includes('PLANK') && this.inventory.includes('JETPACK')) {
                updatePopup(`
                <h3>
                    Choice of action
                </h3>
                <div class="container horizontal center">
                    ${this.inventory.includes('PLANK') ? '<button class="submit" onclick="usePlank()">Place plank</button>' : ''}
                    ${this.inventory.includes('JETPACK') ? '<button class="submit" onclick="useJetpack()">Use jet pack</button>' : ''}
                    <button class="cancel" onclick="hidePopup()">Cancel</button>
                </div>
                `);
                return false;
            } else if (this.inventory.includes('JETPACK')) {
                useJetpack();
                return false;
            } else if (this.inventory.includes('PLANK')) {
                usePlank();
                return false;
            }
        }

        if (returnValue !== null) return returnValue;

        switch (Tiles[tile.type]) {
            case Tiles.WALL:
            case Tiles.FENCE:
            case Tiles.FENCE_ELECTRIFIED:
                return false;
        }
        return true;
    }

    /**
     * Loads the room that the player is entering
     * @param {Room} r 
     */
    async enterRoom(r) {
        if (r == null) return;
        var x = 0, y = 0;
        await new Promise((res, rej) => {
            for (let i = 0; i < r.width * r.height; i++) {
                let t = r.getTile(i % r.width, Math.floor(i / r.width));
                if ((this.changingFloor && (t.code == Tiles.STAIRS_DOWN))
                    || this.from == tileEdgeDirection(t.x, t.y, r) && t.code == Tiles.ENTRANCE
                    || t.code == Tiles.LEVEL_START) {
                    x = t.x;
                    y = t.y;
                    if (t.code != Tiles.LEVEL_START) break;
                }
            }
            res();
        });

        if(Parameters.INFINITE){
            this.movesNb = 0;
            document.getElementById('maxMoves').innerHTML = 'Maximum moves : '+ Math.ceil((r.width * r.height) / 2);
        }

        this.currentRoom = r;
        if (!r.loaded) await r.load();
        this.updateSprite();
        this.moveAt(x, y);
        this.checkPoint = {
            x: this.x,
            y: this.y,
            playerState: this.playerState,
            inventory: [...this.inventory],
            room: new Room(r)
        }
    }

    specialAction() {
        var t = this.currentRoom.getTile(this.x, this.y);
        if (!t) return;
        switch (t.code) {
            case Tiles.ENTRANCE:
                this.from = invertDir(tileEdgeDirection(this.x, this.y, this.currentRoom));
                level.getCurrentFloor().changeRoom(this.currentRoom.x - (this.from == Directions.EAST) + (this.from == Directions.WEST), this.currentRoom.y - (this.from == Directions.SOUTH) + (this.from == Directions.NORTH));
                break;
            case Tiles.GENERATOR:
                playAudio(`/sounds/${t.visualEffects[0].drawBounds.y ? 'startup' : 'shutdown'}.wav`);
                this.currentRoom.tiles.forEach(t => {
                    if (t.code == Tiles.ELECTRIC_ON) t.set(Tiles.ELECTRIC_OFF);
                    else if (t.code == Tiles.ELECTRIC_OFF) t.set(Tiles.ELECTRIC_ON);
                    else if (t.code == Tiles.GENERATOR) t.visualEffects[0].drawBounds.y = (t.visualEffects[0].drawBounds.y + 1) % 2;
                    else if (t.code == Tiles.FENCE_ELECTRIFIED) t.set(Tiles.FENCE);
                    else if (t.code == Tiles.FENCE) t.set(Tiles.FENCE_ELECTRIFIED);
                });
                this.currentRoom.draw();
                break;
            case Tiles.SIGN:
                updatePopup(t.text + '<button class="submit" onclick="hidePopup()">OK</a>');
                break;
        }
        this.movesNb++;
    }

    moveAt(x, y) {
        this.x = x;
        this.y = y;
        this.draw();
        this.lastMoveTime = Date.now();
    }

    moveTowards(x, y, force = false) {
        if (this.x < x) this.direction = Directions.EAST;
        else if (this.x > x) this.direction = Directions.WEST;
        else if (this.y < y) this.direction = Directions.SOUTH;
        else this.direction = Directions.NORTH;

        if(force) this.movesNb--;

        if (!(this.movable || force) || !this.checkMove(x, y, force)) return;
        this.movesNb++;
        this.movable = false;
        this.lastMoveTime = Date.now();

        if (!this.playerState && !this.currentRoom.getTile(this.x, this.y)?.type.includes('CONVEYOR')) this.playerState = 1;

        var loop = setInterval(() => {
            var dx = this.x - x, dy = this.y - y;
            if (Math.sqrt(dx * dx + dy * dy) <= 1 / Parameters.TILE_SIZE) {
                let tile = this.currentRoom.getTile(x, y);
                if (tile) tile.applyEffect();
                if (tile.code != Tiles.EMPTY) this.playerState = 0;
                if (tile.code != Tiles.STAIRS_UP && Parameters.INFINITE && this.movesNb > this.currentRoom.width * this.currentRoom.height) this.die();
                this.moveAt(Math.round(this.x), Math.round(this.y));
                this.movable = true;
                clearInterval(loop);
                return;
            }
            let dt = Parameters.MOVEMENT_SPEED * (Date.now() - this.lastMoveTime) / 1000;
            this.lastMoveTime = Date.now();
            let newX = this.x - Math.sign(dx) * dt,
                newY = this.y - Math.sign(dy) * dt
            if (Math.sign(x - this.x) != Math.sign(x - newX)) newX = x;
            if (Math.sign(y - this.y) != Math.sign(y - newY)) newY = y;
            this.moveAt(newX, newY);
        });
    }

    addToInventory(objectName) {
        let e = document.getElementById('OBJECT_' + objectName).cloneNode();
        document.getElementById('inventory').appendChild(e);
        e.setAttribute('name', Tooltips[objectName].h)
        e.setAttribute('description', Tooltips[objectName].p)
        e.classList.add(e.id);
        e.id = '';
        attachTooltipListeners(e);
        playAudio('/sounds/pickup.wav');
        this.inventory.push(objectName);
        if (objectName == 'JETPACK') document.getElementById('jetpackUses').innerHTML = `Charges left : ${p.inventory.filter(o => o == 'JETPACK').length * 3 - p.jetpackUses}`;
    }

    updateInventory(newInv) {
        document.getElementById('inventory').innerHTML = '';
        this.inventory = [...newInv];
        for (let o of newInv) {
            let e = document.getElementById('OBJECT_' + o).cloneNode();
            document.getElementById('inventory').appendChild(e);
            e.setAttribute('name', Tooltips[o].h)
            e.setAttribute('description', Tooltips[o].p)
            attachTooltipListeners(e);
        }
    }

    die() {
        if (this.lives <= 0) return;

        this.playerState = this.checkPoint.playerState;
        this.updateInventory(this.checkPoint.inventory);

        
        this.currentRoom = new Room(this.checkPoint.room);

        drawables = [];
        this.currentRoom.load();

        if (Parameters.INFINITE) {
            this.lives--;
            this.movesNb = 0;
            document.getElementById('lives').innerHTML = 'Lives left : ' + this.lives;
            if(this.lives == 0) endGame();
        } else {
            level.getCurrentFloor().setRoom(this.currentRoom.x, this.currentRoom.y, this.currentRoom);
        }

        this.moveAt(this.checkPoint.x, this.checkPoint.y);
    }

    draw() {
        playerCtx.clearRect(0, 0, playerCtx.canvas.width, playerCtx.canvas.height);
        playerCtx.drawImage(
            this.sprite,
            this.direction * Parameters.TILE_SIZE,
            this.playerState * 37,
            Parameters.TILE_SIZE,
            37,
            this.x * Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            (this.y + 0.8) * Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            Parameters.TILE_SIZE * Parameters.SIZE_MULTIPLIER,
            37 * Parameters.SIZE_MULTIPLIER
        );
    }

    setNextRoom() {
        return new Promise(async (res, rej) => {
            p.infiniteFloorsNb++;
            this.changingFloor = true;
            await this.enterRoom(this.nextRoom);
            this.nextRoom = null;

            this.roomWorker.onmessage = (e) => {
                console.log(e.data);
                const r = new Room(e.data);
                this.nextRoom = r;
                res();
            };

            this.roomWorker.postMessage({ difficulty: Math.min(Math.max(Math.floor(this.infiniteFloorsNb / 5), 2), Parameters.MAX_DIFFICULTY), floors: this.infiniteFloorsNb, origin: window.location.origin });
        });
    }
}

/* ------------------------------ GAME CODE ------------------------------ */

async function saveInfiniteState(){
    const form = new FormData();
    form.append('floors', p.infiniteFloorsNb);
    form.append('mode', 'infinite');
    form.append('end', false);
    const error = await fetch('/util/registerPerformance.php', {
        method: 'POST',
        body: form
    }).then(r => r.text());
    window.location = '/infinite';
}

function distanceTo(x1, y1, x2, y2) {
    return Math.sqrt(Math.pow(x1 - x2, 2) + Math.pow(y1 - y2, 2));
}

function invertDir(d) {
    if (d == Directions.NORTH) return Directions.SOUTH;
    if (d == Directions.SOUTH) return Directions.NORTH;
    if (d == Directions.EAST) return Directions.WEST;
    if (d == Directions.WEST) return Directions.EAST;
    return -1;
}

function tileEdgeDirection(x, y, r) {
    if (y == 0) return Directions.NORTH;
    if (x == 0) return Directions.WEST;
    if (y == r.height - 1) return Directions.SOUTH;
    if (x == r.width - 1) return Directions.EAST;
    return -1;
}

/**
 * 
 * @param {MouseEvent} e 
 */
function updateTooltip(e) {
    const output = `<div><h4>${e.target.getAttribute('name')}</h4><hr><p>${e.target.getAttribute('description')}</p><div>`;
    if (!document.getElementById('tooltip')) document.getElementById('fullscreenWrapper').innerHTML += `<div id="tooltip" class="glowingBox"><div>${output}</div></div>`;
    else document.getElementById('tooltip').innerHTML = output;

    document.getElementById('tooltip').setAttribute('style', `left:${e.pageX}px; top:${e.pageY}px;`);
    document.getElementById('tooltip').classList.add('visible');
}

function attachTooltipListeners(element) {
    element.addEventListener('mouseenter', updateTooltip);
    element.addEventListener('mouseleave', e => {
        if (document.getElementById('tooltip')) document.getElementById('tooltip').classList.remove('visible');
    });
}


const Parameters = {
    TILE_SIZE: 32,
    SIZE_MULTIPLIER: 1,
    MOVEMENT_SPEED: 5,
    DRAW_DELAY: 150,
    CREATIVE: false,
    MAX_FLOOR_WIDTH: 32,
    MAX_FLOOR_HEIGHT: 32,
    MAX_FLOOR_WIDTH: 5,
    MAX_FLOOR_HEIGHT: 5,
    MAX_NB_FLOORS: 7,
    VOLUME: 1,
    SIGN_DELIMITER: '...',
    INFINITE: false,
    MAX_DIFFICULTY: 9
};

var drawables = [],
    previousTime = Date.now();

const p = new Player();
/** @type {Level} */
var level;

function playAudio(path) {
    let a = new Audio(path);
    a.volume = Parameters.VOLUME;
    a.play();
    return a;
}

var renderLoopID = setInterval(() => {
    // console.log('Render start')
    const dt = Date.now() - previousTime;
    overlayCtx.clearRect(0, 0, overlayCtx.canvas.width, overlayCtx.canvas.height);
    effectsCtx.clearRect(0, 0, effectsCtx.canvas.width, effectsCtx.canvas.height);
    for (let t of ((Parameters.CREATIVE ? level.getCurrentFloor().getCurrentRoom() : p.currentRoom)?.tiles ?? [])) {
        t.visualEffects?.forEach(v => v?.draw());
    }
    // console.log('Render time : ' + ((Date.now() - previousTime) - dt) + 'ms');
    previousTime = Date.now();
}, Parameters.DRAW_DELAY);

document.addEventListener('DOMContentLoaded', () => {
    Parameters.INFINITE = levelMode == 'infinite';
    tileCtx = document.getElementById('tileCanvas').getContext('2d');
    playerCtx = document.getElementById('playerCanvas').getContext('2d');
    effectsCtx = document.getElementById('effectsCanvas').getContext('2d');
    overlayCtx = document.getElementById('overlayCanvas').getContext('2d');
    canvasContainer = document.getElementById('canvasContainer');
    playOST();
    start();
    setTimeout(() => {
        if (!Parameters.INFINITE) level.getCurrentFloor().getCurrentRoom().draw();
    }, 500);
});


document.addEventListener('keydown', playOST);
document.addEventListener('scroll', playOST);
document.addEventListener('click', playOST);

function updatePopup(text = `<div class="loader">
        <span class="loaderBox"></span>
        <span class="loaderBox"></span>
        <span class="loaderBox"></span>
        <span class="loaderBox"></span>
    </div><h3>Please wait...</h3>`) {

    document.getElementById('popup').innerHTML = text;
    document.getElementById('popup').classList.add('visible');
}

function hidePopup() {
    document.getElementById('popup').classList.remove('visible');
    document.getElementById('popup').innerHTML = '';
}