const sendData = (phone, action) => {

    fetch('/call/search/' + phone, {
        method      : 'GET',
        headers     : {
            'Content-Type': 'application/json'
        },
    })
        .then(function (response) {
            return response.text()
        }).then(function (html) {
            action(html);
        });
}

const phoneNumberField = document.getElementById('add-call-for-phone');
phoneNumberField.addEventListener('change', (e) => {
    const phoneNumber = phoneNumberField.value;
    sendData(phoneNumber, (data) => {
        data = JSON.parse(data);
        if (data.client_id) {
            alert('un client a un appel en cours');
        } else {

        }
    });
});
