import { transferTool } from './recipientTransfer';
import {CallsCounter} from './CallsCounter';

const counter = new CallsCounter();

let url = '/newcallsforuser';
if (document.getElementById('supervisedUser')) {
    url = url + '/' + document.getElementById('supervisedUser').value
}

const getNewCalls = (action) => {
        fetch(url, {
        })
        .then(function (response) {
            if (response.status === 200) {
               return response.text();
            }
        }).then(html => {
            action(html)
        })
}

const isRouteWelcome = () => {
    return (window.location.href.indexOf('welcome') != -1)
}

const initButtons = (modal) => {
    const buttons = document.getElementsByClassName('call-treatment-btn');
    const modalHtmlZone = document.getElementById('modal-content-call-treatment');
    for (let i=0; i<buttons.length; i++) {
        buttons[i].addEventListener('click', (e) => {
            e.preventDefault();
            const isInSlider = buttons[i].dataset.inSlider && buttons[i].dataset.inSlider === '1'
            const callId = buttons[i].dataset.call;
            modalHtmlZone.innerHTML = ' ... chargement'
            getProcessForm(callId, (html) => {
                modalHtmlZone.innerHTML = html
                initializeSelects()

                //Show/Hide switch Button
                const contactTypeSelector = document.getElementById('call_processing_contactType');
                const switchLine          = document.getElementById('rdv-switch');
                contactTypeSelector.addEventListener('change', (e) => {
                    if(contactTypeSelector.querySelector('option[value="' + e.target.value + '"]').innerText === 'Contact établi') {
                        switchLine.classList.remove('hide')
                    } else {
                        switchLine.classList.add('hide')
                    }
                })

                //Show/Hide Appointment Date
                const appointmentSwitch = document.getElementById('call_processing_isAppointmentTaken');
                appointmentSwitch.addEventListener('change', (e)=>{
                    if (e.target.checked) {
                        document.getElementById('appointment_date').classList.remove('hide')
                    } else {
                        document.getElementById('appointment_date').classList.add('hide')
                    }
                })

                const processBtn = document.getElementById('process-call-btn');
                const processForm        = document.getElementById('form-process');
                processForm.onsubmit = (e) => {
                    e.preventDefault();
                    document.getElementById('process-preloader').classList.remove('hide');
                    const data   = new FormData(e.target);
                    const callId = processBtn.dataset.call;
                    const url    = `/call/process/${callId}/add`;
                    const params = {
                        body: data,
                        method: "post"
                    }

                    fetch(url, params)
                        .then(response => {
                            return response.json()
                        }).then(data => {
                        document.getElementById('process-preloader').classList.add('hide');
                        modal.close();
                        M.toast({html: 'Traitement enregistré', classes:'light-blue'})

                        if (data.is_ended && !isInSlider){
                            document.getElementById(`call-${data.callId}`).classList.add('hide')
                            if (isRouteWelcome() && counter) {
                                counter.updateTotalCallToProcess('dec');
                            } else {
                                if(counter) {
                                    counter.updateTotalCallInProcess('dec');
                                }
                            }
                        } else {
                            if(!isInSlider) {
                                changeCallStatus(data.callId, data.colors.class);
                                const target = document.getElementById('call-history-' + data.callId);
                                const callLine = document.getElementById('call-' + data.callId);

                                const notification = document.getElementById(`client-callback-${data.callId}`);
                                if (notification && !notification.classList.contains('hide')) {
                                    notification.classList.add('hide')
                                }
                                if (isRouteWelcome()) {
                                    callLine.classList.add('hide');
                                    if (counter) {
                                        counter.updateTotalCallToProcess('dec');
                                        counter.updateTotalCallInProcess();
                                    }
                                } else {
                                    const targetHtml = '<span class="chip ' + data.colors.bgColor + ' black-text">' + data.colors.stepName + '</span>\n' +
                                        '                            <b>\n' +
                                        '                                ' + data.date + '\n' +
                                        '                                à ' + data.time + '\n' +
                                        '                            </b>';
                                    target.innerHTML = targetHtml;
                                }
                                return
                            } else {
                                refreshSlider(buttons[i].dataset.service)
                            }
                        }
                    });
                }
            })
            modal.open();
        })
    }
}

const changeCallStatus = (callId, newClass) => {
    const calHead = document.getElementById(`cal-head-${callId}`)
    const calBtn = document.getElementById(`treatment-btn-${callId}`)
    const urgentText = document.getElementById(`recall-urgent-${callId}`)
    calBtn.classList.remove('urgent');
    calBtn.classList.add('grey', 'darken-3');

    if (urgentText) {
        urgentText.classList.remove('red-text');
        urgentText.classList.add('grey-text', 'text-darken-4');
        urgentText.innerHTML = ' X ';
    }

    calHead.classList.remove('urgent');
    calHead.classList.add(newClass);
}

