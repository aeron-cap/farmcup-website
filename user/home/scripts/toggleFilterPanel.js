function toggleFilterContainerA() {
    const filterContainer = document.getElementById('filter-container-a');
    filterContainer.classList.toggle('visible');
}

document.getElementById('toggle-filter-btn-a').addEventListener('click', toggleFilterContainerA);