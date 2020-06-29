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
    /*
    Etape 1 mettre une classe "delete-{entité}-button sur les liens de suppression dans l'index dans le href ligne 24
    Etape 2 mettre un attributs data-target = '{{entity.id}} sur les liens de suppression dans l'index dans le href ligne 24
    Etape 3 mettre un attribut id = 'entity-line-{{entity.id}} sur le tr contenant le lien (ligne 16 subject/index)
    Etape 4 mettre un attribut id = 'entity-selector sur le select (_subjects.html.twig ligne 17)
    Etape 5 copier-coller les lignes ci-dessous en remplaçant le deleteSubjectsButtons
     */
    const deleteSubjectsButtons = document.getElementsByClassName('delete-subject-button');
    for (let i = 0; i < deleteSubjectsButtons.length; i++) {
        deleteSubjectsButtons[i].addEventListener('click', (e) => {
            e.preventDefault();
            deletor(deleteSubjectsButtons[i], '/subject/delete/', 'subject', 'motif')
        });
    }
    const deleteCommentButtons = document.getElementsByClassName('delete-comment-button');
    for (let i = 0; i < deleteCommentButtons.length; i++) {
        deleteCommentButtons[i].addEventListener('click', (e) => {
            e.preventDefault();
            deletor(deleteCommentButtons[i], '/comment/delete/', 'comment', 'commentaire')
        });
    }

}) //end of document eventListener
