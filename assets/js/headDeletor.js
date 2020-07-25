 document.addEventListener('DOMContentLoaded', () => {
    const deletors = document.getElementsByClassName('head-deletor');
    for (let i = 0; i < deletors.length; i++) {
        const deletor = deletors[i];
        const form = document.getElementById(deletor.dataset.target)
        deletor.addEventListener('click', () => {
            if (confirm('êtes vous sur de vouloir supprimer cette responsabilité ?')) {
                deletor.parentElement.innerHTML = '<div class="preloader-wrapper small active">\n' +
                    '    <div class="spinner-layer spinner-blue-only">\n' +
                    '      <div class="circle-clipper left">\n' +
                    '        <div class="circle"></div>\n' +
                    '      </div><div class="gap-patch">\n' +
                    '        <div class="circle"></div>\n' +
                    '      </div><div class="circle-clipper right">\n' +
                    '        <div class="circle"></div>\n' +
                    '      </div>\n' +
                    '    </div>\n' +
                    '  </div>'
                form.submit()
            }
        })
    }
})
