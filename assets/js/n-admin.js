document.addEventListener('DOMContentLoaded', () => {
    let usersTabs         = document.querySelector('#users-tabs');
    let usersInstance     = M.Tabs.init(usersTabs, {});

    let callsTabs         = document.querySelector('#call-tabs');
    let callsInstance     = M.Tabs.init(callsTabs, {});

    let clientsTabs       = document.querySelector('#clients-tabs');
    let clientsInstance   = M.Tabs.init(clientsTabs, {});

    let commentsTabs      = document.querySelector('#comments-tabs');
    let commentsInstance  = M.Tabs.init(commentsTabs, {});

    let diversTabs        = document.querySelector('#divers-tabs');
    let diversInstance    = M.Tabs.init(diversTabs, {});

    let locationsTabs        = document.querySelector('#locations-tabs');
    let locationsInstance    = M.Tabs.init(locationsTabs, {});

    let subjectsTabs        = document.querySelector('#subjects-tabs');
    let subjectsInstance    = M.Tabs.init(subjectsTabs, {});

    let civilTabs        = document.querySelector('#civil-tabs');
    let civilInstance    = M.Tabs.init(civilTabs, {});
});
