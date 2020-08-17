$(document).ready(function() {

    $('[data-toggle="tooltip"]').tooltip();

    $('#list').on('click',displayList);

    $('#grid').on('click',displayGrid);
    
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