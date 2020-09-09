/**Code js destiné à la gestion d'ajout dynamiques d'éléments de fomulaire pour l'édition et la création d'un produit ou d'une catégorie.
 * Les ajouts d'éléments permettent d'associer plusieurs images, plusieurs catégories ou plusieurs attributs à un produit lors de son édition/création.
 * On peut aussi ajouter plusieurs images à une catégorie lors de son édition/création.
 * 
*/


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
        //On met un écouteur d'événements sur chacun des champs <select> de selection d'un groupe d'attributs
        $(firstSelectElement).on('click', getOptionElement);

        //Une balise <a> dans le template sert accéssoirement à supprimer un attribut du produit
        //Un écouteur d'événement y est adossé
        $(attributesFormElement).parent().find('a').on('click', removeElementFromProduct);
    }

    //Pour les champs catégorie pré-remplis (mapping des catégories qui sont déjà liés au produit en bdd) :
    //Selection des éléments li de la collection des catégories
    let categoriesFormElements = $('#categories-fields-list>li');
    
    for (let categoriesFormElement of categoriesFormElements) {
        //Unne balise <a> dans le template sert accéssoirement à supprimer une catégorie du produit
        //Un écouteur d'événement y est adossé
        $(categoriesFormElement).find('a').on('click', removeElementFromProduct);
    }

    //Pour les champs images pré-remplis (mapping des images qui sont déjà liés au produit en bdd) :
    //Selection des éléments li de la collection des images
    let imagesFormElements = $('#images-fields-list>li');
    
    for (let imagesFormElement of imagesFormElements) {
        //Unne balise <a> dans le template sert accéssoirement à supprimer une catégorie du produit
        //Un écouteur d'événement y est adossé
        $(imagesFormElement).find('a').on('click', removeElementFromProduct);
    }
});

/**
 * Cette fonction ajoute un groupe de champs "attributs", à l'image des prototypes délivrés 
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

    //Tant que l'utilisateur n'a pas sélectionné un groupe d'attributs, les zones "name" et "value" du formulaire du nouvel attribut sont cachés
    $($(divElement).children()[1]).hide();
    $($(divElement).children()[2]).hide();
    //On met un écouteur d'evenements le champ groupe d'attributs
    $($(divElement).children()[0]).on('change', showField);

    //Insertion dans le DOM d'une balise <a> pour la suppression individuelle d'attributs
    if(divElement.id.includes('products_attribute')){
        let deleteLink = document.createElement('a');
        deleteLink.setAttribute('href','#');
        deleteLink.classList.add("float-right");
        deleteLink.textContent ="Retirer l\'attribut du produit ?";
        divElement.parentElement.prepend(deleteLink);
        deleteLink.addEventListener('click', removeElementFromProduct);
    }

}

/**
 * Cette fonction ajoute un groupe de champs "catégories" ou "images", à l'image des prototypes délivrés 
 * par les collectionType du formulaire ProductsType 
 */
function addAnotherCollectionWidget(event) {

    event.preventDefault();

    let list = $($(this).data('list-selector'));
    //Donne le nombre d'éléments dans la liste de collection, soit via le data dans l'élément 'list', soit
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

    //Ajout d'un écouteur d'évènements sur les balises option de la première balise select dans le nouvel élément
    let selectElement = $(newElement).children()[0];


    //On veut afficher l'image que l'utilisateur vient de sélectionner
    if(selectElement.id.includes('_images_')){
        //Champ input d'ajout d'image
        let input = $(selectElement).find('input')[0];
        //Création d'une balise <img>
        let img = document.createElement('img');
        $(img).css('width', '200px').addClass('img-fluid mb-1 d-none');
        //Insertion de l'élément <img> dans le parent
        selectElement.appendChild(img);
        //Ecouteur d'événements sur le champ <input>
        $(input).on('change', pickFileName);

        //Insertion dans le DOM d'une balise <a> pour la suppression individuelle d'images
        let deleteLink = document.createElement('a');
        deleteLink.setAttribute('href','#');
        deleteLink.classList.add("ml-5");
        deleteLink.textContent ="Retirer l'image ?";
        newElement.prepend(deleteLink);
        deleteLink.addEventListener('click', removeElementFromProduct);
    }

    //Insertion dans le DOM d'une balise <a> pour la suppression individuelle de catégories
    if(selectElement.id.includes('products_category')){
        let deleteLink = document.createElement('a');
        deleteLink.setAttribute('href','#');
        deleteLink.classList.add("float-right");
        deleteLink.textContent ="Retirer la catégorie du produit ?";
        newElement.prepend(deleteLink);
        deleteLink.addEventListener('click', removeElementFromProduct);
    }
}

/**
 * Fonction qui récupère l'identifiant du formulaire d'attribut sélectionné par l'utilisateur
 * et qui vide les champs texte des zones "nom d'attribut" ("name") et "valeur d'attribut" ("value")
 */
