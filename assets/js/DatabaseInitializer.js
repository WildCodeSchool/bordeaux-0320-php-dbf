document.addEventListener('DOMContentLoaded', () => {

    if (document.getElementById('database-initializer')) {
        document.getElementById('database-initializer').addEventListener('click', (e) => {
            e.preventDefault()
            if(confirm('Procéder à l\'initialisation de la base de données ?')) {
                document.location.href = '/admin/resetdatabase'
            }
        })
    }
})
