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
                if (status === 200) {
                    objectContainer.remove();
                    removeValueInSelect(document.getElementById(`${name}-selector`), objectId)
                    initializeSelects();
                }
            });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const deleteSubjectsButtons = document.getElementsByClassName('delete-subject-button');
    for (let i = 0; i < deleteSubjectsButtons.length; i++) {
        deleteSubjectsButtons[i].addEventListener('click', (e) => {
            e.preventDefault();
            deletor(deleteSubjectsButtons[i], '/subject/delete/', 'subject', 'motif');
        });
    }
    const deleteCommentButtons = document.getElementsByClassName('delete-comment-button');
    for (let i = 0; i < deleteCommentButtons.length; i++) {
        deleteCommentButtons[i].addEventListener('click', (e) => {
            e.preventDefault();
            deletor(deleteCommentButtons[i], '/comment/delete/', 'comment', 'commentaire');
        });
    }
    const deleteCivilityButtons = document.getElementsByClassName('delete-civility-button');
    for (let i = 0; i < deleteCivilityButtons.length; i++) {
        deleteCivilityButtons[i].addEventListener('click', (e) => {
            e.preventDefault();
            deletor(deleteCivilityButtons[i], '/civility/delete/', 'civility', 'civilité');
        });
    }
    const deleteCityButtons = document.getElementsByClassName('delete-city-button');
    for (let i = 0; i < deleteCityButtons.length; i++) {
        deleteCityButtons[i].addEventListener('click', (e) => {
            e.preventDefault();
            deletor(deleteCityButtons[i], '/city/delete/', 'city', 'plaque');
        });
    }
    const deleteConcessionButtons = document.getElementsByClassName('delete-concession-button');
    for (let i = 0; i < deleteConcessionButtons.length; i++) {
        deleteConcessionButtons[i].addEventListener('click', (e) => {
            e.preventDefault();
            deletor(deleteConcessionButtons[i], '/concession/delete/', 'concession', 'concession');
        });
    }
    const deleteServiceButtons = document.getElementsByClassName('delete-service-button');
    for (let i = 0; i < deleteServiceButtons.length; i++) {
        deleteServiceButtons[i].addEventListener('click', (e) => {
            e.preventDefault();
            deletor(deleteServiceButtons[i], '/service/delete/', 'service', 'service');
        });
    }

}) //end of document eventListener
