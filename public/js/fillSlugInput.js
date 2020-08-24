/**
 * Code qui prérempli le champ slug dans les formulaires "pages" et "categories"
 */
$(document).ready(function () {
    $("#categories_title").on('change', fillTextInput);
    $("#pages_title").on('change', fillTextInput);
});

function fillTextInput(){
    let id = this.id;
    $("#"+id.replace('title', 'slug')).val(this.value);
}