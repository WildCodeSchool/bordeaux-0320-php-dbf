document.addEventListener('DOMContentLoaded', function() {
    const containers = document.getElementsByClassName('paddleft');
    const mamodal = document.getElementById('modal1');
    M.Modal.init(mamodal);

    const elems = document.querySelectorAll('.collapsible');
    M.Collapsible.init(elems);

    const selects = document.querySelectorAll('select');
    M.FormSelect.init(selects);

    const sidenav = document.getElementById('sidenav');
    const instanceOfSidenav = M.Sidenav.init(sidenav);


    const sidebarController = document.getElementById('control-sidebar');
    if (sidebarController) {
        sidebarController.addEventListener('click', (e) => {
            const itsAction = sidebarController.dataset.action;
            if (itsAction === 'hide') {
                if(instanceOfSidenav) {
                    instanceOfSidenav.close();
                }
                sidebarController.dataset.action = 'show';
                Array.from(containers)
                    .forEach((e) => {
                        e.classList.add('large');
                        sidebarController.innerHTML = '<i class="material-icons">chevron_right</i>';
                    });
            } else {
                if(instanceOfSidenav) {
                    instanceOfSidenav.open();
                }
                sidebarController.dataset.action = 'hide';
                Array.from(containers)
                    .forEach((e) => {
                        e.classList.remove('large');
                        sidebarController.innerHTML = '<i class="material-icons">chevron_left</i>';
                    });
            }
        });
    }
});
