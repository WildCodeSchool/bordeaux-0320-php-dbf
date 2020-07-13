const city = document.getElementById('user_city');
const concession = document.getElementById('user_concession');
city.addEventListener('change', (event) => {
    const field = event.target;
    const form = field.closest('form');
    const data = {};
    data[field.name] = field.value;


    fetch(form.action, {
        method: 'POST',
        mode: 'same-origin',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
        },
    }).then((response) => {
        return response.json();
        debugger;
    });/** .then((json) => {
        action(json)
    })* */
});
