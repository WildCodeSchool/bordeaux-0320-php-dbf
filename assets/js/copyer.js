setClipboard = (value) => {
    let tempInput = document.createElement("input");
    tempInput.style = "position: absolute; left: -4000px; top: -4000px";
    tempInput.value = value;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
}

document.addEventListener('DOMContentLoaded', () => {
    const copyers = document.getElementsByClassName('copy-tooltip');
    for (let i = 0; i < copyers.length; i++) {
        copyers[i].addEventListener('click', (e)=> {
            const mail = copyers[i].getAttribute('data-email')
            setClipboard(mail);
            M.toast({html:'Email copié', classes:'cyan'})
        })
    }
})

