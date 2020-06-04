class Switch3 {

    constructor(labels, values, renderZoneId, initVal= 0, name = 'switch3', destinationInputId = null) {
        this.labels         = labels;
        this.values         = values;
        this.renderZone     = document.getElementById(renderZoneId);
        this.initVal        = initVal;
        this.inputId        = destinationInputId;
        this.leftPosition   = 0;
        this.centerPosition = 1;
        this.rightPosition  = 2;
        this.name           = name;
        this.initPosition   = this.values.indexOf(this.initVal);
    }

    init() {
        this.render();
        this.bar            = document.getElementById(this.name + '-bar');
        this.cursor         = document.getElementById(this.name + '-cursor');
        this.field          = (this.inputId) ? document.getElementById(this.inputId) : document.getElementById(this.name + '-switch3-val');
        this.field.setAttribute('value', this.initVal);
        this.labels         = document.getElementsByClassName(this.name + '-label');
        this.getPositions();
        this.addActiveLabels(this.initVal);
        this.bar.addEventListener('click', (e) => {
            this.actions(e)
        });
    }

    getPositions() {
        const barDimensions = this.bar.getBoundingClientRect();
        this.left           = barDimensions.left;
        this.right          = barDimensions.right;
        this.center         = this.right - ((this.right - this.left) / 2);
    }

    mousePosition(e) {
        const mouseClickX = e.clientX;
        if (Math.abs(this.right - mouseClickX) < Math.abs(this.center - mouseClickX)) {
            return this.rightPosition;
        } else if (Math.abs(this.center - mouseClickX) < Math.abs(this.left - mouseClickX)) {
            return this.centerPosition;
        } else {
            return this.leftPosition;
        }
    }

    render() {
        this.renderZone.innerHTML = this.createElement();
    }

    actions(e) {
        this.cursor.classList = ['switch3-cursor'];
        this.cursor.classList.add('pos-' + this.mousePosition(e));
        this.field.value = this.values[this.mousePosition(e)];
        this.removeActiveLabels().addActiveLabels(this.mousePosition(e));
    }

    createElement() {
        let html = '<div class="switch3-container" id="' + this.name + '-container">\n' +
            '    <div class="switch3-bar" id="' + this.name + '-bar">\n' +
            '      <div class="switch3-cursor pos-' + this.initPosition + ' " id="' + this.name + '-cursor"></div>\n' +
            '    </div>\n' +
            '    <div class="switch3-label" data-value="' + this.values[this.leftPosition] + '" id="' + this.name + '-label0">' + this.labels[this.leftPosition] + '</div>\n' +
            '    <div class="switch3-label" data-value="' + this.values[this.centerPosition] + '" id="' + this.name + '-label1">' + this.labels[this.centerPosition] + '</div>\n' +
            '    <div class="switch3-label" data-value="' + this.values[this.rightPosition] + '" id="' + this.name + '-label2">' + this.labels[this.rightPosition] + '</div>\n' ;
        if (!this.inputId) {
            html += '<input type="hidden" id="' + this.name + '-switch3-val" name="' + this.name + '-switch3-val">';
        }
        html += '    </div>';
        return html;
    }

    removeActiveLabels() {
        for (let i = 0; i < this.labels.length; i++) {
            this.labels[i].classList.remove('active');
        };
        return this;
    }

    addActiveLabels(position) {
        for (let i = 0; i < this.labels.length; i++) {
            if (this.labels[i].id === this.name + '-label' + position) {
                this.labels[i].classList.add('active')
            }
        };
        return this;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const switcher = new Switch3(['non', '?', 'oui'], [2, 0, 1], 'switcher', 0,
        '', 'vehicle_hasCome');
    switcher.init();
})
