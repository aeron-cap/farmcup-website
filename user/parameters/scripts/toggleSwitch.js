document.addEventListener('DOMContentLoaded', function() {
    const manualControlSwitch = document.getElementById('manualControlSwitch');
    const editParametersLink = document.getElementById('editParameters');

    function toggleLinkState() {
        if (manualControlSwitch.checked) {
            editParametersLink.classList.remove('disabled');
            editParametersLink.removeAttribute('disabled');
            editParametersLink.removeEventListener('click', preventDefaultClick);
        } else {
            editParametersLink.classList.add('disabled');
            editParametersLink.setAttribute('disabled', 'disabled');
            editParametersLink.addEventListener('click', preventDefaultClick);
            editParametersLink.removeEventListener('contextmenu', preventContextMenu);
        }
    }

    function preventDefaultClick(e) {
        e.preventDefault();
    }

    function preventContextMenu(e) {
        e.preventDefault();
    }

    manualControlSwitch.addEventListener('change', toggleLinkState);
    toggleLinkState();
});