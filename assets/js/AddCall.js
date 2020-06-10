import { Switch3 } from 'triswitch';

document.addEventListener('DOMContentLoaded', () => {
    const switcherAddCall = new Switch3(['non', '?', 'oui'], [2, 0, 1], 'switcher_add_call', 0,
        '', 'call_vehicle_hasCome');
    switcherAddCall.init();
});
