$(document).ready(function(){
    //SE CONNECTER POUR METTRE EN FAVORIS
    $(document).on('click','.js-favoris-login',function(e){
        e.preventDefault()
        Swal.fire(
            'Oups !',
            'Merci de vous conneter pour ajouter un produit à la liste des favoris.',
            'info'
        ).then(()=>{
            $('#signin-modal').modal('show')
        })
    })
})