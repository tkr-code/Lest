$(document).ready(function () {

    // ENVOIE UN LIEN DE MODIFICATION D'EMAIL
    $(document).on('click', '#btn-edit-email', function () {
        Swal.fire({
            title: 'Etes vous sûr?',
            text: "Vous allez envoyer un email de modification d'email !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Fermer',
            confirmButtonText: 'Oui, je confirme!'
        }).then((result) => {
            if (result.isConfirmed) { // ajax
                $.ajax({
                    url: "/send/edit-email",
                    method: "POST",
                    dataType: 'json',
                    data: {},
                    beforeSend: function () {
                        $('.loader-edit-email').css('display', 'initial')
                        $('#btn-edit-email').css('display', 'none')
                    },
                    success: function (data) {
                        if (data == 'success') { } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Le mail de modification a été envoyé',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }
                        $('.loader-edit-email').css('display', 'none')
                        $('#btn-edit-email').css('display', 'initial')
                        $('.alert-edit-email').css('display', 'initial')
                    }
                })
                // ./ajax end
            }
        })
    })

    //BOUTON DE DECONNECTION
    $(document).on('click', '#btn-logout', function (e) {
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