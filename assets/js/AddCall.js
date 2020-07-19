import { Switch3 } from 'triswitch';

document.addEventListener('DOMContentLoaded', () => {
    if(document.getElementById('switcher_add_call')) {
        const switcherAddCall = new Switch3(['non', '?', 'oui'], [2, 0, 1], 'switcher_add_call', 0,
            '', 'call_vehicle_hasCome');
        switcherAddCall.init();
    }

    const copyers = document.getElementsByClassName('copyer');
    for (let i = 0; i < copyers.length; i++) {
        copyers[i].addEventListener('click', (e)=> {
            const dataCopy = copyers[i].getAttribute('data-copy')
            const target   = document.getElementById(dataCopy)
            target.select()
            document.execCommand('copy')
            M.toast({html:'Elément copié', classes:'light-blue'})
        })
    }

    const addCallBtn = document.getElementById('call-add-btn')
    const addCallLoader = document.getElementById('call-add-loader')
    if(addCallBtn) {
        addCallBtn.addEventListener('click', () => {
            addCallLoader.classList.remove('hide')
        })
    }
});
