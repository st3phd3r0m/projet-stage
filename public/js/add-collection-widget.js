let newDivElement; 
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

    newDivElement = $(newElement).children();

    $(newDivElement).on('click', getOptionElement);
}

function getOptionElement() {
    let selects = $($(this).find('select'));
    let options = $(selects[0]).find('option');
    options.on('click', ajaxCall);
}

function ajaxCall() {

    //Récupération url absolue de la requete
    let url = $('#attribute-fields-list').data('url-path');
    
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

    //élément datalist cible
    let attributeNamesDatalist = $(newDivElement).find('select')[1];

    //On vide l'élément de ses balises option
    attributeNamesDatalist.innerHTML = '';

    attributes = JSON.parse(response);

    // Récupération des noms d'attributs (sans doublons)
    let attributeNames = [];
    for (let attribute of attributes) {
        if( ! attributeNames.find( entry => entry.name == attribute.name ) ){
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

    attributeNamesDatalist.setAttribute('size', attributeNames.length );
}

function fillAttributeNamesInput(){
    this.parentElement.previousElementSibling.previousElementSibling.value = this.value;
    fillAttributeContents(this.value);
}

function fillAttributeContents($value){

    //élément datalist cible
    let attributeContentsDatalist = $(newDivElement).find('select')[2];

    //On vide l'élément de ses balises option
    attributeContentsDatalist.innerHTML = '';

    $countValidAttributes = 0;
    for (let attribute of attributes) {
        if($value == attribute.name){
            $countValidAttributes++;
            let option = document.createElement("option"); 
            option.value = attribute.value;
            option.textContent = attribute.value;
            attributeContentsDatalist.appendChild(option);
            option.addEventListener('click', fillAttributeContentsInput);
        }
    }

    attributeContentsDatalist.setAttribute('size', $countValidAttributes );
}

function fillAttributeContentsInput(){
    this.parentElement.previousElementSibling.previousElementSibling.value = this.value;
}



