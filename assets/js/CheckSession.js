document.addEventListener('click', () => {
    console.log(window.location.href)

    fetch('/checksession', {
        method: 'GET'
    }).then(response => {

        if(response.status === 403 && !document.getElementById('inputPassword')) {
                window.location.href = '/'
        }
    })

})
