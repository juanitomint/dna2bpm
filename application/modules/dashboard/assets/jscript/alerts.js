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
    var fecha=moment();
    var myalert={};
    $.each( raw, function( i, field ) {
     myalert[field.name]=field.value;
    });
    
    myalert['start_date']=moment.utc(myalert['start_date'],'DD/MM/YYYY HH:mm').format('YYYY-MM-DD HH:mm'); 
    myalert['end_date']=moment.utc(myalert['end_date'],'DD/MM/YYYY HH:mm').format('YYYY-MM-DD HH:mm'); 

  console.log(myalert);
    $.post(base_url+'dashboard/alerts/create_alert',{myalert:myalert},function(resp){
        console.log(resp);
    });
});
    
    
init_range();
function init_range(){
    $('.range').daterangepicker({
    singleDatePicker: true,
    timePicker: true,
    locale: {
            format: 'DD/MM/YYYY HH:mm '
    },
    timePickerIncrement: 30,
    timePicker24Hour: true,
    opens: "right",
    drops: "down",
    buttonClasses: "btn btn-sm",
    applyClass: "btn-success",
    cancelClass: "btn-default"
}, function(start, end, label) {

});


}


    
    

});

