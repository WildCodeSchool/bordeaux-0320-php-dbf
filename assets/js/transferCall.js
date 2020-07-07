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

const transferCall = (callId, userId) => {
                    const url    = `/call/process/${callId}/transferto/${userId}`;
                    const params = {
                        method: "get"
                    }
                    fetch(url, params)
                        .then(response => {
                            console.log(response.status)
                            if(response.status === 202) {
                                M.toast({html: 'Appel transféré', classes:'red'})
                                document.getElementById(`users-in-service-${callId}`).classList.add('hide');
                                document.getElementById(`call-${callId}`).remove();
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
