const timeout = () => {
    setTimeout(() => {
        window.location.href = '/logout'
    }, 60000)
}

document.addEventListener('click', () => {
    console.log(window.location.href)
    clearTimeout(timeout)
    timeout()

    fetch('/checksession', {
        method: 'GET'
    }).then(response => {
        if(response.status === 403 && window.location.href !== 'https://easy-autos.fr/') {
                window.location.href = '/'
        }
    })

})