function getOptionElement() {

    //Récupération du numéro de l'élémént de formulaire de collection cliqué dans la zone "groupe d'attributs"
    id = this.id.replace('_attribute_group','').replace('products_attribute_','');
    //On vide les champs <input> et <textarea> de l'élément de formulaire de collection
    let input =  $(attributesFormElements[id]).find('input')[0];
    input.value = '';
    let textarea =  $(attributesFormElements[id]).find('textarea')[0];
    textarea.value = '';

    //On met un ecouteur d'évenement toutes les balises <option> du selecteur de groupe d'attributs
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
    //Requete ajax methode get
    $.ajax({
        url: url,
        method: "get",
        data: {id: groupAttributeId}
    }).done((response)=>{
        fillAttributeNames(response);
    });
}

/**
 * Fonction qui affiche, dans la balise <select> de la zone "name" de l'attribut, les noms d'attributs 
 * obtenus via la requête ajax 
 */
function fillAttributeNames(response){
    //élément select cible
    let attributeNamesDatalist = $(attributesFormElements[id]).find('select')[1];

    //On vide l'élément de ses balises option
    attributeNamesDatalist.innerHTML = '';

    attributes = JSON.parse(response);

    if(attributes.length == 0){
        //Si le groupe d'attributs sélectionné n'est lié à aucun attribut , on affiche un message utilisateur dans la balise <select>
        let option = document.createElement("option"); 
        option.value = "Aucun attribut appartenant à ce groupe en bdd";
        option.textContent = "Aucun attribut appartenant à ce groupe en bdd";
        attributeNamesDatalist.appendChild(option);

    }else{
        //Sinon
        //On vide l'élément de ses balises option, pour en créer de nouvelles
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

        //Création d'une balise <option> pour chacun des attributs récupérés
        for (let attribute of attributeNames) {
            let option = document.createElement("option"); 
            option.value = attribute.name;
            option.textContent = attribute.name;
            attributeNamesDatalist.appendChild(option);
            //Un écouteur d'évenements est adossé à chacune des balises pour remplir la balise <input> avec le nom de l'attribut sélectionné 
            option.addEventListener('click', fillAttributeNamesInput);
        }
        //Ajustement de la taille de la balise <select> pour affichage de tous les noms d'attributs appartenants au groupe d'attributs sélectionné par l'utilisateur
        attributeNamesDatalist.setAttribute('size', attributeNames.length+1 );
    }   
}

/**
 * Fonction qui remplit l'input "name" de l'attribut
 */
function fillAttributeNamesInput(){
    //Récupération du numéro de l'élémént de formulaire de collection cliqué
    let selectElementId = this.parentElement.id;
    id = selectElementId.replace('_name_list','').replace('products_attribute_','');
    //Sélection du champ input cible
    let attachedInput = document.querySelector('[list='+selectElementId+']');
    //Remplissage du champ
    attachedInput.value = this.value;
    //On met en paramêtre la valeur de remplissage dans une fonction qu'on appelle
    fillAttributeContents(this.value);
}

/**
 * Fonction qui remplit la balise <select> dans la zone "value" de l'attribut
 */
function fillAttributeContents($value){

    //élément select cible
    let attributeContentsDatalist = $(attributesFormElements[id]).find('select')[2];

    //Apparition du champ Contenu d'attribut
    $($(attributesFormElements[id]).children()[2]).show();

    //On vide l'élément de ses balises option
    attributeContentsDatalist.innerHTML = '';

    //On remplit la balise <select> avec des balises <option> avec toutes valeurs d'attribut qui ont le même nom d'attribut sélectionné par l'utilisateur
    $countValidAttributes = 0;
    for (let attribute of attributes) {
        if($value == attribute.name){
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
function fillAttributeContentsInput(){

    let selectElementId = this.parentElement.id;
    let attachedInput = document.querySelector('[list='+selectElementId+']')
    attachedInput.value = this.value;
}

/**
 * Fonction qui efface un par un les champs d'une catégorie, d'une image ou d'un attribut (même ceux mappés par les collectionType sur symfony) que l'utilisateur veut enlever d'un produit
 */
function removeElementFromProduct(event){
    event.preventDefault();
    this.parentElement.remove();
}

/**
 * Fonction qui fait apparaître le groupe de champs "nom d'attribut" ("name") si un groupe d'attributs a été sélectionné par l'utilisateur, ou, le groupe de champs "valeur d'attribut" ("value") si un nom d'attribut a été sélectionné par l'utilisateur
 */
function showField(){
    $(this).next().show();
    $(this).next().on('keyup',showField);
}

/**
 * Fonction qui affiche l'image sélectionnée par l'utilisateur dans le formulaire
 */
function pickFileName(){
    //Instanciation de FileReader
    var reader = new FileReader();
    //L'instance lit le fichier image
    reader.readAsDataURL(this.files[0]);

    //Récupération de l'élément <img>
    let img = $($(this).parents('fieldset')[0]).next()[0];

    //On met un écouteur d'événements sur l'instance reader
    reader.addEventListener("load", function () {
        //Une fois la lecture du fichier image par reader est terminée, on récupère l'url de l'image que l'on charge dans l'attribut src de la balise <img>
        img.src = reader.result;
      }, false);

    //On fait apparaître la balise <img> dans le DOM
    $(img).removeClass('d-none').addClass('d-inline');
}