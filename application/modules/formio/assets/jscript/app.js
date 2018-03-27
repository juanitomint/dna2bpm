/**
 * Main JS
 * Author: Gabriel Fojo
**/
angular.module('formioForm', ['formio']);

$('body').on('click', '.delete-resource', function(e){
    e.preventDefault();
    var element = $(this);
    $.ajax({
        url: element.attr('href'),
        method: "DELETE",
        dataType: 'json',
        success: function(r){
            if(r.response) {
                element.parent().parent().hide();
            }
        }
    });
});