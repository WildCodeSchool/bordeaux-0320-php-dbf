function sendData(url, data, action) {

    fetch(url, {
        method      : 'POST',
        mode        : "same-origin",
        credentials : "same-origin",
        body        : JSON.stringify(data),
        headers     : {
            'Content-Type': 'application/json'
        },
    })
        .then(function (response) {
            return response.text();
        }).then(function (html) {
        action(html);
    });
}

const citySelect = document.getElementById('call_city');
const ConcessionSelect = document.getElementById('call_concession');

citySelect.addEventListener('change', (e) => {

    const form   = citySelect.form
    const url    =  '/call/add';
    var data     = {};
    data[e.target.label] = e.target.value;

    sendData(url, data, (html) => {
        console.log(html)
        // On remplace le select concession d'origine
        // Par le select contenu dans la réponse ajax
        // EN JQuery :
        /*
        $('#call_concession').replaceWith(
                // ... with the returned one from the AJAX response.
                $(html).find('#call_concession')
            );
         */
    });
});
