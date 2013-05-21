/* Author: Gabriel Fojo 

*/

/*
* 
*    JQUERY ONLOAD
* 
*/ 

$(document).ready(function(){


 // Date Picker 1
  cal1 = new dhtmlxCalendarObject('calendario1',true);
  cal1.loadUserLanguage("es");
  cal1.setPosition(170,210);       
  cal1.attachEvent("onClick",function(date){
     var d= cal1.getFormatedDate('%d/%m/%Y', date);
     $("#start_date").val(d);
     cal1.hide();
  })
  cal1.hide();
 $("#start_date").click(function(){cal1.show();})

 // Date Picker 2
  cal2 = new dhtmlxCalendarObject('calendario2',true);
  cal2.loadUserLanguage("es");
  cal2.setPosition(170,210);
  cal2.hide();

  cal2.attachEvent("onClick",function(date){
     var d= cal2.getFormatedDate('%d/%m/%Y', date);
     $("#end_date").val(d);
     cal2.hide();
  })

 $("#end_date").click(function(){cal2.show();})

 // Cancelar
 $("#cancelar").click(function(){
     parent.lightbox_close($('#id_dhtmlx').val());
 });

 // Borrar
$("#borrar").click(function(){
    $( "#dialog" ).dialog({ buttons: { 
            "Si": function() { 
                $(this).dialog("close"); 
                     var id=$('#id').val();
                     $.post('{module_url}main/delete_event/',{eventID:id},function(data){
                       if(data.length>1)parent.msg(data);
                       console.log(data);
                       parent.lightbox_close($('#id_dhtmlx').val());
                    });
                  parent.refresh(); 

            },
            "No": function() { $(this).dialog("close");} 
     }});
});

/**
*       
*   Validaciones
*
* */


// Validate titulo
$("#titulo").click(function(){
if($(this).hasClass('error')){
  $(this).removeClass("error");
  $(this).val("");  
}
});

$("#estado").val("{estado}");

// xxxx Guardar
$("#guardar").click(function(){

    var s_date=$('#start_date').val();
    var s_date2=s_date.split("/");
    var sd=s_date2[0];
    var sm=s_date2[1];
    var sy=s_date2[2];
    var sh=$('#start_hour').val();
    var smi=$('#start_minute').val();             
    var start_date=sd+"_"+sm+"_"+sy+"_"+sh+"_"+smi;

    var e_date=$('#end_date').val();
    var e_date2=e_date.split("/");
    var ed=e_date2[0];
    var em=e_date2[1];
    var ey=e_date2[2];
    var eh=$('#end_hour').val();
    var emi=$('#end_minute').val();             
    var end_date=ed+"_"+em+"_"+ey+"_"+eh+"_"+emi;
 

    json = {};
    json["event_id"]=$('#id').val();
    json["id_dhtmlx"]=$('#id_dhtmlx').val();
    json["agendaID"]= $("#agenda").val() || [];
    var Qagendas=json["agendaID"].length;
    json["event_name"]=$("#titulo").val();
    json["latLng"]=$("#latLng").val();
    json["tema"]=$("#tema").val();
    json["detalle"]=$("#detalle").val();
    json["lugar"]=$("#lugar").val();
    json["estado"]=$('#estado option:selected').val();
    json["start_date"]=start_date;
    json["end_date"]=end_date;
    json["mod"]=1;
    json=JSON.stringify(json);

    // Validaciones 
    var trap=0;
    if($("#titulo").val()=="" || $("#titulo").val()=="Complete este campo" ){    
        // title check
        $("#titulo").val("Complete este campo");
        $("#titulo").addClass("error");
        trap=1;
    }

    if(!Qagendas){
        trap=1;
        parent.pop('Error','No ha elegido una agenda');
    }  

    if(!trap){   

        $.post('{module_url}main/lightbox_save_event/',{},function(data){
            alert(data);
        });
//        $.post('{module_url}main/lightbox_save_event/',{evento:json},function(data){
//           if(data.show==true){  
//              parent.msg(data.msg);   
//           } 
//           parent.lightbox_close($('#id_dhtmlx').val());
//        },'json');
        parent.refresh();
    }


    


});

// xxxxxxxxxxx Dates control
// Start Hour validation

$("#start_hour").change(function(){
    sh=parseInt($("#start_hour").val());
    if(sh<8)sh=8;
    if(sh>22)sh=22;
    eh=sh+1;
    sh="00"+sh;
    sh=sh.match(/[0-9]{2}$/);
    $("#start_hour").val(sh);
    eh="00"+eh;
    eh=eh.match(/[0-9]{2}$/);
    $("#end_hour").val(eh);
});

// Start Minute validation and end minute adjust
$("#start_minute").bind('change',function(){
    
    sm=parseInt($("#start_minute").val());
    sm=checkMin(sm);
    $("#start_minute").val(sm);
    $("#end_minute").val(sm);
    alert(sm);
});

// End hour validation
$("#end_hour").change(function(){
    eh=parseInt($("#end_hour").val());
    eh= checkHour(eh);
    $("#end_hour").val(eh);

});

// End Minutes Validation
$("#end_minute").change(function(){
    sm=parseInt($("#end_minute").val());
    sm=checkMin(sm);
    $("#end_minute").val(sm);
});

// Map Button
$(".bt_gmap").click(function(e){
//    var map_loaded=$('#map_loaded').val();
//    // JS loaded on demand
//    if(map_loaded==0){
//    $('#map_loaded').val('1');
//    Modernizr.load({
//      load: '{module_url}assets/jscript/gmap.js',
//      callback: function (url, result, key) {
//        alert(url);
//      },
//      complete:function(){
//          init_map();
//      }
//    });
//
// };
    init_map();
$('#map').slideToggle();
    e.preventDefault();
});

     
            
            
}); // Jquery onload
 
/*
* 
*    FUNCIONES
* 
*/ 


function sqlDate(date){
Y=date.substr(6, 4);
M=date.substr(3, 2);
D=date.substr(0, 2);
return Y+"/"+M+"/"+D;
}

function checkHour(h){
    if(h<8)h=8;
    if(h>22)h=22;
    h="00"+h;
    h=h.match(/[0-9]{2}$/);
    return h;
}

function checkMin(m){
    if(m<0)m=0;
    if(m>59)m=59;
    m="00"+m;
    m=m.match(/[0-9]{2}$/);
    return m;
}

