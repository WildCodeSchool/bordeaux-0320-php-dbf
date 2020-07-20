export class ServiceAjaxTool {
    constructor(url) {
        this.urlToAdd = url;
        this.init();
    }
    getPhoneCityId(callback) {
        fetch('/phoneCity/getId')
            .then(response=>{
                return response.json()
            })
            .then(json=> {
                callback(json.phoneCityId);
            });
    }


    init(data = null) {
        this.citySelectorId = 'user_city';
        this.concessionSelectorId = 'user_concession';
        this.serviceSelectorId = 'user_service_choice';
        this.concessionZoneId = 'recipient-concession';
        this.serviceZoneId = 'recipient-service';
        this.authorizedToPost = true;
        this.serviceFieldId = 'user_service';

        this.citySelector = document.getElementById(this.citySelectorId);
        this.concessionSelector = document.getElementById(this.concessionSelectorId);
        this.serviceSelector = document.getElementById(this.serviceSelectorId);
        this.concessionZone = document.getElementById(this.concessionZoneId);
        this.serviceZone = document.getElementById(this.serviceZoneId);
        this.serviceField = document.getElementById(this.serviceFieldId);
        this.getPhoneCityId(((phoneCityId) => {
            this.phoneCityId = phoneCityId;
        }));

        if (data) {
            this.values = data;
        } else {
            this.values = {
                City: 0,
                Concession: 0,
                Service: 0,
            };
        }

        this.selectValueInSelect(this.citySelector, this.values.City);

        if (this.concessionSelector) {
            this.selectValueInSelect(this.concessionSelector, this.values.Concession);
        }

        if (this.serviceSelector) {
            this.selectValueInSelect(this.serviceSelector, this.values.Service);
        }

        this.initializeSelects();
        if (this.citySelector) {
            this.citySelector.addEventListener('change', () => {
                this.concessionZone.innerHTML = this.addLoader();
                const postdata = {
                    City: this.citySelector.value,
                };
                this.sendData(postdata, (data) => {
                    this.concessionZone.innerHTML = `<small class="grey-text label-for-select">Concession</small><br>${this.getHtmlElement(data, 'user_concession')}`;
                    this.serviceZone.innerHTML = '';
                    this.init(postdata);
                });

                this.initializeSelects();
            });
        }

        if (this.concessionSelector) {
            this.concessionSelector.addEventListener('change', () => {
                this.serviceZone.innerHTML = this.addLoader();
                const postdata = {
                    'City'       : this.citySelector.value,
                    'Concession' : this.concessionSelector.value,
                };
                if (this.authorizedToPost) {
                    this.authorizedToPost = false;
                    this.sendData(postdata, (data) => {
                        this.serviceZone.innerHTML = data;
                        this.serviceZone.innerHTML = '<small class="grey-text label-for-select">Service</small><br>' + this.getHtmlElement(data, 'user_service_choice');
                        this.init(postdata);
                    });
                }
            });
        }
        if (this.serviceSelector) {
            this.serviceSelector.addEventListener('change', () => {
                this.selectValueInSelect(this.serviceField, this.serviceSelector.value);
                this.serviceField.value = this.serviceSelector.value;
            })
        }
    }
    getHtmlElement(html, elemId) {
        const parser = new DOMParser();
        const result = parser.parseFromString(html, 'text/html');
        const domElem = result.getElementById(elemId);
        return domElem.outerHTML;
    }

    addLoader() {
        return '<div class="progress grey lighten-3 mgt30">\n'
            + '      <div class="indeterminate light-blue"></div>\n'
            + '  </div>';
    }

    sendData(data, action) {
        fetch(this.urlToAdd, {
            method: 'POST',
            mode: 'same-origin',
            credentials: 'same-origin',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json',
            },
        })
            .then(response => response.text()).then((html) => {
                action(html);
            });
    }


    selectValueInSelect(selector, selectorValue) {
        if (selector) {
            if (selector.querySelector(`option[value="${selectorValue}"]`)) {
                selector.querySelector(`option[value="${selectorValue}"]`)
                    .setAttribute('selected', 'selected');
            }
        }
    }

    initializeSelects() {
        const selects = document.querySelectorAll('select');
        M.FormSelect.init(selects);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const serviceAjaxer = new ServiceAjaxTool('/user/new', '');
});
