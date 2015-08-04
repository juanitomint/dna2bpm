/**
 * Main JS
 * Author: Gabriel Fojo
 **/

$(document).ready(function() {
 var base_url=globals['base_url'];
//Colores
$(document).on('click','#event-color-picker li a',function(e){
    e.preventDefault();
    
    var myclass=$(this).attr('data-class');
    
    $('#event-color-picker #event-class').val(myclass);
    $('#event-color-picker #alert-caret').removeClass('text-info text-primary text-success text-warning text-danger');
    $('#event-color-picker #alert-caret').addClass('text-'+myclass);
    
 ///  console.log(data);
});


$(document).on('submit','#alertform',function(e){
    e.preventDefault();
    raw=$(this).serializeArray();
    var myalert={};
    $.each( raw, function( i, field ) {
     myalert[field.name]=field.value;
    });


    $.post(base_url+'dashboard/alerts/create_alert',{myalert:myalert},function(resp){
        console.log(resp);
    });
    

});
    

});


