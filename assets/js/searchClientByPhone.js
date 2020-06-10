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

document.addEventListener('DOMContentLoaded', () => {
    const phoneNumberField = document.getElementById('call_client_phone');
    const modalClientPhone = document.getElementById('modal-callclient-phone');
    const tableForCalls = document.getElementById('calls-on-the-way-for-phone');
    const modalForPhoneAlert = M.Modal.init(modalClientPhone);
    phoneNumberField.addEventListener('change', (e) => {
        const phoneNumber = phoneNumberField.value;
        sendData(phoneNumber, (data) => {
            data = JSON.parse(data);
            data = data[0];
            if (data.calls) {
                document.getElementById('callclient-civility').innerHTML = data.client.client_civility
                document.getElementById('callclient-name').innerHTML     = data.client.client_name
                document.getElementById('callclient-count').innerHTML    = data.calls.length + ' appel';
                document.getElementById('callclient-count').innerHTML   += data.calls.length>1 ? 's' : ''
                document.getElementById('callclient-phone').innerHTML     = data.client.client_phone

                tableForCalls.innerHTML ='';

                data.calls.forEach((call) => {
                    let html = '<tr>' +
                        '<td colspan="">' + call.call_subject + '</td>' +
                        '<td colspan="">' + call.call_comment + '</td>' +
                        '<td colspan="">' + call.call_date + '</td>' +
                        '<td colspan="">' + call.call_hour + '</td>' +
                        '<td colspan="">' + call.call_vehicule + '</td>' +
                        '<td colspan=""><a class="btn light-blue" data-call="' + call.call_id + '">continuer</a></td>' +
                        '</tr>'
                    tableForCalls.innerHTML = tableForCalls.innerHTML + html
                })
                modalForPhoneAlert.open()
            } else {

            }
        });
    });
})

