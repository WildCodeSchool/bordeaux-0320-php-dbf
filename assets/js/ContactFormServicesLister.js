
if (document.getElementById('contact_form_id')) {
    form = document.getElementById('contact_form_id')
    concessionSelector = document.getElementById('dbf_contact_place')
    serviceSelector = document.getElementById('dbf_contact_service')
    messageInput = document.getElementById('dbf_contact_message')

    concessionSelector.addEventListener('change', () => {
        let errors = 0;
        if (document.getElementById('dbf_contact_name').value === '') {
            document.getElementById('dbf_contact_name').classList.add('red')
            document.getElementById('dbf_contact_name').addEventListener('click', () => {
                document.getElementById('dbf_contact_name').classList.remove('red')
            })
            errors++;
        }
        if (document.getElementById('dbf_contact_phone').value === '') {
            document.getElementById('dbf_contact_phone').classList.add('red')
            document.getElementById('dbf_contact_phone').addEventListener('click', () => {
                document.getElementById('dbf_contact_phone').classList.remove('red')
            })
            errors++
        }
        if (document.getElementById('dbf_contact_immat').value === '') {
            document.getElementById('dbf_contact_immat').classList.add('red')
            document.getElementById('dbf_contact_immat').addEventListener('click', () => {
                document.getElementById('dbf_contact_immat').classList.remove('red')
            })
            errors++
        }
        if(concessionSelector.value === null) {
            errors++;
        }
        if (errors === 0) {
            if (document.getElementById('dbf_contact_service')) {
                document.getElementById('dbf_contact_service').remove()
                document.getElementById('dbf_contact_message').remove()
            }
            form.submit()
        } else {
            document.getElementById('form-alert').classList.remove('hide')
            concessionSelector.value = null
            const selects = document.querySelectorAll('select');
            M.FormSelect.init(selects);
        }
    })
}
