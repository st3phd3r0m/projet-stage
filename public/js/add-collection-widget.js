let selectGroup;
let attributes;
let counter = 0;

$(document).ready(function () {
    $('.add-another-collection-widget').on('click', addAnotherCollectionWidget);

});

function addAnotherCollectionWidget() {


    let list = $($(this).data('list-selector'));
    // Try to find the counter of the list or use the length of the list
    counter = list.data('widget-counter') || list.children().length;

    // grab the prototype template
    let newWidget = list.data('prototype');
    // console.log(newWidget);
    // replace the "__name__" used in the id and name of the prototype
    // with a number that's unique to your emails
    // end name attribute looks like name="contact[emails][2]"
    newWidget = newWidget.replace(/__name__/g, counter);
    // Increase the counter
    counter++;
    // And store it, the length cannot be used if deleting widgets is allowed
    list.data('widget-counter', counter);

    // create a new list element and add it to the list
    let newElement = $(list.data('widget-tags')).html(newWidget);
    newElement.appendTo(list);

    $(newElement).children().on('click', getOptionElement);

}

function getOptionElement() {
    let selects = $($(this).find('select'));
    selectGroup = $(selects[0]).attr('id');
    let options = $('#'+selectGroup).find('option');
    options.on('click', ajaxCall);
}

function ajaxCall() {

    //Récupération url absolue de la requete
    let url = $('#attributeGroupField-fields-list').data('url-path');
    
    //Récupération valeur de la balise option
    let groupAttributeId = this.value;

    $.ajax({
        url: url,
        method: "get",
        data: {id: groupAttributeId}
    }).done((response)=>{
        fillAttributeNames(response);
    });

}

function fillAttributeNames(response){
    //Identifiant de l'élément select cible
    let attributeNamesSelect = document.querySelector('#'+selectGroup.substring(0,31)+'_name');

    //On vide l'élément de ses balises option
    attributeNamesSelect.innerHTML = '';

    attributes = JSON.parse(response);

    // Récupération des noms d'attributs (sans doublons)
    let attributeNames = [];
    for (let attribute of attributes) {
        if( ! attributeNames.includes(attribute.name)){
            attributeNames.push(attribute.name);
        }
    }

    for (let attribute of attributeNames) {
        let option = document.createElement("option"); 
        option.textContent = attribute;
        attributeNamesSelect.appendChild(option);
        option.addEventListener('click', fillAttributeContents);
    }
}

function fillAttributeContents(){
    console.log(this.parentNode.id );
    //Identifiant de l'élément select cible
    let attributeContentsSelect = document.querySelector('#'+this.parentNode.id.substring(0,31)+'_value');

    //On vide l'élément de ses balises option
    attributeContentsSelect.innerHTML = '';

    console.log(attributes);

    for (let attribute of attributes) {
        if(this.value == attribute.name){
            let option = document.createElement("option"); 
            option.value = attribute.id;
            option.textContent = attribute.value;
            attributeContentsSelect.appendChild(option);
        }
    }

}



