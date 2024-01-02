import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    connect() {
        this.table = document.querySelector('#collabs-list')
        this.loader = document.querySelector('#search-loader')
    }

    search() {
        this.loader.classList.toggle('opacity-0')
        const form = new FormData()
        form.append('name', this.element.value)
        fetch('/user/search', {
            method: 'POST',
            body: form
        }).then(response => {
            if(response.status === 200) {
                return response.text()
            }
            return null
        }).then(html => {
            this.loader.classList.toggle('opacity-0')
            this.table.innerHTML = html
        })
    }
}