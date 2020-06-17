document.addEventListener('DOMContentLoaded', () => {
    let usersTabs = document.querySelector('#users-tabs');
    let usersInstance = M.Tabs.init(usersTabs, {});

    let callsTabs = document.querySelector('#call-tabs');
    let callsInstance = M.Tabs.init(callsTabs, {});

    let clientsTabs = document.querySelector('#clients-tabs');
    let clientsInstance = M.Tabs.init(clientsTabs, {});

    let commentsTabs = document.querySelector('#comments-tabs');
    let commentsInstance = M.Tabs.init(commentsTabs, {});

    let diversTabs = document.querySelector('#divers-tabs');
    let diversInstance = M.Tabs.init(diversTabs, {});

    let locationsTabs = document.querySelector('#locations-tabs');
    let locationsInstance = M.Tabs.init(locationsTabs, {});

    let subjectsTabs = document.querySelector('#subjects-tabs');
    let subjectsInstance = M.Tabs.init(subjectsTabs, {});

    let civilTabs = document.querySelector('#civil-tabs');
    let civilInstance = M.Tabs.init(civilTabs, {});

    let selects = document.querySelectorAll('select');
    let instancesOfSelects = M.FormSelect.init(selects, {});

    const modalAddClient = document.getElementById('modal-add-client');
    const instanceModalAddCLient = M.Modal.init(modalAddClient, {});

    const modalEditClient = document.getElementById('modal-edit-client');
    const instanceModalEditCLient = M.Modal.init(modalEditClient, {});

    const modalAddCivility = document.getElementById('modal-add-civility');
    const instanceModalAddCivility = M.Modal.init(modalAddCivility, {});

    const modalAddService = document.getElementById('modal-add-service');
    const instanceModalAddService = M.Modal.init(modalAddService, {});

    const modalListService = document.getElementById('modal-list-service');
    const instanceModalListService = M.Modal.init(modalListService, {});

    const modalShowVehicles         = document.getElementById('modal-show-vehicles');
    const instanceModalShowVehicles = M.Modal.init(modalShowVehicles, {});

    const modalListConcession = document.getElementById('modal-list-concession');
    const instanceModalListConcession = M.Modal.init(modalListConcession, {});

    const modalAddConcession = document.getElementById('modal-add-concession');
    const instanceModalAddConcession = M.Modal.init(modalAddConcession, {});

    var tooltips = document.querySelectorAll('.tooltipped');
    var instancesOfTooltips = M.Tooltip.init(tooltips);

})






