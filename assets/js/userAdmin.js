import { AjaxTools } from './clientFormAdmin';

document.addEventListener('DOMContentLoaded', () => {

    const searchModalElement = document.getElementById('modal-search-user')
    const modal = M.Modal.getInstance(searchModalElement)

    if(modal) {
        modal.options.onOpenEnd = () => {
            console.warn('open')
            document.getElementById('autocomplete-user-name').focus()
        }
    }
    const autocompleteUsers = document.getElementById('autocomplete-user-name');
    const userAjaxer = new AjaxTools('/user/new', '/user/liste');

    // Recherche user ///////////////////////////////////////////////////
    if (autocompleteUsers) {

        userAjaxer.getData((data) => {
                M.Autocomplete.init(
                    autocompleteUsers,
                    {
                        data: JSON.parse(data),
                    },
                );

        });

        autocompleteUsers.addEventListener('change', (e) => {
            const formZone = document.getElementById('form-edit-user');
            const user = e.target.value;
            formZone.innerHTML = '<a href="/user/#' + userAjaxer.getId(user) + '"><h6>' + userAjaxer.getName(user) + '</h6></a>'
        });
    }
});
