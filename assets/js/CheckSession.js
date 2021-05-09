document.addEventListener('click', () => {

    fetch('/checksession', {
        method: 'GET'
    }).then(response => {
        if(response.status === 403) {
            if (window.location.href !== 'https://easy-autos.fr/') {
                window.location.href = '/'
            }
        }
    })

})
