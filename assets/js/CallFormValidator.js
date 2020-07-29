export class CallFormValidator {

    constructor() {
        this.phoneField = document.getElementById('call_client_phone');
        this.nameField = document.getElementById('call_client_name');
        this.immatField = document.getElementById('call_vehicle_immatriculation');
        this.recipientField = null;
    }

    allAreInPage() {
        return (this.recipientField) ? true : false
    }

    allAreFilled() {
        return (this.phoneField.value != ''
            && this.nameField.value != ''
            && this.immatField.value != ''
            && this.recipientField.value != '')
    }

    checkFields() {
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
        if (!this.recipientField || (this.recipientField && this.recipientField.value === '')) {
            M.toast({html : 'choisir le destinataire de l\'appel', classes : 'red'})
        }
    }

    finalCheck() {
        return (this.allAreInPage() && this.allAreFilled())
    }

}
