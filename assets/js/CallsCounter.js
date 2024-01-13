export class CallsCounter {

    updateTotalCallToProcess (method = 'add') {
        const counter = document.getElementById('nb-calls-to-process');
        if(counter) {
            let total = parseInt(counter.innerHTML);
            if (method === 'dec') {
                total--
            } else {
                total++
            }
            counter.innerHTML = total
        }
    }

    updateTotalCallInProcess (method = 'add') {
        const counter = document.getElementById('nb-calls-in-process');
        if(counter) {
            let total = parseInt(counter.innerHTML);
            if (method === 'dec') {
                total--
            } else {
                total++
            }
            counter.innerHTML = total;
        }
    }
}
