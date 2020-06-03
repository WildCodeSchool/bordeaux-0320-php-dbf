function ajaxPoster(url, data) {
    const thisClass = this;
    fetch(url, {
        method: 'POST',
        mode: "same-origin",
        credentials: "same-origin",
        body: JSON.stringify(data),
        headers: {
            'Content-Type': 'application/json'
        },
    })
        .then(function (response) {
            M.toast({ html: 'Client ajouté à la base de données' });
        });
}


document.addEventListener('DOMContentLoaded', () => {

    const button = document.getElementById('client-button');

    button.addEventListener('click', (e) => {
        e.preventDefault();
        const civility = document.getElementById('client_civility').value;
        const name = document.getElementById('client_name').value;
        const phone = document.getElementById('client_phone').value;
        const phone2 = document.getElementById('client_phone2').value;
        const email = document.getElementById('client_email').value;
        const postcode = document.getElementById('client_postcode').value;

        const data = {
            'civility': civility,
            'name': name,
            'phone': phone,
            'phone2': phone2,
            'email': email,
            'postcode': postcode
        }
        ajaxPoster('/admin/addclient', data);

    });

});
