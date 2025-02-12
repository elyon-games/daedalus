document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('banModal').style.display = 'none';
    document.getElementById('suppModal').style.display = 'none';

    function toggleInfo(infoId) {
        var info = document.querySelector(`[data-info="${infoId}"]`);
        info.style.display = info.style.display === "block" ? "none" : "block";
    }

    function openBanModal(event, tag, id, isBanned) {
        event.stopPropagation();
        var banModal = document.getElementById('banModal');
        closeModal('suppModal');
        banModal.style.display = 'flex';
        document.getElementById('banPlayerName').textContent = tag;
        document.getElementById('banPlayerId').value = id;
        document.getElementById('banAction').value = isBanned ? 0 : 1;
        document.getElementById('banActionText').textContent = isBanned ? 'unban' : 'ban';
        document.getElementById('banConfirmButton').textContent = isBanned ? 'Unban' : 'Ban';
    }

    function openSuppModal(event, tag, id) {
        event.stopPropagation();
        var suppModal = document.getElementById('suppModal');
        closeModal('banModal');
        suppModal.style.display = 'flex';
        document.getElementById('suppPlayerName').textContent = tag;
        document.getElementById('suppPlayerId').value = id;
    }

    function closeModal(modalId) {
        var modal = document.getElementById(modalId);
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        var banModal = document.getElementById('banModal');
        var suppModal = document.getElementById('suppModal');
        if (event.target == banModal || event.target == suppModal) {
            closeModal(event.target.id);
        }
    }

    window.toggleInfo = toggleInfo;
    window.openBanModal = openBanModal;
    window.openSuppModal = openSuppModal;
    window.closeModal = closeModal;

    setTimeout(() => {
        var notifs = document.querySelectorAll('.notification');
        notifs.forEach(notif => {
            notif.style.display = 'none';
        });
    }, 5000);
});