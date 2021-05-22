export class CallFormValidator {

    constructor(phoneCityId) {
        this.phoneField = document.getElementById('call_client_phone');
        this.nameField = document.getElementById('call_client_name');
        this.immatField = document.getElementById('call_vehicle_immatriculation');
        this.recipientField = null;
        this.cityField = document.getElementById('call_city');
        this.phoneCityId = phoneCityId;
    }

    allAreInPage() {
        if(this.cityField.value == this.phoneCityId) {
            return true;
        }
        return (this.recipientField) ? true : false
    }

    allAreFilled() {
        const dataFields =  (
            this.phoneField.value != ''
            && this.nameField.value != ''
            && this.immatField.value != ''
        )
        if(this.cityField.value == this.phoneCityId && dataFields) {
            return true;
        }
        if(this.recipientField.value === '') {
            return false;
        }
        if(this.recipientField.value !== '' && dataFields) {
            return true;
        }
        return false;
    }

    checkFields() {
        let notified = 0;
        this.recipientField = document.getElementById('call_recipient_choice');

        if (!this.recipientField && this.cityField.value != this.phoneCityId) {
            M.toast({html : 'Choisir le destinataire de l\'appel', classes : 'red'})
            notified++
        }

        if (this.phoneField.value == '') {
            M.toast({html : 'Merci de remplir au moins un numéro de téléphone', classes : 'red'})
        }
        if (this.nameField.value == '') {
            M.toast({html : 'Merci de remplir le nom du client', classes : 'red'})
        }
        if (this.immatField.value == '') {
            M.toast({html : 'Merci de remplir la plaque d\'immatriculation. Si aucune, saisir "NC"', classes : 'red'})
        }
        if ((!this.recipientField  && this.cityField.value != this.phoneCityId )|| (this.recipientField && this.recipientField.value === '' && this.cityField.value != this.phoneCityId)) {
            if(notified === 0) {
                M.toast({
                    html: 'Choisir le destinataire de l\'appel',
                    classes: 'red'
                })
            }
        }
    }

    finalCheck() {
        return (this.allAreInPage() && this.allAreFilled())
    }

}
