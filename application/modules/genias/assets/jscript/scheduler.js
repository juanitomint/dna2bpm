/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


$( document ).ready(function() {


$('#calendar').fullCalendar({

    eventSources: [

        // your event source
        {
            url: globals.module_url+"/scheduler_get_json",
            type: 'POST',
            data: {
                custom_param1: 'something',
                custom_param2: 'somethingelse'
            },
            error: function() {
                alert('there was an error while fetching events!');
            },
            color: '#A4C8DB',   // a non-ajax option
            textColor: 'black' // a non-ajax option
        }

        // any other sources...

    ],
    eventClick: function(calEvent, jsEvent, view) {

        $('#detalle input[name="title"]').val(calEvent.title);
        $('#detalle input[name="start"]').val(calEvent.start);
        $('#detalle input[name="end"]').val(calEvent.end);
        $('#detalle input[name="detail"]').val(calEvent.detail);

    },
    header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
    }

});
		
});

