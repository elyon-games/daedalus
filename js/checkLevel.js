onmessage = (e) => {
    let startFloor = -1,
        endFloor = Infinity;
    const levelObject = e.data[0],
        Tiles = e.data[1],
        objects = new Set();

    for(let i = 0; i < levelObject.floors.length; i++){
        let f = levelObject.floors[i], 
            hasStairsUp = false,
            hasStairsDown = false;

        for(let j = 0; j < f.height * f.width; j++){
            let r = f.rooms[j],
                roomX = j % f.width,
                roomY = Math.floor(j / f.width),
                hasEntrance = false,
                hasLevelStart = false,
                hasLevelFinish = false;

            if(!r) continue;

            for(let k = 0; k < r.width * r.height; k++) {
                let t = r.grid[k],
                    y = Math.floor(k / r.width),
                    x = k % r.width;

                switch(t){
                    case Tiles.ENTRANCE:
                        let yAxis = (y ? (y == (r.height - 1) ? 1 : 0) : -1),
                            xAxis = (x ? (x == (r.width - 1) ? 1 : 0) : -1);
                        if(!yAxis && !xAxis) return postMessage(`Floor n°${i} room at coordinates ${j % f.width},${Math.floor(j/f.width)} : entrance not positioned besides a wall !`);

                        if(yAxis == xAxis || yAxis == -xAxis) return postMessage(`Floor n°${i} room at coordinates ${j % f.width},${Math.floor(j/f.width)} : entrance positioned in a corner !`);
                        
                        if(!f.rooms.some(r => r.x == (roomX + xAxis) && r.y == (roomY + yAxis))) return postMessage(`Floor n°${i} room at coordinates ${j % f.width},${Math.floor(j/f.width)} : entrance leading to an inexistent room !`);
                        
                        hasEntrance = true;
                        break;
                    case Tiles.LEVEL_START:
                        hasLevelStart = true;
                        f.startRoomX = j % f.width;
                        f.startRoomY = Math.floor(j / f.height);
                        startFloor = i;
                        break;
                    case Tiles.LEVEL_END:
                        hasLevelFinish = true;
                        endFloor = i;
                        break;
                    case Tiles.STAIRS_UP:
                        if(i == levelObject.floors.length - 1) return postMessage(`Floor n°${i} room at coordinates ${j % f.width},${Math.floor(j/f.width)} : stairs leading to an inexistent floor (up) !`);
                        hasEntrance = true;
                        hasStairsUp = true;
                        break;
                    case Tiles.STAIRS_DOWN:
                        if(i == 0) return postMessage(`Floor n°${i} room at coordinates ${j % f.width},${Math.floor(j/f.width)} : stairs leading to an inexistent floor (down) !`);
                        f.startRoomX = j % f.width;
                        f.startRoomY = Math.floor(j / f.height);
                        hasEntrance = true;
                        hasStairsDown = true;
                        break;
                    case Tiles.JETPACK:
                    case Tiles.LIGHTSABER:
                    case Tiles.KEY_RED:
                    case Tiles.KEY_BLUE:
                    case Tiles.KEY_YELLOW:
                    case Tiles.KEY_GREEN:
                    case Tiles.PLANK:
                        let type = Object.entries(Tiles).find((e, i) => e[1] == t);
                        if(type) objects.add(type[0]);
                }
            }

            if(!hasEntrance && !(hasLevelStart && hasLevelFinish)) return postMessage(`Floor n°${i} room at coordinates ${j % f.width},${Math.floor(j/f.width)} : no passage to other rooms`);
        }
        if(!(hasStairsUp || i == levelObject.floors.length - 1)) return postMessage(`Floor N°${i} doesn't have stairs to go up !`);
        if(!(hasStairsDown || i == 0)) return postMessage(`Floor N°${i} doesn't have stairs to go down !`);
    }
    if(startFloor != 0) return postMessage(`The start of the level must be placed on floor 0 !`);
    if(endFloor != levelObject.floors.length - 1) return postMessage(`The end of the level must be placed on the last floor !`);

    if(objects.size >= 1) levelObject.objects = Array.from(objects).reduce((p, c) => p + ',' + c, '').match(/^,?(.+?),?$/)[1];
    else levelObject.objects = '';
    return postMessage(levelObject);
}