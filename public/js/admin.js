$(document).ready(function() {
    $(document).on('click', '#btn-logout', function(e) {
        e.preventDefault()
        Swal.fire({
            title: 'Etes vous sûr?',
            text: "Vous êtes sur le point de vous déconnecter !  ",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Fermer',
            confirmButtonText: 'Oui, je confirme!'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Déconnection réussie !',
                    showConfirmButton: false,
                    timer: 1500
                }).then((result) => {
                    window.location.href = "/logout"
                })
            }
        })
    })
})