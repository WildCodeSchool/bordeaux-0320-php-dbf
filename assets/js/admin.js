document.addEventListener('DOMContentLoaded', () => {
    let usersTabs = document.querySelector('#users-tabs');
    M.Tabs.init(usersTabs, {});

    let callsTabs = document.querySelector('#call-tabs');
    M.Tabs.init(callsTabs, {});

    let clientsTabs = document.querySelector('#clients-tabs');
    M.Tabs.init(clientsTabs, {});

    let commentsTabs = document.querySelector('#comments-tabs');
    M.Tabs.init(commentsTabs, {});

    let diversTabs = document.querySelector('#divers-tabs');
    M.Tabs.init(diversTabs, {});

    let locationsTabs = document.querySelector('#locations-tabs');
    M.Tabs.init(locationsTabs, {});

    let subjectsTabs = document.querySelector('#subjects-tabs');
    M.Tabs.init(subjectsTabs, {});

    let civilTabs = document.querySelector('#civil-tabs');
    M.Tabs.init(civilTabs, {});

    let selects = document.querySelectorAll('select');
    M.FormSelect.init(selects, {});

    const modalAddClient = document.getElementById('modal-add-client');
    M.Modal.init(modalAddClient, {});

    const modalEditClient = document.getElementById('modal-edit-client');
    M.Modal.init(modalEditClient, {});

    const modalAddCivility = document.getElementById('modal-add-civility');
    M.Modal.init(modalAddCivility, {});

    const modalEditCivility = document.getElementById('modal-edit-civility');
    M.Modal.init(modalEditCivility, {});

    const modalAddService = document.getElementById('modal-add-service');
    M.Modal.init(modalAddService, {});

    const modalListService = document.getElementById('modal-list-service');
    M.Modal.init(modalListService, {
        onCloseEnd: () => {
            document.location.reload();
        }
    });

    const modalShowVehicles = document.getElementById('modal-show-vehicles');
    M.Modal.init(modalShowVehicles, {});

    const modalListConcession = document.getElementById('modal-list-concession');
    M.Modal.init(modalListConcession, {
        onCloseEnd: () => {
            document.location.reload();
        }
    });

    const modalAddConcession = document.getElementById('modal-add-concession');
    M.Modal.init(modalAddConcession, {});

    const modalAddSubject = document.getElementById('modal-add-subject');
    M.Modal.init(modalAddSubject, {});

    const modalAddComment = document.getElementById('modal-add-comment');
    M.Modal.init(modalAddComment, {});

    const modalListCity = document.getElementById('modal-list-city');
    M.Modal.init(modalListCity, {});

    const modalAddCity = document.getElementById('modal-add-city');
    M.Modal.init(modalAddCity, {});

    const modalEditSubject = document.getElementById('modal-edit-subject');
    M.Modal.init(modalEditSubject, {});

    const modalEditComment = document.getElementById('modal-edit-comment');
    M.Modal.init(modalEditComment, {});

    const tooltips = document.querySelectorAll('.tooltipped');
    M.Tooltip.init(tooltips, {
        html:true
    });

    const modalSearchUser = document.getElementById('modal-search-user');
    M.Modal.init(modalSearchUser, {});

    const modalAddUser = document.getElementById('modal-add-user');
    M.Modal.init(modalAddUser, {});

})






