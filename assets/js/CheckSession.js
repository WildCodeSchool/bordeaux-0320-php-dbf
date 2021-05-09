document.addEventListener('click', () => {
    console.log(window.location.href)

    fetch('/checksession', {
        method: 'GET'
    }).then(response => {

        if(response.status === 403 && window.location.href !== 'https://easy-autos.fr/') {
                window.location.href = '/'
        }
    })

})
