$(document).ready(function () {
    //Formulaire message dans la page "produit" (ou "sortie")
    //On met un écouteur d'événement sur le champ input de l'objet du message
    $("#messages_subject").on('keyup', resetField);
});

/**
 * Fonction qui réinitialise le champ input "l'objet du message" si l'utilisateur tente de modifier le contenu texte du champ
 */
function resetField(){
    //On retrouve dans le DOM le contenu du titre de la page "produit"
    $("#messages_subject").val($('article').first().text().trim());
    //On réinitialise les attributs disabled et required du champ input
    $("#messages_subject").attr('disabled','disabled');
    $("#messages_subject").attr('required','required');
    //On revoie le curseur de souris vers un autre champ du formulaire
    $('#messages_wished_date').focus();
} 