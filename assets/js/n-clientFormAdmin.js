class AjaxTools {
    constructor(url) {
        this.url     = url
    }

    sendData(data, action) {
        const thisClass = this
        fetch(this.url, {
            method      : 'POST',
            mode        : "same-origin",
            credentials : "same-origin",
            body        : JSON.stringify(data),
            headers     : {
                'Content-Type': 'application/json'
            },
        })
            .then(function (response) {
               action()
            });
    }

    getData(action) {
        fetch(this.url, {
            method      : 'POST',
        })
            .then((response) => {
                return response.text();
            })
            .then((data) => {
                action (data)
            });
    }
}


document.addEventListener('DOMContentLoaded', () => {

// Edition client ///////////////////////////////////////////////////
    const autocompleteClients  = document.getElementById('autocomplete-client-name');
    const clientGetter = new AjaxTools('/admin/client/list');
    clientGetter.getData((data) => {
        M.Autocomplete.init(autocompleteClients,
    {
                data : JSON.parse(data);
            }
        )
    });

    autocompleteClients.addEventListener('change', (e) => {
        const formZone   = document.getElementById('form-edit-client');
        const client     = e.target.value
        const elements   = client.split('(');
        const clientName = elements[0];
        let id           = elements[1].split(')');
        id               = id[0];
        formZone.innerHTML = '<h6>' + clientName + ' <span class="secondary-content">ID : ' + id + ' </span></h6>'
    })


// Ajout client ///////////////////////////////////////////////////
    const button = document.getElementById('client-button');

    button.addEventListener('click', (e) => {
        e.preventDefault();
        const civility = document.getElementById('client_civility');
        const name     = document.getElementById('client_name');
        const phone    = document.getElementById('client_phone');
        const phone2   = document.getElementById('client_phone2');
        const email    = document.getElementById('client_email');
        const postcode = document.getElementById('client_postcode');

        const data = {
            'civility': civility.value,
            'name'    : name.value,
            'phone'   : phone.value,
            'phone2'  : phone2.value,
            'email'   : email.value,
            'postcode': postcode.value
        }
        const adder = new AjaxTools('/admin/addclient');
        adder.sendData(data, () => {
            M.toast({ html: 'Client ajouté à la base de données' });
            clientGetter.getData((data) => {
                autocompletorClients = M.Autocomplete.init(autocompleteClients, {
                    data : JSON.parse(data)
                });
            });
        })
        name.value = ''; phone.value = ''; phone2.value = ''; email.value = ''; postcode.value = '';
    });
});
