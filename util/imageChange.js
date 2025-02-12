function updateImage(files, id) {
    const [f] = files;
    if (f) document.getElementById(id).src = URL.createObjectURL(f);
}

