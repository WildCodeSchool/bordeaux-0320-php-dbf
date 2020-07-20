document.addEventListener('DOMContentLoaded', () => {
    const route = window.location.href;
    if (route.indexOf('/user/#') != -1) {
        const routeParams = route.split('/user/#')
        const userId = routeParams[1]

        if (userId) {
            document.getElementById(userId)
                .classList
                .add('cyan', 'lighten-4')
        }
    }
})
