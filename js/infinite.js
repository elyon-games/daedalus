var modal;
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('myModal').style.display = 'none';
    
    modal = document.getElementById("myModal");

    window.openModal = openModal;
    window.closeModal = closeModal;
});

function showModal() {       
    modal.style.display = "flex";
}
function closeModal() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    var Modal = document.getElementById('myModal');
    if (event.target == myModal) {
        closeModal(event.target.id);
    }
}