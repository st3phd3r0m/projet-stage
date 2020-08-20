$(document).ready(function () {
    $("#messages_subject").on('keyup', resetField);
});

function resetField(){
    $("#messages_subject").val($('article').first().text().trim());
    $("#messages_subject").attr('disabled','disabled');
    $("#messages_subject").attr('required','required');
    $('#messages_wished_date').focus();
} 