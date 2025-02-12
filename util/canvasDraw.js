const cellNb = 24, cellSize = 16;

document.addEventListener('DOMContentLoaded', () => {
    var ctx = document.querySelector('canvas').getContext("2d");

    const cv = document.querySelector('canvas');
    cv.width = cv.height = cellNb * cellSize;

    console.log(ctx);

    ctx.strokeStyle = 'black';

    for (var i = 0; i < cellNb; i++) {
        for (var j = 0; j < cellNb; j++) {

            for (var x = 0; x < cellSize; x++) {
                for (var y = 0; y < cellSize; y++) {
                    ctx.beginPath();
                    ctx.fillStyle = `rgb(${Math.round(Math.random() * 255)}, ${Math.round(Math.random() * 255)}, ${Math.round(Math.random() * 255)})`;
                    ctx.rect((i * cellSize + x), j * cellSize + y, 1, 1);

                    ctx.fill()
                    ctx.closePath()
                }
            }

            ctx.rect(i * cellSize, j * cellSize, cellSize, cellSize)
            ctx.stroke();

        }
    }
});

function fullscreen(){
    document.querySelector('canvas').requestFullscreen();
}