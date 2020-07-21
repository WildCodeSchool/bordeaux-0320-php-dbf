class headBoardUpdater {

    getHeadBoardNewData() {
        fetch('/head/data', {method:'GET'})
        .then(response => {
            if (response.status === 200) {
                return response.json()
            }
        })
        .then(json => {
            for (var [key, value] of Object.entries(json)){
                if(document.getElementById(key)) {
                    document.getElementById(key).innerHTML = value
                    if (value != 0) {
                        document.getElementById(key).classList.remove('hide')
                    }
                    if (value === 0 && !document.getElementById(key).classList.contains('hide')) {
                        document.getElementById(key).classList.add('hide')
                    }
                }
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const updater = new headBoardUpdater()
    window.setInterval(() => {
        updater.getHeadBoardNewData();
    }, 15000)
});
