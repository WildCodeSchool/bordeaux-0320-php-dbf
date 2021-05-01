const initializeSelects = () => {
    const selects = document.querySelectorAll('select');
    M.FormSelect.init(selects);
}
const removeValueInSelect = (selector, selectorValue) => {
    if (selector.querySelector('option[value="' + selectorValue + '"]')) {
        selector.querySelector('option[value="' + selectorValue + '"]').remove();
    }
}
const deletor = (button, urlPrefix, name, nameFr) => {
    const objectId = button.getAttribute('data-target');
    const objectContainer = document.getElementById(`${name}-line-${objectId}`);
    const route = `${urlPrefix}${objectId}`;
    if (confirm(`êtes vous sûr de vouloir supprimer le ${nameFr} n° ${objectId}`)) {
        fetch(route, {
            method: 'DELETE',
        })
            .then((response) => {
                return response.status;
            })
            .then((status) => {
                if (status === 204) {
                    objectContainer.remove();
                    M.toast({html:'Suppression effectuée', classes:'cyan'});
                    removeValueInSelect(document.getElementById(`${name}-selector`), objectId)
                    initializeSelects();
                } else {
                    M.toast({html:'Opération impossible', classes:'red'});
                }
            });
    }
};

const deletorAction = (collection, route, name, nameFr) => {
    for (let i = 0; i < collection.length; i++) {
        collection[i].addEventListener('click', (e) => {
            e.preventDefault();
            deletor(collection[i], route, name, nameFr);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const deleteSubjectsButtons = document.getElementsByClassName('delete-subject-button');
    deletorAction(deleteSubjectsButtons, '/subject/delete/', 'subject', 'motif')

    const deleteCommentButtons = document.getElementsByClassName('delete-comment-button');
    deletorAction(deleteCommentButtons, '/comment/delete/', 'comment', 'commentaire')

    const deleteCivilityButtons = document.getElementsByClassName('delete-civility-button');
    deletorAction(deleteCivilityButtons, '/civility/delete/', 'civility', 'civilité')

    const deleteCityButtons = document.getElementsByClassName('delete-city-button');
    deletorAction(deleteCityButtons, '/city/delete/', 'city', 'plaque')

    const deleteConcessionButtons = document.getElementsByClassName('delete-concession-button');
    deletorAction(deleteConcessionButtons, '/concession/delete/', 'concession', 'concession')

    const deleteServiceButtons = document.getElementsByClassName('delete-service-button');
    deletorAction(deleteServiceButtons, '/service/delete/', 'service', 'service')

}) //end of document eventListener
