//Code js destiné à la gestion des fomulaires d'édition et de création d'un produit et d'une catégorie

let id; 
let attributesFormElements;
let attributes;
let counter = 0;

$(document).ready(function () {
    //Ecouteur d'évémenents sur le bouton d'ajout de champ (en lien avec les collectionType) 
    //Ajout champ attribut du produit
    $('.add-another-attribute-collection-widget').on('click', addAnotherAttributeCollectionWidget);
    //Ajout champ image du produit
    $('.add-another-image-collection-widget').on('click', addAnotherCollectionWidget);
    //Ajout champ categorie du produit
    $('.add-another-category-collection-widget').on('click', addAnotherCollectionWidget);

    //Un champ ajoutés via le prototype du collectionType est encapsulé par des balises <li>
    //Selection des éléments li>div de la collection des attributs
    attributesFormElements = $('#attribute-fields-list>li>div');

    //Pour les champs attributs pré-remplis (mapping des attributs qui sont déjà liés au produit en bdd) :
    for (let attributesFormElement of attributesFormElements) {
        //On laisse la possibilité à l'uilisateur de modifier ces attributs.
        let firstSelectElement = $(attributesFormElement).find('select')[0];
        //On met un écouteur d'événements sur chacun des champ <select> de selection d'attribut
        $(firstSelectElement).on('click', getOptionElement);

        //Unne balise <a> dans le template sert accéssoirement à supprimer un attribut du produit
        //Un écouteur d'événement y est adossé
        $(attributesFormElement).find('a').on('click', removeAttributeFromProduct);
    }

    //Pour les champs catégorie pré-remplis (mapping des catégories qui sont déjà liés au produit en bdd) :
    //Selection des éléments li de la collection des catégories
    let categoriesFormElements = $('#categories-fields-list>li');
    
    for (let categoriesFormElement of categoriesFormElements) {
        //Unne balise <a> dans le template sert accéssoirement à supprimer une catégorie du produit
        //Un écouteur d'événement y est adossé
        $(categoriesFormElement).find('a').on('click', removeCategoryFromProduct);
    }

});

/**
 * Cette fonction ajoute un groupe de champs, à l'image des prototypes délivrés 
 * par les collectionType du formulaire ProductsType 
 */
function addAnotherAttributeCollectionWidget(event) {

    event.preventDefault();

    let list = $($(this).data('list-selector'));
    //Donne le nombre d'éléments dans la liste de collection, soit via l'attribut data de l'élémént 'list', soit en utilisant la méthode children
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

    // Création d'un nouvel élément encapsulé dans une balise <li> grace au prototype encodé dans 'newWidget'
    let newFieldsGroup = $(list.data('widget-tags')).html(newWidget);
    //Insertion du nouvel élément dans l'élément DOM 'list' 
    newFieldsGroup.appendTo(list);
    // Ajout du nouvel élément dans un array
    attributesFormElements.push($(newFieldsGroup).children()[0]);

    //Dans le champ "goupe d'attributs" (première balise <select> du nouveau groupe de champs), on veut récupérer le groupe d'attributs choisi par l'utilisateur
    //I
    let divElement = $(newFieldsGroup).children()[0];
    let firstSelectElement = $(newFieldsGroup).find('select')[0];
    //Ajout d'un écouteur d'évenements sur la première balise <select> dans le nouvel élément
    $(firstSelectElement).on('click', getOptionElement);

    //Tant que l'utilisateur n'a pas sélectionné un groupe d'attributs, 
    $($(divElement).children()[1]).hide();
    $($(divElement).children()[2]).hide();

    $($(divElement).children()[0]).on('change', showField);

    //Insertion dans le DOM d'une balise <a> pour la suppression individuelle d'attribut
    if(divElement.id.includes('products_attribute')){
        let deleteLink = document.createElement('a');
        deleteLink.setAttribute('href','#');
        deleteLink.classList.add("float-right");
        deleteLink.textContent ="Enlever l\'attribut du produit ?";
        divElement.prepend(deleteLink);
        deleteLink.addEventListener('click', removeAttributeFromProduct);
    }

}

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

    //Ajout d'un écouteur d'évenements sur les balises option de la première balise select dans le nouvel élément
    let selectElement = $(newElement).children()[0];


    if(selectElement.id.includes('_images_')){

        //Champ input d'ajout d'image
        let input = $(selectElement).find('input')[0];
        //Création d'une balise <img>
        let img = document.createElement('img');
        $(img).addClass('img-fluid w-25 ml-5 mb-1 d-none');
        //Insertion de l'élément <img> dans le parent
        selectElement.appendChild(img);
        //Ecouteur d'événements sur le champ <input>
        $(input).on('change', pickFileName);
    }

    //Insertion dans le DOM d'une balise <a> pour la suppression individuelle d'attribut
    if(selectElement.id.includes('products_category')){
        let deleteLink = document.createElement('a');
        deleteLink.setAttribute('href','#');
        deleteLink.classList.add("float-right");
        deleteLink.textContent ="Enlever la catégorie du produit ?";
        newElement.prepend(deleteLink);
        deleteLink.addEventListener('click', removeCategoryFromProduct);
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

    //Apparition du champ Nom d'attribut
    $($(attributesFormElements[id]).children()[2]).show();

    //On vide l'élément de ses balises option
    attributeNamesDatalist.innerHTML = '';

    attributes = JSON.parse(response);

    if(attributes.length == 0){

        let option = document.createElement("option"); 
        option.value = "Aucun attribut appartenant à ce groupe en bdd";
        option.textContent = "Aucun attribut appartenant à ce groupe en bdd";
        attributeNamesDatalist.appendChild(option);

    }else{

        //On vide l'élément de ses balises option
        attributeNamesDatalist.innerHTML = '';

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

    //Apparition du champ Contenu d'attribut
    $($(attributesFormElements[id]).children()[3]).show();

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

function removeCategoryFromProduct(event){
    event.preventDefault();
    this.parentElement.remove();
}

function showField(){
    $(this).next().show();
    $(this).next().on('keyup',showField);
}

function pickFileName(){
    var reader = new FileReader();
    reader.readAsDataURL(this.files[0]);

    //Récupération de l'élément <img>
    let img = $($(this).parents('fieldset')[0]).next()[0];

    reader.addEventListener("load", function () {
        img.src = reader.result;
      }, false);

    $(img).removeClass('d-none').addClass('d-inline');
}