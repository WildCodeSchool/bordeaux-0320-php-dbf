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
        return (this.recipientField) ? true : false
    }

    allAreFilled() {
        let evaluation = 0;
        if (this.phoneField.value != ''
            && this.nameField.value != ''
            && this.immatField.value != '') {
            evaluation++;
            if (this.recipientField.value === '' && this.cityField.value === this.phoneCityId) {
                evaluation++;
            }
            if (this.recipientField.value === '' && this.cityField.value != this.phoneCityId) {
                evaluation--;
            }
            if (this.recipientField.value != '' && this.cityField.value != this.phoneCityId) {
                evaluation++;
            }
        }
        return (evaluation > 1);
    }

    checkFields() {
        console.log(this.phoneCityId, this.cityField.value);
        this.recipientField = document.getElementById('call_recipient_choice');

        if (this.phoneField.value == '') {
            M.toast({html : 'Merci de remplir au moins un numéro de téléphone', classes : 'red'})
        }
        if (this.nameField.value == '') {
            M.toast({html : 'Merci de remplir le nom du client', classes : 'red'})
        }
        if (this.nameField.value == '') {
            M.toast({html : 'Merci de remplir la plaque d\'immatriculation. Si aucune, saisir "NC"', classes : 'red'})
        }
        if (!this.recipientField || (this.recipientField && this.recipientField.value === '' && this.cityField.value != this.phoneCityId)) {
            M.toast({html : 'choisir le destinataire de l\'appel', classes : 'red'})
        }
    }

    finalCheck() {
        return (this.allAreInPage() && this.allAreFilled())
    }

}
