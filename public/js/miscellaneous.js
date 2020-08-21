//La variable pointDesktop (déclarée en global) correspond à la supposée largeur minimale 
//en pixel d'un ordinateur de bureau
let pointDesktop = 992;

$(document).ready(function() {

    //fonctionnalité grid/list-------------------------------------//
    //Fonction tooltip (de boostrap) appliquée aux éléments #list et #grid
    $('[data-toggle="tooltip"]').tooltip();
    //Ecouteur d'événements sur les éléments #list et #grid
    $('#list').on('click',displayList);
    $('#grid').on('click',displayGrid);
    
    //Barre de navigation-------------------------------------------//
    //Ecouteur d'événement sur la page web
    $(document).on('scroll', switchBanner);
    //Ecouteur d'événement sur la fenêtre
    $(window).on('resize', switchBanner);

    //Barre de navigation mobile------------------------------------//
    //Ecouteur d'événement sur le "bouton burger"
    $('[data-toggle="offcanvas"]').one('click', slideMenuToRight);

    //Ecouteur d'événement sur le chevron en bas à droite de la fenetre en position:fixed aller directement vers le haut du document
    $("#scrollChrevron").on("click", goToHomeSite);

});

/**
 * Fonction qui ajoute la classe 'list-group-item' au wrapper de la série de produits dans les templates;
 * Il en résulte que les cartes produits sont affichés sous la forme d'une liste
 */
function displayList(event){
    event.preventDefault();
    $('#products .item').addClass('list-group-item');
}

/**
 * Fonction qui enlève la classe 'list-group-item' au wrapper de la série de produits dans les templates;
 * Il en résulte que les cartes produits sont affichés sous la forme d'une grille
 */
function displayGrid(event){
    event.preventDefault();
    $('#products .item').removeClass('list-group-item');
}

/**
 * Fonction qui gére la position de la barre de navigation selon la taille de la fenêtre et la portion de document déroulé.
 * Fonction qui gére aussi, selon les mêmes critères (mais des conditions autres), l'apparition ou non de la version mobile du logo du site.
 */
function switchBanner(){

    //Position du haut de la fenêtre dans le document déroulé
    let positionScroll = $(document).scrollTop();
    //Elément barre de navigation
    let navbar = $("#navbar");
    //Position vertical en pixel du haut de la navbar dans le document
    let positionNavbar = Math.round(navbar.offset().top);

    //Suivant la portion de document déroulé et suivant la taille de la fenêtre (donc de l'écran),
    //on fait apparaître le logo du site ou non.
    //La variable pointDesktop (déclarée plus haut en global) correspond à la supposée largeur minimale 
    //en pixel d'un ordinateur de bureau
    if (positionScroll > 0 && $(window).width() < pointDesktop ) {
        $("#logoMobile").show();
    } else {
        $("#logoMobile").hide();
    }

    //Suivant la portion de document déroulé et suivant la taille de la fenêtre (donc de l'écran),
    //on colle ou pas la barre de navigation en haut de la fenêtre via la classe css .stickyNavBar
    if ( positionScroll >= positionNavbar && $(window).width() > pointDesktop  ) {
        $("#navbarUL").addClass('stickyNavBar').addClass('container');
    } else {
        $("#navbarUL").removeClass('stickyNavBar').removeClass('container');
    }
};


/**
 * Fonction qui fait apparaître le menu de navigation mobile via la classe css .open.
 * La fonction pousse vers la droite le "bouton burger" qui active/désactive le menu
 */
function slideMenuToRight(){
    $('.offcanvas-collapse').toggleClass('open');
    $('#burgerAndBannerWrapper').toggleClass('pushButton');
    $(clickCloseMenu);
}

/**
 * Fonction qui prend en compte l'événement "click" de souris pour la fermeture du menu mobile
 */
function clickCloseMenu(){
    $(document).one("click", slideMenuToLeft);
}

/**
 * Fonction qui fait ferme le menu de navigation mobile via la classe css .open.
 * La fonction pousse vers la gauche (état initial) le "bouton burger" qui active/désactive le menu
 */
function slideMenuToLeft(){
    $('.offcanvas-collapse').removeClass('open');
    $('#burgerAndBannerWrapper').removeClass('pushButton');
    $('[data-toggle="offcanvas"]').one('click', slideMenuToRight);
}

/**
 * Fonction qui va vers le haut du document en scrolling
 */
function goToHomeSite() {

    $(this).addClass('clickChevron');

    $('html').animate({
        scrollTop: '0'
    }, function(){
        $("#scrollChrevron").removeClass('clickChevron');
    });

}