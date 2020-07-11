class Collapsor {
    constructor() {
        this.classToCollapse = 'collapsor';
        this.init();
    }

    init() {
        this.collapsors = document.getElementsByClassName(this.classToCollapse);
        this.unCollapsors = document.getElementsByClassName(this.classToUncollapse);
        for (let i = 0; i < this.collapsors.length; i++) {
            this.collapsors[i].addEventListener('click', (event) => {
                if (this.collapsors[i].classList.contains('active')) {
                    this.hideChildren(this.collapsors[i])
                } else {
                    this.showChildren(this.collapsors[i])
                }
            })
        }
    }

    getAllChildren(elem) {
        const type = elem.dataset.type;
        const typeName = elem.dataset.collapse;
        const children = document.querySelectorAll("[data-" + type + "='" + typeName + "']");
        return children;
    }

    showChildren(row) {
        const children = document.getElementsByClassName(row.getAttribute('data-collapse'));
        if (children.length>0) {
            row.classList.add('active')
            for (let i = 0; i < children.length; i++) {
                const child = children[i];
                child.classList.remove('hide');
            }
        } else {
            M.toast({html:'Rien à afficher'});
        }
    }

    hideChildren(row) {
        row.classList.remove('active')
        const children = this.getAllChildren(row);
        for (let i = 0; i < children.length; i++) {
            const child = children[i];
            child.classList.remove('active');
            child.classList.add('hide');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const collapsor = new Collapsor();
})
