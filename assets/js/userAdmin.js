import { AjaxTools } from './clientFormAdmin';

document.addEventListener('DOMContentLoaded', () => {
    const autocompleteUsers = document.getElementById('autocomplete-user-name');
    const userAjaxer = new AjaxTools('/user/new', '/user/');

    // Recherche user ///////////////////////////////////////////////////
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
        console.log(userAjaxer.getName(user));
        formZone.innerHTML = '<h6>' + userAjaxer.getName(user) +
            ' <span class="secondary-content">ID : ' + userAjaxer.getId(user) +
            ' </span></h6>'
    });
});
