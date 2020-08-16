$(document).ready(function () {

    $('#categories-fields-list').hide();
    $('#pages-fields-list').hide();
    $('#external-link-field').hide();
    $('#links_type').on('change',ajaxCall);



});

function ajaxCall(){

    let url;
    let targetElement;

    if(this.value === '/categorie/'){
        targetElement = $('#categories-fields-list');
        $('#pages-fields-list').hide();
        $('#external-link-field').hide();
        $(targetElement).show();
        url = $('#categories-fields-list').data('url-path');
    }else if(this.value === '/page/'){
        targetElement = $('#pages-fields-list');
        $('#categories-fields-list').hide();
        $('#external-link-field').hide();
        $(targetElement).show();
        url = $('#pages-fields-list').data('url-path');
    }else if(this.value === 'external'){
        $('#categories-fields-list').hide();
        $('#pages-fields-list').hide();
        $('#external-link-field').show();
        return false;
    }else{
        $('#categories-fields-list').hide();
        $('#pages-fields-list').hide();
        $('#external-link-field').hide();
        return false;
    }

    $.ajax({
        url: url,
        method: "get"
    }).done((response) => {
        //On vide l'élément de ses balises option
        items = JSON.parse(response);
        if (items.length == 0) {
            let option = document.createElement("option");
            option.value = "Aucun élément de type en bdd";
            option.textContent = "Aucun élément de type en bdd";
            targetElement.children.appendChild(option);
    
        } else {
            //On vide l'élément de ses balises option
            $(targetElement).find('select')[0].innerHTML = '';
            // Récupération des catégories ou pages de publication
            for (let item of items) {
                let option = document.createElement("option");
                option.value = item.slug;
                option.textContent = item.title;
                $(targetElement).find('select')[0].appendChild(option);
            }
        }
    });
}



