import { Switch3 } from 'triswitch';

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

const hydrateForm = (data) => {
    for (var [key, value] of Object.entries(data)){
        if (document.getElementById('call_' + key)) {
            document.getElementById('call_' + key).value = value
            if (key === 'client_civility') {
                selectValueInSelect(document.getElementById('call_' + key), value)
                M.FormSelect.init(document.getElementById('call_' + key), {})
            }
        }
    }
}

const selectValueInSelect = (selector, selectorValue) => {
    if (selector.querySelector('option[value="' + selectorValue + '"]')) {
        selector.querySelector('option[value="' + selectorValue + '"]')
            .setAttribute('selected', 'selected');
    }
}

const hydrateVehicle = (data) => {
    for (var [key, value] of Object.entries(data)){
        if (document.getElementById('call_' + key)) {
            document.getElementById('call_' + key).value = value
        }
    }
    document.getElementById('switcher_add_call').innerHTML = '';
    const switchValues = [2,0,1]
    const switchInitVal = parseInt(data.vehicle_hasCome)
    const switcherAddCallVehicle = new Switch3(['non', '?', 'oui'], switchValues, 'switcher_add_call', switchInitVal,
        '', 'call_vehicle_hasCome');
    switcherAddCallVehicle.init();
}

const initVehicleAdders = (dataTotal) => {
    const buttons = document.getElementsByClassName('valid-vehicle');
    for (let i = 0; i<buttons.length; i++) {
        buttons[i].addEventListener('click', (e) => {
            const data = {
                'vehicle_id'              : e.target.dataset.id,
                'vehicle_immatriculation' : e.target.dataset.immatriculation,
                'vehicle_chassis'         : e.target.dataset.chassis,
                'vehicle_hasCome'         : e.target.dataset.hascome,
            }
            hydrateVehicle(data);
            alertForCalls(dataTotal);
        })
    }
}

const alertForCalls = (data) => {
    if (data.calls) {
        const modalClientPhone = document.getElementById('modal-callclient-phone');
        const modalForPhoneAlert = M.Modal.init(modalClientPhone);
        const tableForCalls = document.getElementById('calls-on-the-way-for-phone');
        document.getElementById('callclient-civility').innerHTML = data.client.client_civility
        document.getElementById('callclient-name').innerHTML = data.client.client_name
        document.getElementById('callclient-count').innerHTML = data.calls.length + ' appel';
        document.getElementById('callclient-count').innerHTML += data.calls.length > 1 ? 's' : ''
        document.getElementById('callclient-phone').innerHTML = data.client.client_phone

        tableForCalls.innerHTML = '';

        data.calls.forEach((call) => {
            let html = '<tr>' +
                '<td>' + call.call_subject + '</td>' +
                '<td>' + call.call_comment + '</td>' +
                '<td>' + call.call_date + '</td>' +
                '<td>' + call.call_hour + '</td>' +
                '<td>' + call.call_vehicule + '</td>' +
                '<td><a class="btn light-blue modal-close" href="/call/process/' + call.call_id + '/callback">Notifier dest.</a></td>' +
                '</tr>'
            tableForCalls.innerHTML = tableForCalls.innerHTML + html
        })
        modalForPhoneAlert.open()
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const labels = document.getElementsByTagName('label');
    for (let i = 0; i < labels.length; i++) {
        labels[i].classList.add('active');
    }
    const phoneNumberField = document.getElementById('call_client_phone');
    const modalVehicles = document.getElementById('modal-callclient-vehicles');
    const tableForVehicles = document.getElementById('client-vehicles-table');
    const modalForVehicles = M.Modal.init(modalVehicles);

    phoneNumberField.addEventListener('change', (e) => {
        const phoneNumber = phoneNumberField.value;
        sendData(phoneNumber, (data) => {
            data = JSON.parse(data);
            data = data[0];
            if (data.client) {
                hydrateForm(data.client)
                const reattribute = document.getElementById('reattribute');
                reattribute.dataset.client = data.client.client_id

                if (data.client.client_id) {
                    reattribute.classList.remove('hide');
                }

                if (data.client.vehicles.length <= 1) {
                    hydrateForm(data.client.vehicles[0])
                    alertForCalls(data.calls)
                } else {
                    tableForVehicles.innerHTML ='';
                    data.client.vehicles.forEach((vehicle) => {
                        let html = '<tr>' +
                            '<td>' + vehicle.vehicle_immatriculation + '</td>' +
                            '<td>' + vehicle.vehicle_chassis + '</td>' +
                            '<td><a class="btn light-blue valid-vehicle modal-close" data-immatriculation="' + vehicle.vehicle_immatriculation + '"' +
                            ' data-chassis="' + vehicle.vehicle_chassis + '" data-id="'+ vehicle.vehicle_id +'" data-hascome="'+ vehicle.vehicle_hasCome +'">valider</a></td>' +
                            '<td><a class="#"><i class="material-icons red-text">delete</i></a></td>' +
                            '</tr>'
                        tableForVehicles.innerHTML = tableForVehicles.innerHTML + html
                        initVehicleAdders(data);
                    })
                    modalForVehicles.open()
                }
            }
            if (data.client.vehicles.length === 1) {
                alertForCalls(data)
            }

        });
    });
})

