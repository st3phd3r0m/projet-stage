let pointTablette = 991.98;

$(document).ready(function() {

    $('[data-toggle="tooltip"]').tooltip();

    $('#list').on('click',displayList);

    $('#grid').on('click',displayGrid);
    
    $(document).on('scroll', switchBanner);

    $(window).on('resize', switchBanner);

    $('[data-toggle="offcanvas"]').on('click', function() {
        $('.offcanvas-collapse').toggleClass('open');
        $('#burgerAndBannerWrapper').toggleClass('pushButton');
    });


});

function displayList(event){
    event.preventDefault();
    $('#products .item').addClass('list-group-item');
}

function displayGrid(event){
    event.preventDefault();
    $('#products .item').removeClass('list-group-item');
    $('#products .item').addClass('grid-group-item');
}

function switchBanner(){

    let positionScroll = $(document).scrollTop();

    if (positionScroll > 0 && $(window).width() < pointTablette ) {
        $("#logoMobile").show();
    } else {
        $("#logoMobile").hide();
    }

};

