import { Switch3 } from 'triswitch';

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('switcher')) {
        const switcherHasCome = new Switch3(['non', '?', 'oui'], [2, 0, 1], 'switcher', 0,
            '', 'vehicle_hasCome');
        switcherHasCome.init();
    }
});
