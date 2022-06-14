$(document).ready(function() {
    function compteurToRedirige(id, count, text, path = "/") {
        var countdown = setInterval(function() {
            $(id).html(text + count + ' s');
            if (count == 0) {
                clearInterval(countdown);
                window.open(path, "_self");
            }
            count--;
        }, 1000);
    }
    //SUPPRIMER UN CLIENT
    $(document).on('click', '.js-user-delete', function(e) {
        e.preventDefault()
        let href = $(this).attr('href')
        let token = $(this).data('token')

        Swal.fire({
            title: 'Etes vous sûr ?',
            text: "Vous êtes sur le point de supprimeer votre compte lest  !  ",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            cancelButtonText: 'Fermer',
            confirmButtonText: 'Oui, je confirme!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: href,
                    method: 'POST',
                    type: 'json',
                    data: {
                        _token: token,
                        ajax: true
                    },
                    beforeSend: function() {
                        $('.js-loader-text').text('Vérificqtion de suppresion du compte en cour ...')
                        $('.js-loader').css('display', 'flex')
                    },
                    success: function(data) {
                        $('.js-loader').css('display', 'none')
                        if (data.reponse == true) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Votre compte a bien été supprimé!',
                                showConfirmButton: false,
                                timer: 1500
                            }).then((result) => {
                                $('.text-delete').html('Votre conpte a été supprimer !')
                                compteurToRedirige('.btn-delete-compte', 5, 'Vous serez redirigé dans ', '/logout');
                            })
                        } else {
                            alert('Une erreur est servenue :)')
                        }
                    },
                    error: function() {
                        $('.js-loader').css('display', 'none')
                        alert('Une erreur est servenue.')
                    }
                })
            }
        })


    })
})