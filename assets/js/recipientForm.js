class recipientsAjaxTool {
    constructor(url) {
        this.urlToAdd     = url
        this.init()

    }

    init(data = null) {
        this.citySelectorId       = 'recipient_city'
        this.concessionSelectorId = 'recipient_concession'
        this.serviceSelectorId    = 'recipient_service'
        this.formId               = 'recipient_form'
        this.citySelector         = document.getElementById(this.citySelectorId);
        this.concessionSelector   = document.getElementById(this.concessionSelectorId);
        this.serviceSelector      = document.getElementById(this.serviceSelectorId);
        this.recipientForm        = document.getElementById(this.formId);
        if (data) {
            this.values = data
        } else {
            this.values       = {
                'City' : 0,
                'Concession' : 0,
                'Service' : 0,
            }
        }
        console.log(this.values)
        this.selectCity()
        if (this.concessionSelector) {
            this.selectConcession()
        }
        if (this.serviceSelector) {
            this.selectService()
        }

        this.initializeSelects()

        this.citySelector.addEventListener('change', () => {
            const postdata = {
                'City' : this.citySelector.value
            }
            this.sendData(postdata, (data) => {
                this.recipientForm.innerHTML = data
                this.init(postdata)
            })
        })
        if (this.concessionSelector) {
            this.concessionSelector.addEventListener('change', () => {
                const postdata = {
                    'City': this.citySelector.value,
                    'Concession': this.concessionSelector.value
                }
                this.sendData(postdata, (data) => {
                    this.recipientForm.innerHTML = data
                    this.init(postdata)
                })
            })
        }

        if (this.serviceSelector) {
            this.serviceSelector.addEventListener('change', () => {
                const postdata = {
                    'City': this.citySelector.value,
                    'Concession': this.concessionSelector.value,
                    'Service': this.serviceSelector.value
                }
                this.sendData(postdata, (data) => {
                    this.recipientForm.innerHTML = data
                    this.init(postdata)
                })
            })
        }
    }

    parseHtml (html) {
        const parser = new DOMParser();
        const result = parser.parseFromString(html, "text/html");
        return result;
    }

    sendData(data, action) {
        const thisClass = this
        fetch(this.urlToAdd, {
            method      : 'POST',
            mode        : "same-origin",
            credentials : "same-origin",
            body        : JSON.stringify(data),
            headers     : {
                'Content-Type': 'application/json'
            },
        })
        .then(function (response) {
            return response.text();
        }).then(function (html) {
            action(html);
        });
    }

    selectCity () {
        if ( this.citySelector.querySelector('option[value="' + this.values.City + '"]')) {
            this.citySelector.querySelector('option[value="' + this.values.City + '"]')
                .setAttribute('selected', 'selected');
        }
    }

    selectConcession () {
        if (this.concessionSelector.querySelector('option[value="' + this.values.Concession + '"]') && this.values.Concession !== 0) {
            this.concessionSelector.querySelector('option[value="' + this.values.Concession + '"]')
                .setAttribute('selected', 'selected');
        }
    }

    selectService () {
        if (this.serviceSelector.querySelector('option[value="' + this.values.Service + '"]') && this.values.Service !== 0) {
            this.serviceSelector.querySelector('option[value="' + this.values.Service + '"]')
                .setAttribute('selected', 'selected');
        }
    }

    initializeSelects() {
        const selects = document.querySelectorAll('select');
        M.FormSelect.init(selects);
    }
}

document.addEventListener('DOMContentLoaded', () => {

    const clientAjaxer         = new recipientsAjaxTool('/call/recipient/form', '');

});
