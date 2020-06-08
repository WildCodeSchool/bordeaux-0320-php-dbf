class recipientsAjaxTool {
    constructor(url) {
        this.urlToAdd     = url
        this.init()
    }

    init(data = null) {
        this.citySelectorId       = 'call_city'
        this.concessionSelectorId = 'call_concession'
        this.serviceSelectorId    = 'call_service'
        this.concessionZoneId     = 'recipient-concession'
        this.serviceZoneId        = 'recipient-service'
        this.recipientZoneId      = 'recipient-recipient'
        this.authorizedToPost     = true

        this.citySelector         = document.getElementById(this.citySelectorId);
        this.concessionSelector   = document.getElementById(this.concessionSelectorId);
        this.serviceSelector      = document.getElementById(this.serviceSelectorId);
        this.concessionZone       = document.getElementById(this.concessionZoneId);
        this.serviceZone          = document.getElementById(this.serviceZoneId);
        this.recipientZone        = document.getElementById(this.recipientZoneId);

        if (data) {
            this.values = data
        } else {
            this.values       = {
                'City' : 0,
                'Concession' : 0,
                'Service' : 0,
            }
        }

        this.selectValueInSelect(this.citySelector, this.values.City)

        if (this.concessionSelector) {
            this.selectValueInSelect(this.concessionSelector, this.values.Concession)
        }

        if (this.serviceSelector) {
            this.selectValueInSelect(this.serviceSelector, this.values.Service)
        }

        this.initializeSelects()

        this.citySelector.addEventListener('change', () => {
            const postdata = {
                'City' : this.citySelector.value
            }
            if (this.authorizedToPost) {
                this.authorizedToPost = false;
                this.sendData(postdata, (data) => {
                    this.concessionZone.innerHTML = '<b>Choisir une concession</b><br>' + this.getHtmlElement(data, 'call_concession');
                    this.serviceZone.innerHTML = "";
                    this.recipientZone.innerHTML = "";
                    this.init(postdata)
                })
            }
        })

        if (this.concessionSelector) {
            this.concessionSelector.addEventListener('change', () => {
                const postdata = {
                    'City'       : this.citySelector.value,
                    'Concession' : this.concessionSelector.value
                }
                if (this.authorizedToPost) {
                    this.authorizedToPost = false;
                    this.sendData(postdata, (data) => {
                        this.serviceZone.innerHTML = data;
                        this.serviceZone.innerHTML = '<b>Choisir un service</b><br>' + this.getHtmlElement(data, 'call_service');
                        this.recipientZone.innerHTML = "";
                        this.init(postdata)
                    })
                }

            })
        }

        if (this.serviceSelector) {
            this.serviceSelector.addEventListener('change', () => {
                const postdata = {
                    'City'       : this.citySelector.value,
                    'Concession' : this.concessionSelector.value,
                    'Service'    : this.serviceSelector.value
                }
                if (this.authorizedToPost) {
                    this.authorizedToPost = false;
                    this.sendData(postdata, (data) => {
                        this.recipientZone.innerHTML = '<b>Choisir un destinataire</b><br>' + this.getHtmlElement(data, 'call_recipient');
                        this.init(postdata)
                    })
                }
            })
        }
    }

    getHtmlElement(html, elemId) {
        const parser  = new DOMParser();
        const result  = parser.parseFromString(html, "text/html");
        const domElem = result.getElementById(elemId);
        return domElem.outerHTML;
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

    selectValueInSelect(selector, selectorValues) {
        if ( selector.querySelector('option[value="' + selectorValues + '"]')) {
            selector.querySelector('option[value="' + selectorValues + '"]')
                .setAttribute('selected', 'selected');
        }
    }

    initializeSelects() {
        const selects = document.querySelectorAll('select');
        M.FormSelect.init(selects);
    }
}

document.addEventListener('DOMContentLoaded', () => {

    const clientAjaxer         = new recipientsAjaxTool('/call/add', '');

});
