document.addEventListener('click', () => {

    fetch('/checksession', {
        method: 'GET'
    }).then(response => {
        console.log(window.location.href)
        if(response.status === 403) {
            if (window.location.href !== 'https://easy-auto.fr') {
                window.location.href = '/'
            }
        }
    })

})
