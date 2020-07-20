const initializeSelects = () => {
    const selects = document.querySelectorAll('select');
    M.FormSelect.init(selects);
}

const lineHider = (className) => {
    const lines = document.getElementsByClassName(className);
    for (let i = 0; i < lines.length; i++) {
        lines[i].classList.add('hide');
    }
};

const btnDesactivator = (className) => {
    const buttons = document.getElementsByClassName(className);
    for (let i = 0; i < buttons.length; i++) {
        buttons[i].classList.remove('btn-floating', 'light-blue');
    }
};

const editor = (button, url, classLine, classBtn, entity) => {
    lineHider(classLine);
    btnDesactivator(classBtn);
    button.classList.add('btn-floating', 'light-blue');
    fetch(url, {
        method: 'GET',
    }).then((response) => {
        return response.text();
    }).then((html) => {
        const cell = document.getElementById(`${entity}-td-edit-${button.dataset.target}`);
        cell.innerHTML = html;
        initializeSelects();
    });
    const editLine = document.getElementById(`${entity}-line-edit-${button.dataset.target}`);
    editLine.classList.remove('hide');
};

const editorAction = (collection, classLine, classBtn, entity) => {
    for (let i = 0; i < collection.length; i++) {
        collection[i].addEventListener('click', (event) => {
            editor(collection[i], `/${entity}/${collection[i].dataset.target}/edit`, classLine, classBtn, entity)
        });
    }
}

document.addEventListener('DOMContentLoaded',() => {
    //edition des subjects
    const editorSubjectButtons = document.getElementsByClassName('edit-subject-button');
    editorAction(editorSubjectButtons, 'editor-line', 'edit-subject-button', 'subject')

    // fin edition subjects
    const editorCivilityButtons = document.getElementsByClassName('edit-civility-button');
    editorAction(editorCivilityButtons, 'editor-line', 'edit-civility-button', 'civility')

    const editorCommentButtons = document.getElementsByClassName('edit-comment-button');
    editorAction(editorCommentButtons, 'editor-line', 'edit-comment-button', 'comment')

    const editorCityButtons = document.getElementsByClassName('edit-city-button');
    editorAction(editorCityButtons, 'editor-line', 'edit-city-button', 'city');

    const editorConcessionButtons = document.getElementsByClassName('edit-concession-button');
    editorAction(editorConcessionButtons, 'editor-line', 'edit-concession-button', 'concession');

    const editorServiceButtons = document.getElementsByClassName('edit-service-button');
    editorAction(editorServiceButtons, 'editor-line', 'edit-service-button', 'service');

}) //end of document
