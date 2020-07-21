export class AjaxTools {
    constructor(urlToAdd, urlToShow) {
        this.urlToAdd     = urlToAdd
        this.urlToShow    = urlToShow
    }

    sendData(data, action) {
        fetch(this.urlToAdd, {
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
        fetch(this.urlToShow, {
            method      : 'POST',
        })
            .then((response) => {
                return response.text();
            })
            .then((data) => {
                action (data)
            });
    }

    getName(client) {
        const elements   = client.split('(');
        return elements[0];
    }

    getId(client) {
        const elements   = client.split('(');
        const id           = elements[1].split(')');
        return id[0];
    }
}


document.addEventListener('DOMContentLoaded', () => {
    const autocompleteClients  = document.getElementById('autocomplete-client-name');
    const clientAjaxer         = new AjaxTools('/admin/addclient', '/admin/client/list');

// Edition client ///////////////////////////////////////////////////
    if(autocompleteClients) {
        clientAjaxer.getData((data) => {
            M.Autocomplete.init(autocompleteClients,
                {
                    data: JSON.parse(data)
                }
            )
        });

        autocompleteClients.addEventListener('change', (e) => {
            const formZone = document.getElementById('form-edit-client');
            const client = e.target.value
            formZone.innerHTML = '<h6>' + clientAjaxer.getName(client) +
                ' <span class="secondary-content">ID : ' + clientAjaxer.getId(client) +
                ' </span></h6>'
        })
    }

// Ajout client ///////////////////////////////////////////////////
    const button = document.getElementById('client-button');
    if(button) {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const civility = document.getElementById('client_civility');
            const name = document.getElementById('client_name');
            const phone = document.getElementById('client_phone');
            const phone2 = document.getElementById('client_phone2');
            const email = document.getElementById('client_email');
            const postcode = document.getElementById('client_postcode');

            const data = {
                'civility': civility.value,
                'name': name.value,
                'phone': phone.value,
                'phone2': phone2.value,
                'email': email.value,
                'postcode': postcode.value
            }

            clientAjaxer.sendData(data, () => {
                M.toast({ html: 'Client ajouté à la base de données' });
                clientAjaxer.getData((data) => {
                    M.Autocomplete.init(autocompleteClients, {
                        data: JSON.parse(data)
                    });
                });
            })
            name.value = '';
            phone.value = '';
            phone2.value = '';
            email.value = '';
            postcode.value = '';
        });
    }
});
