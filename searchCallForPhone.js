class callSniffer {
    constructor(phoneNumber) {
        this.phoneNUmber = phoneNumber
        this.urlToSearch = '/call/search/' + this.phoneNumber
        this.init()
    }

    init() {
        this.getdata((data) => {
            console.log(data)
        })
    }

    getData(action) {
        fetch(this.urlToSearch, {
            method      : 'GET',
            headers     : {
                'Content-Type': 'application/json'
            },
        })
            .then(function (response) {
                return response.text();
            }).then(function (data) {
                action(data)
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {

    const phoneChecker = new callSniffer('0698765432');

});
