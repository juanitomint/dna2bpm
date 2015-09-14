/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. ---
 */

jQuery(document).ready(function($) {
    var base_url=globals['base_url'];
    var lang={'english':'eng','spanish':'es'};




 /* ----------- initialize the calendar --------*/

    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        editable: true,
        lang:lang[globals.lang],
        droppable: true, // this allows things to be dropped onto the calendar !!!
        eventDrop: function( event, delta, revertFunc, jsEvent, ui, view ){
            //==== drag event
           // console.log(event);
            var myevent={
                start: event.start._d.toISOString(),
                end: event.end._d.toISOString(),
                _id:event._id
            }        
            $.post(base_url+'calendar/update_event',{'event':myevent},function(resp){
                console.log(resp);
            });

        },
        eventClick: function(calEvent, jsEvent, view) {
            // alert('Coordinates: ' + jsEvent.pageX + ',' + jsEvent.pageY);
            // alert('View: ' + view.name);
                var myid=calEvent._id;
            
                $.post(base_url+'calendar/get_event_by_id',{'id':myid},function(resp){
                    $event=JSON.parse(resp);
                     $('#myModal .modal-body').html($event.body);
                     $('#myModal .modal-title').html($event.title);
                     init_range2();
                     $('#myModal').modal();
                });

                
        },
        eventResize: function(event, delta, revertFunc) {
            var myevent={
                start: event._start._d.toISOString(),
                end: event._end._d.toISOString(),
                _id:event._id
            }    
            $.post(base_url+'calendar/update_event',{'event':myevent},function(resp){
                console.log(resp);
            });
        },
		events: {
			url:  base_url+'calendar/get_events',
			type: 'GET',
			error: function() {
				console.log('error');
			},
			success: function(e) {
				//console.log(e);
			}
		},
		loading: function(bool) {
        console.log('loading');
    	}
    });



//========= Date range picker 

var options={
    "timePicker": true,
    "timePicker24Hour": true,
    "timePickerIncrement": 1,
    "locale": {
        "format": "DD/MM/YYYY HH:mm",
        "separator": " - ",
        "applyLabel": "Apply",
        "cancelLabel": "Cancel",
        "fromLabel": "From",
        "toLabel": "To",
        "customRangeLabel": "Custom",
        "daysOfWeek": [
            "Do",
            "Lu",
            "Ma",
            "Mi",
            "Ju",
            "Vi",
            "Sa"
        ],
        "monthNames": [
            "Enero",
            "Febrero",
            "Marzo",
            "Abril",
            "Mayo",
            "Junio",
            "Julio",
            "Agosto",
            "Septiembre",
            "Octubre",
            "Noviembre",
            "Diciembre"
        ],
        "firstDay": 1
    },
    "opens": "center",
    "drops": "down",
    "buttonClasses": "btn btn-sm",
    "applyClass": "btn-success",
    "cancelClass": "btn-default"
};

//==== dateRange create
init_range();
function init_range(){
var start = moment();
var end = moment().add(30,'minutes');

$('.range').daterangepicker(options, function(start, end, label) {});
var drp = $('.range').data('daterangepicker');
$('.range').data('daterangepicker').setStartDate(start);
$('.range').data('daterangepicker').setEndDate(end);

}

//==== dateRange Modal
var options2=options;
options2.parentEl='#myModal';
init_range2();
function init_range2(){
 $('.modal_range').daterangepicker(options2, function(start, end, label) {
});


}


//========= Color picker eventos

$('#event-color-picker a').on('click',function(e){
    e.preventDefault();
     var mycolor=$(this).attr('data-color');
     $('#event-color-picker #event-color').val(mycolor);
     $('#event-color-picker button i').css('color',mycolor);

});

//========= Color picker modal

$(document).on('click','#modal-color-picker a',function(e){
      e.preventDefault();
       var mycolor=$(this).attr('data-color');
       $('#modal-color-picker #modal-color').val(mycolor);

       $('#modal-color-picker #modal-color-box').css('color',mycolor);
});


//========= Add events 

$("#add-new-event").click(function(e) {
console.log("--- Adding Event ---");               
e.preventDefault();
//Get value and make sure it is not null
var titulo = $("#event-title").val();
var body = $("#event-body").val();
var allDay = $("#allday").prop('checked'); 
var group = $("#event-groups").val();
var color = $("#event-color").val();

if (titulo.length == 0) {
    return;
}

var intervalraw = $("#event-interval").val(); 
var event={
    title: titulo,
    interval: intervalraw,
    body:body,
    allDay:allDay,
    group:group,
    color:color
}     


$.post(base_url+'calendar/create_event_wrapper',{event:event},function(resp){
    console.log(resp);
 if(resp==1)
    $('#calendar').fullCalendar('refetchEvents');
});

    
});

//========= Modal delete

$('#myModal').on('click','#modal_delete',function(e){
    var myid=$(this).attr('data-id');
    $.post(base_url+'calendar/delete_event',{'id':myid},function(resp){
     if(resp==1)
        $('#calendar').fullCalendar('refetchEvents');
        $('#myModal').modal('hide');
    });
});


//========= Modal save

$('#myModal').on('click','#modal_save',function(e){
    var myid=$(this).attr('data-id');
    var body=$(this).parents('#myModal').find('#modal_detail').val();
    var group=$(this).parents('#myModal').find('#modal_group').val();
    var allDay=$(this).parents('#myModal').find('#modal_allDay').prop('checked');
    var intervalraw = $("#modal-event-interval").val(); 
    var color = $('#modal-color-picker #modal-color').val();
    var title = $('#modal-color-picker #modal-title').val();
    var myevent={
        body: body,
        _id:myid,
        allDay:allDay,
        intervalraw:intervalraw,
        group:group,
        color:color,
        title:title
    }

    $.post(base_url+'calendar/update_event',{'event':myevent},function(resp){
        console.log(resp);
     if(resp==1)
        $('#calendar').fullCalendar('refetchEvents');
        $('#myModal').modal('hide');
    });
    
});



//==  ready
});

