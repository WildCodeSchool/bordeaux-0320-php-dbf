const initButtons = (modal) => {
    const buttons = document.getElementsByClassName('call-treatment-btn');
    const modalHtmlZone = document.getElementById('modal-content-call-treatment');
    for (let i=0; i<buttons.length; i++) {
        buttons[i].addEventListener('click', (e) => {
            e.preventDefault();
            const callId = buttons[i].dataset.call;
            getProcessForm(callId, (html) => {
                modalHtmlZone.innerHTML = html
                initializeSelects()
            })
            modal.open();
        })
    }
}

const getProcessForm = (callId, action) => {

    fetch('/call/process/' + callId, {
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

const initializeSelects = () => {
    const selects = document.querySelectorAll('select');
    const instancesOfSelects = M.FormSelect.init(selects, {});
}

document.addEventListener('DOMContentLoaded', ()=> {
    const modalCallTreatment         = document.getElementById('modal-call-treatment');
    const modalCallTreatmentInstance = M.Modal.init(modalCallTreatment, {});
    initButtons(modalCallTreatmentInstance);
})
