import { CallsCounter } from './CallsCounter';

const counter = new CallsCounter();

const deleteCall = (callId) => {
    const data = {
        call: callId
    }
    const params = {
        body: data,
        method: "delete"
    }
    fetch(`/call/${callId}`, params)
        .then(response => {
            if (response.status === 403) {
                M.toast({html: 'Vous n\'êtes pas autorisé à supprimer un appel', classes:'red'})
            }
            if (response.status === 200) {
                const json = response.json();
                return json
            }
        }).then(json => {
            document.getElementById(`call-${json.callId}`).classList.add('hide');
            M.toast({html: 'Appel supprimé', classes:'black'})
        })
}

document.addEventListener('DOMContentLoaded', () => {
    const callDeletors = document.getElementsByClassName('delete-call');

    for (let i = 0; i < callDeletors.length; i++) {

        callDeletors[i].addEventListener('click', (e)=> {
            e.preventDefault()
            const callId = callDeletors[i].dataset.call
            const status = callDeletors[i].dataset.status
            console.log(status);
            if (confirm('Etes vous sur de vouloir supprimer cet appel ?')) {
                deleteCall(callId)
                if(status === 'new') {
                    counter.updateTotalCallToProcess('dec')
                } else {
                    counter.updateTotalCallInProcess('dec')
                }
            }
        })

    }
})
