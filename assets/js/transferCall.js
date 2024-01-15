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
    if(counter) {
        let total = parseInt(counter.innerHTML);
        if (method === 'dec') {
            total--
        } else {
            total++
        }
        counter.innerHTML = total
    }
}

const updateTotalCallInProcess = (method = 'add') => {
    const counter = document.getElementById('nb-calls-in-process');
    if(counter) {
        let total = parseInt(counter.innerHTML);
        if (method === 'dec') {
            total--
        } else {
            total++
        }
        counter.innerHTML = total;
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
            ev.slider = target
            document.dispatchEvent(ev)
            M.toast({html: 'Rechargement du volet terminé', classes:'light-green'})
        })
    }
}

const  transferCall =  (callId, userId, callback) => {
                    M.toast({html: 'Transfert en cours', classes:'orange'})
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
                                M.toast({html: 'Appel transféré', classes:'green'})
                                document.getElementById(`users-in-service-${callId}`).classList.add('hide');
                                callLine.classList.add('hide');
                                callback()
                            }
                            return false
                        })
                }

                const init = (sourceElement = null) => {

                    const source = sourceElement ?? document

                    const closers= source.getElementsByClassName('close-list');
                    for (let i = 0; i<closers.length; i++) {
                            closers[i].addEventListener('click', (e) => {
                                e.preventDefault()
                                const target = closers[i].getAttribute('data-target');
                                closeList(target)
                            })

                    }

                    const recipientsList = source.getElementsByClassName('recipient-changer');
                    for (let i = 0; i<recipientsList.length; i++) {

                            recipientsList[i].addEventListener('click', (e) => {
                                e.preventDefault()
                                hideUsersList()
                                const callId = e.target.dataset.call
                                document.getElementById(`users-in-service-${callId}`).classList.remove('hide')
                            })
                    }

                    const recipients = source.getElementsByClassName('recipients-to-transfer');
                    for (let i = 0; i<recipients.length; i++) {
                            recipients[i].addEventListener('click', (e) => {
                                e.preventDefault();

                                transferCall(e.target.dataset.call, e.target.dataset.for,  () => {
                                    const isInSlider = recipients[i].dataset.inSlider && recipients[i].dataset.inSlider === '1'
                                    const service = recipients[i].dataset.service
                                    if (isInSlider) {
                                        refreshSlider(service)
                                    }
                                })


                            })
                    }
                }
document.addEventListener('DOMContentLoaded', () => {
    init()
})
document.addEventListener('sliderIsOpen', (ev) => {
    init(ev.slider)
})

