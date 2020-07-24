document.addEventListener('DOMContentLoaded', () => {

    const immat   = document.getElementById('call_vehicle_immatriculation')
    const hasCome = document.getElementById('call_vehicle_hasCome')
    const chassis = document.getElementById('call_vehicle_chassis')
    const vehicle = document.getElementById('call_vehicle_id')
    const button  = document.getElementById('add-vehicle-button')
    if (button) {
        button.addEventListener('click', () => {
            immat.value = ''
            hasCome.value = 0
            chassis.value = ''
            vehicle.removeAttribute('value')
        })
    }

})
