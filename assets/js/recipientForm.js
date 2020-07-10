class recipientsAjaxTool {
    constructor(url) {
        this.urlToAdd     = url
        this.urlToRandom = '/user/random';
        this.init()
    }

    init(data = null) {
        this.citySelectorId       = 'call_city'
        this.concessionSelectorId = 'call_concession'
        this.serviceSelectorId    = 'call_service_choice'
        this.recipientSelectorId  = 'call_recipient_choice'
        this.concessionZoneId     = 'recipient-concession'
        this.serviceZoneId        = 'recipient-service'
        this.recipientZoneId      = 'recipient-recipient'
        this.authorizedToPost     = true
        this.serviceFieldId       = 'call_service'
        this.recipientFieldId     = 'call_recipient'

        this.citySelector         = document.getElementById(this.citySelectorId);
        this.concessionSelector   = document.getElementById(this.concessionSelectorId);
        this.serviceSelector      = document.getElementById(this.serviceSelectorId);
        this.recipientSelector    = document.getElementById(this.recipientSelectorId);
        this.concessionZone       = document.getElementById(this.concessionZoneId);
        this.serviceZone          = document.getElementById(this.serviceZoneId);
        this.recipientZone        = document.getElementById(this.recipientZoneId);
        this.serviceField         = document.getElementById(this.serviceFieldId);
        this.recipientField       = document.getElementById(this.recipientFieldId);

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
            // TODO change != 4
            if (this.citySelector.value != 4) {
                this.concessionZone.innerHTML = this.addLoader()
            } else {
                this.concessionZone.innerHTML ='';
            }
            const postdata = {
                'City' : this.citySelector.value
            }
            if (this.authorizedToPost) {
                this.authorizedToPost = false;
                // TODO change != 4
                if (this.citySelector.value != 4) {
                    this.sendData(postdata, (data) => {
                        this.concessionZone.innerHTML = '<small class="grey-text">Choisir une concession</small><br>' + this.getHtmlElement(data, 'call_concession');
                        this.serviceZone.innerHTML = "";
                        this.recipientZone.innerHTML = "";
                        this.init(postdata)
                    })
                } else {
                    this.citySelector.setAttribute('disabled', 'disabled');
                    this.getRandomUser(data => {
                        console.log(data);
                        //TODO GET DATA
                        this.selectValueInSelect(this.recipientField, data.recipientId)
                        this.concessionZone.innerHTML = "";
                        this.serviceZone.innerHTML = "";
                        this.init(postdata)
                    })
                    this.initializeSelects()
                }
            }
        })

        if (this.concessionSelector) {
            this.concessionSelector.addEventListener('change', () => {
                this.serviceZone.innerHTML = this.addLoader()
                const postdata = {
                    'City'       : this.citySelector.value,
                    'Concession' : this.concessionSelector.value
                }
                if (this.authorizedToPost) {
                    this.authorizedToPost = false;
                    this.sendData(postdata, (data) => {
                        this.serviceZone.innerHTML = data;
                        this.serviceZone.innerHTML = '<small class="grey-text">Choisir un service</small><br>' + this.getHtmlElement(data, 'call_service_choice');
                        this.recipientZone.innerHTML = "";
                        this.init(postdata)
                    })
                }

            })
        }

        if (this.serviceSelector) {
            this.serviceSelector.addEventListener('change', () => {
                this.recipientZone.innerHTML = this.addLoader()
                const postdata = {
                    'City'       : this.citySelector.value,
                    'Concession' : this.concessionSelector.value,
                    'Service'    : this.serviceSelector.value
                }
                if (this.authorizedToPost) {
                    this.authorizedToPost = false;
                    this.sendData(postdata, (data) => {
                        this.recipientZone.innerHTML = '<small class="grey-text">Choisir un destinataire</small><br>' + this.getHtmlElement(data, 'call_recipient_choice');
                        this.init(postdata)
                    })
                }
                this.selectValueInSelect(this.serviceField, this.serviceSelector.value)
                this.serviceField.value = this.serviceSelector.value
            })
        }

        if (this.recipientSelector) {
            this.recipientSelector.addEventListener('change', () => {
                this.selectValueInSelect(this.recipientField, this.recipientSelector.value)
                this.recipientField.value = this.recipientSelector.value;
            })
        }
    }

    getHtmlElement(html, elemId) {
        const parser  = new DOMParser();
        const result  = parser.parseFromString(html, "text/html");
        const domElem = result.getElementById(elemId);
        return domElem.outerHTML;
    }


    addLoader() {
        return '<div class="progress grey lighten-3 mgt30">\n' +
            '      <div class="indeterminate light-blue"></div>\n' +
            '  </div>'
    }

    sendData(data, action) {
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

    getRandomUser(action) {
        fetch(this.urlToRandom, {
            method      : 'GET',
            headers     : {
                'Content-Type': 'application/json'
            },
        })
            .then(function (response) {
                return response.json();
            }).then(function (html) {
                action(html);
        });
    }

    selectValueInSelect(selector, selectorValue) {
        if ( selector.querySelector('option[value="' + selectorValue + '"]')) {
            selector.querySelector('option[value="' + selectorValue + '"]')
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
