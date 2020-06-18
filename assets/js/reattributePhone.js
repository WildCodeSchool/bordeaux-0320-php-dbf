const emptyClientForm = () => {
    const dataClient = {
        call_client_id     : '',
        call_client_phone2 : '',
        call_client_name   : '',
        call_client_email  : '',
        call_vehicle_immatriculation : '',
        call_vehicle_chassis : '',
        call_vehicle_id    : '',
    }
    for (var [key, value] of Object.entries(dataClient)){
        if (document.getElementById(key)) {
            document.getElementById(key).value = value
        }
    }
}
document.addEventListener('DOMContentLoaded', () => {
    const reattributeBtn = document.getElementById('reattribute');
    const loaderChangeNumber = document.getElementById('loaderChangeNumber');

    reattributeBtn.addEventListener('click', (event) => {
        event.preventDefault();
        loaderChangeNumber.classList.remove('hide');
        const client = reattributeBtn.getAttribute('data-client');

        fetch('/call/reattribute/' + client, {
            method      : 'GET',
            headers     : {
                'Content-Type': 'application/json',
            },
        }).then(((response) => {
            emptyClientForm();
            loaderChangeNumber.classList.add('hide');
            event.target.classList.add('hide');
            return response.text();
        }));
    });
});
