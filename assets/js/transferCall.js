import { transferTool } from './recipientTransfer';

const hideUsersList = () => {
    const list = document.getElementsByClassName('list-dest');
    for (let i = 0; i < list.length; i++) {
        list[i].classList.add('hide');
    }
}

const closeList = (target) => {
    document.getElementById(target).classList.add('hide');
}

const updateTotalCallToProcess = (method = 'add') => {
    const counter = document.getElementById('nb-calls-to-process');
    let total = parseInt(counter.innerHTML);
    if (method === 'dec') {
        total--
    } else {
        total++
    }
    counter.innerHTML = total
}

const updateTotalCallInProcess = (method = 'add') => {
    const counter = document.getElementById('nb-calls-in-process');
    let total = parseInt(counter.innerHTML);
    if (method === 'dec') {
        total--
    } else {
        total++
    }
    counter.innerHTML = total;
}

const transferCall = (callId, userId) => {
                    const url    = `/call/process/${callId}/transferto/${userId}`;
                    const params = {
                        method: "get"
                    }
                    fetch(url, params)
                        .then(response => {
                            if(response.status === 202) {
                                const callLine = document.getElementById(`call-${callId}`)
                                const status = callLine.dataset.status
                                if (status === 'new') {
                                    updateTotalCallToProcess('dec')
                                } else {
                                    updateTotalCallInProcess('dec')
                                }
                                M.toast({html: 'Appel transféré', classes:'red'})
                                document.getElementById(`users-in-service-${callId}`).classList.add('hide');
                                callLine.remove();
                            }
                        })
                }


document.addEventListener('DOMContentLoaded', () => {

    const closers= document.getElementsByClassName('close-list');
    for (let i = 0; i<closers.length; i++) {
        closers[i].addEventListener('click', (e) => {
            e.preventDefault()
            const target = closers[i].getAttribute('data-target');
            closeList(target)
        })
    }

    const recipientsList = document.getElementsByClassName('recipient-changer');
    for (let i = 0; i<recipientsList.length; i++) {
        recipientsList[i].addEventListener('click', (e) => {
            e.preventDefault()
            hideUsersList()
            const callId = e.target.dataset.call
            document.getElementById(`users-in-service-${callId}`).classList.remove('hide')
        })
    }

    const recipients = document.getElementsByClassName('recipients-to-transfer');
    for (let i = 0; i<recipients.length; i++) {
        recipients[i].addEventListener('click', (e) => {
            e.preventDefault();
            transferCall(e.target.dataset.call, e.target.dataset.for)
        })
    }

})
