class Collapsor {
    constructor(className) {
        this.classToCollapse = className;
    }

    init() {
        this.collapsors = document.getElementsByClassName(this.classToCollapse);
        for (let i = 0; i < this.collapsors.length; i++) {
            this.collapsors[i].addEventListener('click', (event) => {
                event.preventDefault();
                if (this.collapsors[i].classList.contains('active')) {
                    this.hideChildren(this.collapsors[i])
                } else {
                    this.showChildren(this.collapsors[i])
                }
            })
            if(this.isInLocalStorage(this.collapsors[i])) {
                this.collapsors[i].click()
            }
        }


    }

    isInLocalStorage(elem)
    {
            if (elem.dataset.type === 'city' && localStorage.getItem('opened-city') === elem.dataset.city) {
                return true
            }
            if (elem.dataset.type === 'concession' && localStorage.getItem('opened-concession') === elem.dataset.concession) {
                return true
            }
            if (elem.dataset.type === 'service' && localStorage.getItem('opened-service') === elem.dataset.service) {
                return true
            }
            return false
    }

    saveInLocalStorage(elem) {
        if(elem.dataset.type === 'city') {
            localStorage.setItem('opened-city',  elem.dataset.city)
        }
        if(elem.dataset.type === 'concession') {
            localStorage.setItem('opened-concession',  elem.dataset.concession)
        }
        if(elem.dataset.type === 'service') {
            localStorage.setItem('opened-service',  elem.dataset.service)
        }
    }

    mergeNodeLists(a, b) {
        const slice = Array.prototype.slice;
        return slice.call(a).concat(slice.call(b));
    }

    getAllChildrenToShow(elem) {
        const typeName = elem.dataset.collapse;
        return document.querySelectorAll("[data-parent=" + typeName + "]");
    }

    getAllChildrenToHide(elem) {
        const type = elem.dataset.type;
        const typeName = elem.dataset.collapse;
        let children = null;
        if (type === 'service') {
            children = document.querySelectorAll(
                "[data-parent=" + typeName + "]"
            );
        }
        if (type === 'concession') {
            children = document.querySelectorAll("[data-parent=" + typeName + "]");
            for (let i = 0; i < children.length; i ++) {
                children = this.mergeNodeLists(children, this.getAllChildrenToShow(children[i]))
            }
        }
        if (type === 'city') {
            children = document.querySelectorAll("[data-parent=" + typeName + "]");
            for (let i = 0; i < children.length; i ++) {
                children = this.mergeNodeLists(children, this.getAllChildrenToShow(children[i]))
                for (let j = 0; j < children[i].length; j ++) {
                    children = this.mergeNodeLists(children[i], this.getAllChildrenToShow(children[j]))
                }
            }
        }
        return children;
    }

    showChildren(row) {
        const children = this.getAllChildrenToShow(row);
        if (children.length>0) {
            row.classList.add('active')
            for (let i = 0; i < children.length; i++) {
                const child = children[i];
                child.classList.remove('hide');
            }
            this.saveInLocalStorage(row)
        } else {
            M.toast({html:'Rien à afficher'});
        }
    }

    hideChildren(row) {
        row.classList.remove('active')
        const children = this.getAllChildrenToHide(row);
        for (let i = 0; i < children.length; i++) {
            const child = children[i];
            child.classList.remove('active');
            child.classList.add('hide');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const collapsor = new Collapsor('collapsor');
    collapsor.init();
})
