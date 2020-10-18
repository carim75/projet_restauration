// DOM ready

var x =0;

var textEffect = ".Commandeur.";
var container = document.getElementById('effect');


function animate () {

    if (x < textEffect.length){

        container.innerHTML+= textEffect.charAt(x);

        x++;
        setTimeout(animate,70);
    }
}
animate();


var e = document.getElementById('parent');
e.onmouseover = function() {
    document.getElementById('popup').style.display = 'block';
}
e.onmouseout = function() {
    document.getElementById('popup').style.display = 'none';
}


var u = document.getElementById('parent2');
e.onmouseover = function() {
    document.getElementById('popup2').style.display = 'block';
}
e.onmouseout = function() {
    document.getElementById('popup2').style.display = 'none';
}





$(function () {
    $('.btn-delete').click(function (event) {
        // pour ne pas aller directement vers la page de suppression
        event.preventDefault();

        // l'attribut href du bouton Supprimer
        var link = $(this).attr('href');

        var $modal = $('#modal_delete');

        // affiche la modale
        $modal.modal('show');

        $modal.find('.btn-confirm-delete').click(function () {
            // redirection vers le lien du bouton Supprimer
            window.location.href = link;
        });
    });
});

// DOM ready
$(function () {
    $('.btn-delete').click(function (event) {
        // pour ne pas aller directement vers la page de suppression
        event.preventDefault();

        // l'attribut href du bouton Supprimer
        var link = $(this).attr('href');

        var $modal = $('#modal_delete');

        // affiche la modale
        $modal.modal('show');

        $modal.find('.btn-confirm-delete').click(function () {
            // redirection vers le lien du bouton Supprimer
            window.location.href = link;
        });
    });
});





