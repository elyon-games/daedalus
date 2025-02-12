onmessage = async (e) => {
    const method = "SOLVER" + String.fromCharCode(e.data.difficulty + 65);
    let [t, width, height, data] = (await fetch(withQuery('/api', {"sa": e.data.difficulty}, e.data.origin), {
        method: method,
        body: JSON.stringify(e.data.difficulty)
    }).then(r => r.text())).match(/(.+?),(.+?),\[(.+)\]/);

    const form = new FormData();
    form.append('floors', e.data.floors);
    form.append('mode', 'infinite');
    form.append('end', false);
    const error = await fetch('/util/registerPerformance.php', {
        method: 'POST',
        body: form
    }).then(r => r.text());

    width = parseInt(width);
    height = parseInt(height);
    
    const grid = data.match(/[0-9]+(?<=,?)/g).filter((v, i) => {
        let x = i % width, y = Math.floor(i / width);
        return (x != 0 && y != 0 && x < width - 1 && y < height - 1);
    }).map(t => parseInt(t));
    postMessage({
        x: 0,
        y: 0,
        width: width - 2,
        height: height - 2, 
        grid: grid,
        error: error
    });
}

function withQuery(url, params, origin) {
    if (url.startsWith("/")) {
        const baseUrl = origin;
        url = `${baseUrl}${url}`;
    }

    const urlObj = new URL(url);
    const queryParams = new URLSearchParams(urlObj.search);

    for (const key in params) {
        if (params[key] === null || params[key] === undefined) {
            queryParams.delete(key);
        } else {
            queryParams.set(key, params[key]);
        }
    }

    urlObj.search = queryParams.toString();

    return urlObj.toString();
}