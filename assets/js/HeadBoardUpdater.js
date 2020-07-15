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
                document.getElementById(key).innerHTML = value
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const updater = new headBoardUpdater()
    const interval = setInterval(() => {
        updater.getHeadBoardNewData();
    }, 15000)
});