const initTransferButtons = (modal) => {
    const transferButtons = document.getElementsByClassName('call-transfer-btn');
    const transferModalHtmlZone = document.getElementById('modal-content-call-transfer');
    for (let i=0; i<transferButtons.length; i++) {
        transferButtons[i].addEventListener('click', (e) => {
            e.preventDefault();
            const isInSlider = transferButtons[i].dataset.inSlider && transferButtons[i].dataset.inSlider === '1'
            const callId = transferButtons[i].dataset.call;
            transferModalHtmlZone.innerHTML = ' ... chargement'
            getTransferForm(callId, (html) => {
                transferModalHtmlZone.innerHTML = html;
                const clientAjaxer = new transferTool(`/call/process/${callId}/transfer`, '');
                clientAjaxer.init();
                initializeSelects();
                const transferBtn = document.getElementById('transfer-call-btn');
                const form        = document.getElementById('form-transfer');
                form.onsubmit = (e) => {
                    e.preventDefault();
                    document.getElementById('transfer-preloader').classList.remove('hide');
                    const data   = new FormData(e.target);
                    const callId = transferBtn.dataset.call;
                    const url    = `/call/process/${callId}/dotransfer`;
                    const params = {
                        body: data,
                        method: "post"
                    }

                    fetch(url, params)
                        .then(response => {
                            return response.json()
                        }).then(data => {
                            if(!isInSlider) {
                                const callLine = document.getElementById('call-' + data.callId);
                                const status = callLine.dataset.status;
                                if (status === 'new' && counter) {
                                    counter.updateTotalCallToProcess('dec');
                                } else {
                                    if (counter) {
                                        counter.updateTotalCallInProcess('dec');
                                    }
                                }
                            }
                        document.getElementById('transfer-preloader').classList.add('hide');
                        modal.close();
                        M.toast({html: 'Appel transféré', classes:'red'})
                        if(!isInSlider) {
                            callLine.classList.add('hide');
                        } else {
                            refreshSlider(transferButtons[i].dataset.service)
                        }
                    });
                }
            })

            modal.open();
        })
    }
}

const refreshSlider = (service) => {
    const target = document.getElementById('slide-out-' + service)
    if(target) {
        M.toast({html: 'Rechargement du volet en cours', classes:'orange'})

        fetch('/head/supervision/' + service, {

        }).then(response => {
            return response.text()
        }).then(html => {
            target.innerHTML = html
            const ev = new CustomEvent('sliderIsOpen')
            document.dispatchEvent(ev)
            M.toast({html: 'Rechargement du volet terminé', classes:'light-green'})
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

const getTransferForm = (callId, action) => {
    fetch('/call/process/' + callId + '/transfer', {
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
    M.FormSelect.init(selects, {});
}

const clientCallbacks = () => {
    fetch('/callbacksforuser')
        .then(response => {
            if (response.status === 200) {
                return response.json()
            }
        })
        .then(json => {
            for (var [key, value] of Object.entries(json)){
                const notification = document.getElementById(key);
                if (notification) {
                    notification.innerHTML = value
                    if (notification.classList.contains('hide') && value != 0) {
                        notification.classList.remove('hide')
                    } else if (!notification.classList.contains('hide') && value === 0) {
                        notification.classList.add('hide')
                    }
                }
            }
        })
}

const start = () => {
    const modalCallTreatment         = document.getElementById('modal-call-treatment');
    const modalCallTreatmentInstance = M.Modal.init(modalCallTreatment, {});
    initButtons(modalCallTreatmentInstance);

    const modalCallTransfer         = document.getElementById('modal-call-transfer');
    const modalCallTransferInstance = M.Modal.init(modalCallTransfer, {});
    initTransferButtons(modalCallTransferInstance);

    window.setInterval(()=> {
        getNewCalls(html => {
            if(html) {
                const icon = '<i class="material-icons left white-text">notifications</i>'
                const text = 'Vous avez un nouvel appel à traiter'
                M.toast({html: `${icon} ${text}`, classes: 'light-blue darken-3', displayLength:10000})
                const listOfCallsZone = document.getElementById('list-calls-to-process')
                if(counter) {
                    counter.updateTotalCallToProcess();
                }
                //TODO Insert row at the good place not a the end
                listOfCallsZone.innerHTML += html
                initButtons(modalCallTreatmentInstance);
            }
        })
        clientCallbacks()

    }, 15000);
}
document.addEventListener('DOMContentLoaded', () => {
    start()
})
document.addEventListener('sliderIsOpen', () => {
    start()
})