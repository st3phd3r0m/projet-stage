let id; 
let attributesFormElements;
let attributes;
let counter = 0;

$(document).ready(function () {
    //Ecouteur d'évémenents sur le bouton d'ajout de formulaire de collection (fonctionne pour l'ajout d'image et l'ajout d'attributs) 
    $('.add-another-collection-widget').on('click', addAnotherCollectionWidget);

    //Selection des éléments li>div de la collection des attributs
    attributesFormElements = $('#attribute-fields-list>li>div');

    for (let attributesFormElement of attributesFormElements) {
        let firstSelectElement = $(attributesFormElement).find('select')[0];
        $(firstSelectElement).on('click', getOptionElement);

        $(attributesFormElement).find('a').on('click', removeAttributeFromProduct);
    }

});

/**
 * Cette fonction ajoute un champs ou un groupe de champs, à l'image des prototypes délivrés 
 * par les collectionType du formulaire ProductsType 
 */
function addAnotherCollectionWidget(event) {

    event.preventDefault();

    let list = $($(this).data('list-selector'));
    //Donne le nombre d'éléments dans la liste de collection, soit via le data dans l'élémént 'list', soit
    //en utilisant la méthode children
    counter = list.data('widget-counter') || list.children().length;

    // Récupération en data du prototype qui se trouve dans l'élément 'list'
    let newWidget = list.data('prototype');

    // Remplacement, dans le prototype (qui est très basiquement une chaine de caractères), de 
    //"__name__" par un identifiant numérique unique qu'on incrémente par la suite : counter
    newWidget = newWidget.replace(/__name__/g, counter);
    // Incrémentation de counter
    counter++;
    // Ecrasement de counter en data dans l'élément 'list'
    list.data('widget-counter', counter);

    // Création d'un nouvel élément encapsulé dans un élément li grace au prototype encodé dans 'newWidget'
    let newElement = $(list.data('widget-tags')).html(newWidget);
    //Insertion du nouvel élément dans l'élément 'list' 
    newElement.appendTo(list);
    // Ajout du nouvel élément dans un array
    attributesFormElements.push($(newElement).children()[0]);
    //Ajout d'un écouteur d'évenements sur les balises option de la première balise select dans le nouvel élément
    let divElement = $(newElement).children()[0];
    let firstSelectElement = $(divElement).find('select')[0];
    $(firstSelectElement).on('click', getOptionElement);

    if(divElement.id.includes('products_attribute')){
        //Insertion dans le DOM d'une balise <a> pour la suppression individuelle d'attribut
        let deleteLink = document.createElement('a');
        deleteLink.setAttribute('href','#');
        deleteLink.classList.add("float-right");
        deleteLink.textContent ="Enlever l\'attribut du produit ?";
        divElement.prepend(deleteLink);
        deleteLink.addEventListener('click', removeAttributeFromProduct);
    }

}

function getOptionElement() {

    //Récupération du numéro de l'élémént de formulaire de collection cliqué
    id = this.id.replace('_attribute_group','').replace('products_attribute_','');
    //On vide les champs <input> de l'élément de formulaire de collection
    let inputs =  $(attributesFormElements[id]).find('input');

    for (let input of inputs) {
        input.value = '';
    }

    let options = $(this).find('option');
    options.on('click', ajaxCall);
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
        data: {id: groupAttributeId}
    }).done((response)=>{
        fillAttributeNames(response);
    });
}

function fillAttributeNames(response){

    //élément select cible
    let attributeNamesDatalist = $(attributesFormElements[id]).find('select')[1];

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
    $id = this.parentElement.id.replace('_name_list','').replace('products_attribute_','');
    let selectElementId = this.parentElement.id;
    let attachedInput = document.querySelector('[list='+selectElementId+']')
    attachedInput.value = this.value;
    fillAttributeContents(this.value);
}

function fillAttributeContents($value){

    //élément select cible
    let attributeContentsDatalist = $(attributesFormElements[id]).find('select')[2];

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

    let selectElementId = this.parentElement.id;
    let attachedInput = document.querySelector('[list='+selectElementId+']')
    attachedInput.value = this.value;
}



function removeAttributeFromProduct(event){
    event.preventDefault();

    this.parentElement.parentElement.remove();
}

