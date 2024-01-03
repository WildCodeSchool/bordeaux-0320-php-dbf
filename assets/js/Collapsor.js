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
            if (
                elem.dataset.type &&
                localStorage.getItem(`opened-${elem.dataset.type}`) &&
                JSON.parse(localStorage.getItem(`opened-${elem.dataset.type}`)).includes(elem.dataset.identifier)) {
                return true
            }
            return false
    }

    saveInLocalStorage(elem) {

        if(elem.dataset.type) {
            this.addLocalItem(elem.dataset.type,  elem.dataset.identifier)
        }
    }
    removeFromLocalStorage(elem) {
        if(elem.dataset.type) {
            this.removeLocalItem(elem.dataset.type,  elem.dataset.identifier)

            if(elem.dataset.type === 'city') {
                this.removeAllConcessionsForCity(elem.dataset.identifier)
            }

            if(elem.dataset.type === 'concession') {
                this.removeAllServicesForConcession(elem.dataset.identifier)
            }
        }
    }

    removeAllConcessionsForCity(city)
    {
        let concessions = JSON.parse(localStorage.getItem(`opened-concession`))
        for (let i in concessions) {
            if(concessions[i].indexOf(city + '-') !== -1) {
                this.removeLocalItem('concession', concessions[i])
                this.removeAllServicesForConcession(concessions[i])
            }
        }
    }
    removeAllServicesForConcession(concession)
    {
        let services = JSON.parse(localStorage.getItem(`opened-service`))
        for (let i in services) {
            if(services[i].indexOf(concession + '-') !== -1) {
                this.removeLocalItem('service', services[i])
            }
        }
    }


    addLocalItem(type, value)
    {
        let val = localStorage.getItem(`opened-${type}`) ? JSON.parse(localStorage.getItem(`opened-${type}`)) : []
        if(!val.includes(value)) {
            val.push(value)
        }
        localStorage.setItem(`opened-${type}`, JSON.stringify(val))
    }

    removeLocalItem(type, value)
    {
        let val = localStorage.getItem(`opened-${type}`) ? JSON.parse(localStorage.getItem(`opened-${type}`)) : []
        if(val.indexOf(value) !== -1) {
            const index = val.indexOf(value)
            val.splice(index, 1)
        }
        localStorage.setItem(`opened-${type}`, JSON.stringify(val))
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
        this.removeFromLocalStorage(row)
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const collapsor = new Collapsor('collapsor');
    collapsor.init();
})
