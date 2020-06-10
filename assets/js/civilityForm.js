 /*function sendData(data, action) {
    fetch('/civility/add', {
        method      : 'POST',
        mode        : "same-origin",
        credentials : "same-origin",
        body        : JSON.stringify(data),
        headers     : {
            'Content-Type': 'application/json'
        },
    })
        .then(function (response) {
            action(response)
        });
}
document.addEventListener('DOMContentLoaded', () => {
    const addButton = document.getElementById('civility-button')
    const civility = document.getElementById('civility_name').value
    const data = {
        name:civility
    }
    addButton.addEventListener("click", (event)=> {
        event.preventDefault()
        sendData(data, ( response)=> {
          console.log(response)
            M.toast({ html:'ça marche'})
        })

    })
})
*/
