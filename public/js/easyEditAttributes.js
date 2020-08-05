let id;
let attributesFormElement;
let attributes;
let counter = 0;

$(document).ready(function () {

    //Selection des éléments li>div de la collection des attributs
    attributesFormElement = $('#attribute-fields-list');

    $($(attributesFormElement).find('select')[0]).on('click', getOptionElement);

});

function getOptionElement() {
    $($(attributesFormElement).find('input')).value = '';
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
    $.ajax({
        url: url,
        method: "get",
        data: { id: groupAttributeId }
    }).done((response) => {
        fillAttributeNames(response);
    });
}

function fillAttributeNames(response) {

    //élément select cible
    let attributeNamesDatalist = $(attributesFormElement).find('select')[1];

    //On vide l'élément de ses balises option
    attributeNamesDatalist.innerHTML = '';

    attributes = JSON.parse(response);

    if (attributes.length == 0) {

        let option = document.createElement("option");
        option.value = "Aucun attribut appartenant à ce groupe en bdd";
        option.textContent = "Aucun attribut appartenant à ce groupe en bdd";
        attributeNamesDatalist.appendChild(option);

    } else {

        //On vide l'élément de ses balises option
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

        for (let attribute of attributeNames) {
            let option = document.createElement("option");
            option.value = attribute.name;
            option.textContent = attribute.name;
            attributeNamesDatalist.appendChild(option);
            option.addEventListener('click', fillAttributeNamesInput);
        }

        attributeNamesDatalist.setAttribute('size', attributeNames.length);
    }

}

function fillAttributeNamesInput() {
    $id = this.parentElement.id.replace('_name_list', '').replace('products_attribute_', '');
    let selectElementId = this.parentElement.id;
    let attachedInput = document.querySelector('[list=' + selectElementId + ']')
    attachedInput.value = this.value;
    fillAttributeContents(this.value);
}

function fillAttributeContents($value) {

    //élément select cible
    let attributeContentsDatalist = $(attributesFormElement).find('select')[2];

    //On vide l'élément de ses balises option
    attributeContentsDatalist.innerHTML = '';

    $countValidAttributes = 0;
    for (let attribute of attributes) {
        if ($value == attribute.name) {
            $countValidAttributes++;
            let option = document.createElement("option");
            option.value = attribute.value;
            option.textContent = attribute.value;
            attributeContentsDatalist.appendChild(option);
            option.addEventListener('click', fillAttributeContentsInput);
        }
    }

    attributeContentsDatalist.setAttribute('size', $countValidAttributes);
}

function fillAttributeContentsInput() {

    let selectElementId = this.parentElement.id;
    let attachedInput = document.querySelector('[list=' + selectElementId + ']')
    attachedInput.value = this.value;
}

