import { AjaxTools } from './clientFormAdmin';

document.addEventListener('DOMContentLoaded', () => {
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
