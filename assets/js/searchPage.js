document.addEventListener('DOMContentLoaded', () => {
    const searchButton = document.getElementById('searchBtn');
    const loaderSearch = document.getElementById('loaderSearch');
    searchButton.addEventListener('click', () => {
        loaderSearch.classList.remove('hide');
    });
});
