/**
 * Code qui gére l'édition/création d'un attribut hors contexte de collection dans la création/edition de produits
 */

let id;
let attributesFormElement;
let attributes;
let counter = 0;

$(document).ready(function () {

    //Selection des éléments div du formulaire de l'attribut
    attributesFormElement = $('#attribute-fields-list');

    //On met un écouteur d'événements sur chacun des champs <select> de selection d'un groupe d'attributs
    $($(attributesFormElement).find('select')[0]).on('click', getOptionElement);

});

/**
 * Fonction qui vide les champs texte des zones "nom d'attribut" ("name") et "valeur d'attribut" ("value")
 */
function getOptionElement() {
    $($(attributesFormElement).find('input')[0]).val('');
    $($(attributesFormElement).find('textarea')[0]).val('');
    //Adossement d'un écouteur d'événements sur toutes les balises <option> du selecteur de groupe d'attribut
    $($(this).find('option')).on('click', ajaxCall);
}

/**
 * Fonction qui va chercher en requête Ajax les attributs appartenants au groupe d'attributs
 * sélectionné par l'utilisateur
 */
function ajaxCall() {
    //Récupération url absolue de la requete
    let url = $('#attribute-fields-list').data('url-path');

    //Récupération valeur de la balise option
    let groupAttributeId = this.value;
    //Requete ajax methode get
    $.ajax({
        url: url,
        method: "get",
        data: { id: groupAttributeId }
    }).done((response) => {
        fillAttributeNames(response);
    });
}

/**
 * Fonction qui affiche, dans la balise <select> de la zone "name" de l'attributs, les noms d'attributs 
 * obtenus via la requête ajax 
 */
function fillAttributeNames(response) {

    //élément select cible
    let attributeNamesDatalist = $(attributesFormElement).find('select')[1];

    //On vide l'élément de ses balises option
    attributeNamesDatalist.innerHTML = '';

    attributes = JSON.parse(response);

    if (attributes.length == 0) {
        //Si le groupe d'attributs sélectionné n'est lié à aucun attribut , on affiche un message utilisateur dans la balise <select>
        let option = document.createElement("option");
        option.value = "Aucun attribut appartenant à ce groupe en bdd";
        option.textContent = "Aucun attribut appartenant à ce groupe en bdd";
        attributeNamesDatalist.appendChild(option);

    } else {
        //Sinon
        //On vide l'élément de ses balises option, pour en créer de nouvelles
        attributeNamesDatalist.innerHTML = '';

        // Récupération des noms d'attributs (sans doublons)
        let attributeNames = [];
        for (let attribute of attributes) {
            if (!attributeNames.find(entry => entry.name == attribute.name)) {
                attributeNames.push({
                    'id': attribute.id,
                    'name': attribute.name
                });
            }
        }
                
        //Création d'une balise <option> pour chacun des attributs récupérés
        for (let attribute of attributeNames) {
            let option = document.createElement("option");
            option.value = attribute.name;
            option.textContent = attribute.name;
            attributeNamesDatalist.appendChild(option);
            option.addEventListener('click', fillAttributeNamesInput);
        }
        //Ajustement de la taille de la balise <select> pour affichage de tous les noms d'attributs appartenants au groupe d'attributs sélectionné par l'utilisateur
        attributeNamesDatalist.setAttribute('size', attributeNames.length+1 );
    }

}

/**
 * Fonction qui remplit l'input "name" de l'attribut
 */
function fillAttributeNamesInput() {
    
    //Récupération du numéro de l'élémént de formulaire de collection cliqué
    let selectElementId = this.parentElement.id;
    id = selectElementId.replace('_name_list', '').replace('products_attribute_', '');
    //Sélection du champ input cible
    let attachedInput = document.querySelector('[list=' + selectElementId + ']');
    //Remplissage du champ
    attachedInput.value = this.value;
    //On met en paramêtre la valeur de remplissage dans une fonction qu'on appelle
    fillAttributeContents(this.value);
}


/**
 * Fonction qui remplit la balise <select> dans la zone "value" de l'attribut
 */
function fillAttributeContents($value) {

    //élément select cible
    let attributeContentsDatalist = $(attributesFormElement).find('select')[2];

    //On vide l'élément de ses balises option
    attributeContentsDatalist.innerHTML = '';

    
    //On remplit la balise <select> avec des balises <option> avec toutes valeurs d'attribut qui ont le même nom d'attribut sélectionné par l'utilisateur
    $countValidAttributes = 0;
    for (let attribute of attributes) {
        if ($value == attribute.name) {
            $countValidAttributes++;
            let option = document.createElement("option");
            option.value = attribute.value;
            option.textContent = attribute.value;
            attributeContentsDatalist.appendChild(option);
            //On adosse un ecouteur d'evenement sur chacune des balises <option>
            option.addEventListener('click', fillAttributeContentsInput);
        }
    }
    //Ajustement de la taille de la balise <select> pour affichage de toutes les valeurs d'attributs appartenants au nom d'attribut sélectionné par l'utilisateur
    attributeContentsDatalist.setAttribute('size', $countValidAttributes+1 );
}

/**
 * Fonction qui remplit la balise <input> de la zone "value" de l'attribut
 */
function fillAttributeContentsInput() {
    let selectElementId = this.parentElement.id;
    let attachedInput = document.querySelector('[list=' + selectElementId + ']')
    attachedInput.value = this.value;
}

