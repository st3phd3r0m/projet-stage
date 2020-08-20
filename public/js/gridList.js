let pointDesktop = 992;
let pointTablette = 576;

$(document).ready(function() {

    $('[data-toggle="tooltip"]').tooltip();

    $('#list').on('click',displayList);

    $('#grid').on('click',displayGrid);
    
    $(document).on('scroll', switchBanner);

    $(window).on('resize', switchBanner);

    $('[data-toggle="offcanvas"]').one('click', slideMenuToRight);

    // double Flèche en bas à droite de la fenetre en position:sticky pour retour à l'acceuil du site
    $("#scrollChrevron").on("click", goToHomeSite);

});

function displayList(event){
    event.preventDefault();
    $('#products .item').addClass('list-group-item');
}

function displayGrid(event){
    event.preventDefault();
    $('#products .item').removeClass('list-group-item');
}

function switchBanner(){

    let positionScroll = $(document).scrollTop();
    let navbar = $("#navbar");
    let positionNavbar = Math.round(navbar.offset().top);

    if (positionScroll > 0 && $(window).width() < pointDesktop ) {
        $("#logoMobile").show();
    } else {
        $("#logoMobile").hide();
    }

    if ( positionScroll >= positionNavbar && $(window).width() > pointDesktop  ) {
        $("#navbarUL").addClass('stickyNavBar').addClass('container');
    } else {
        $("#navbarUL").removeClass('stickyNavBar').removeClass('container');
    }
};

function slideMenuToRight(){
    $('.offcanvas-collapse').toggleClass('open');
    $('#burgerAndBannerWrapper').toggleClass('pushButton');
    $(clickCloseMenu);
}

function clickCloseMenu(){
    $(document).one("click", slideMenuToLeft);
}

function slideMenuToLeft(){
    $('.offcanvas-collapse').removeClass('open');
    $('#burgerAndBannerWrapper').removeClass('pushButton');
    $('[data-toggle="offcanvas"]').one('click', slideMenuToRight);
}

/**
 * Fonction qui va vers l'acceuil en scrolling
 * @param {*} event 
 */
function goToHomeSite() {
    window.scrollTo({
        top: 0,
        left: 0,
        behavior: 'smooth'
    });
}