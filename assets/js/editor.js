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
    const btns = document.getElementsByClassName(className);
    for (let i = 0; i < btns.length; i++) {
        btns[i].classList.remove('btn-floating', 'light-blue');
    }
};

const editor = (button, url, classLine, classBtn) => {
    lineHider(classLine);
    btnDesactivator(classBtn)
    button.classList.add('btn-floating', 'light-blue');
    fetch(url, {
        method:'GET',
    }).then((response) => {
        return response.text();
    }).then((html) => {
        const cell = document.getElementById(`subject-td-edit-${button.dataset.target}`);
        cell.innerHTML = html;
        initializeSelects();
    });
    const editLine = document.getElementById(`subject-line-edit-${button.dataset.target}`);
    editLine.classList.remove('hide');
};

document.addEventListener('DOMContentLoaded',() => {
    //edition des subjects
    const editorSubjectButtons = document.getElementsByClassName('edit-subject-button');
    for (let i = 0; i < editorSubjectButtons.length; i++) {
        editorSubjectButtons[i].addEventListener('click', (event) => {
            editor(editorSubjectButtons[i], `/subject/${editorSubjectButtons[i].dataset.target}/edit`, 'editor-line', 'edit-subject-button')
        });
    }
    // fin edition subjects

}) //end of document
