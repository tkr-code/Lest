$(document).ready(function(){
    $(".alert").fadeTo(10000, 500).slideUp(500, function () {
        $(".alert").slideUp(500);
    });
    //Copier un lien
    $(document).on('click', '.copie', function (e) {
        e.preventDefault()
        navigator.clipboard.writeText($(this).data('text'));
        Swal.fire({
            icon: 'success',
            title: "Le lien de l'article a été copié",
            showConfirmButton: false,
            timer: 2000
        })
    })
})