document.addEventListener('DOMContentLoaded', () => {
    const searchButton = document.getElementById('searchBtn');
    const loaderSearch = document.getElementById('loaderSearch');
    if (searchButton) {
        searchButton.addEventListener('click', () => {
            loaderSearch.classList.remove('hide');
        });
    }
});
