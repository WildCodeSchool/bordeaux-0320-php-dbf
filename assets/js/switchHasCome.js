document.addEventListener('DOMContentLoaded', () => {
    const switcher = new Switch3(['non', '?', 'oui'], [2, 0, 1], 'switcher', 1,
        '', 'vehicle_hasCome');
    switcher.init();
})
