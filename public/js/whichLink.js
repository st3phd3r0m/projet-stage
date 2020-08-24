/**
 * Code qui gére la sélection du type de liens et le pré-remplissage du formulaire de création/modification de liens  
 */

$(document).ready(function () {

    //Suivant le type de liens que souhaite ajouter l'utilisateur, on
    //fait apparaître ultérieurement les champs corespondants
    $('#categories-fields-list').hide();
    $('#pages-fields-list').hide();
    $('#external-link-field').hide();
    
    //Ecouteur d'événements sur le champ "type de liens"
    $('#links_type').on('click',ajaxCall);
});

/**
 * Fonction qui appel une requete ajax suivant le type de liens selectionné par l'utilisateur
 */
function ajaxCall(){

    let url;
    let targetElement;

    if(this.value === '/categorie/'){
        //Si l'utilisateur choisit d'ajouter un lien vers une page "catégorie"
        targetElement = $('#categories-fields-list');
        //On cache les autres balises <input> et <select>
        $('#pages-fields-list').hide();
        $('#external-link-field').hide();
        //On vide les balises <input> de titre et de contenu
        $("#links_title").val('');
        $("#links_content").val('');
        //On fait apparaitre la balise cible
        $(targetElement).show();
        //L'url cible de la requete ajax 
        url = $('#categories-fields-list').data('url-path');
    }else if(this.value === '/page/'){
        //Si l'utilisateur choisit d'ajouter un lien vers une page "publication"
        targetElement = $('#pages-fields-list');
        //On cache les autres balises <input> et <select>
        $('#categories-fields-list').hide();
        $('#external-link-field').hide();
         //On vide les balises <input> de titre et de contenu
        $("#links_title").val('');
        $("#links_content").val('');
        //On fait apparaitre la balise cible
        $(targetElement).show();
        //L'url cible de la requete ajax 
        url = $('#pages-fields-list').data('url-path');
    }else if(this.value === 'external'){
        //Si l'utilisateur choisit d'ajouter un lien externe
        //On cache les balises <select>
        $('#categories-fields-list').hide();
        $('#pages-fields-list').hide();
        //On fait apparaitre la balise <input> cible
        $('#external-link-field').show();
        //On vide les balises <input> de titre et de contenu
        $("#links_title").val('');
        $("#links_content").val('');
        return false;
    }else{
        $('#categories-fields-list').hide();
        $('#pages-fields-list').hide();
        $('#external-link-field').hide();
        $("#links_title").val('');
        $("#links_content").val('');
        return false;
    }

    //Requete ajax methode get
    $.ajax({
        url: url,
        method: "get"
    }).done((response) => {
        // Récupération des catégories ou pages de publication
        items = JSON.parse(response);
        if (items.length == 0) {
            //Message utilisateur au cas où la requete ajax retourne un tableau vide 
            let option = document.createElement("option");
            option.value = "Aucun élément de ce type en bdd";
            option.textContent = "Aucun élément de ce type en bdd";
            targetElement.children.appendChild(option);
        } else {
            //On vide l'élément de ses balises option
            $(targetElement).find('select')[0].innerHTML = '';
            //Création de balises <option> dans la balise <select>
            for (let item of items) {
                let option = document.createElement("option");
                option.value = item.slug;
                option.textContent = item.title;
                $(targetElement).find('select')[0].appendChild(option);
                //Ecouteur d'événement sur chacune des balises <option>
                $(option).on('click', fillTextInput);
            }
        }
    });
}

/**
 * Fonction qui pré-remplit les champs <input> de titre du liens et de contenu texte du lien
 */
function fillTextInput(){
    $("#links_title").val(this.textContent);
    $("#links_content").val(this.textContent);
}



