   </div>

        <!-- xxxxxxxxxxx  Scheduler  xxxxxxxxxxx -->

        <div id="scheduler_here" class="dhx_cal_container" style='width:100%;height:100%'>
                    <div class="dhx_cal_navline">
                        <div class="dhx_cal_prev_button">&nbsp;</div>
                        <div class="dhx_cal_next_button">&nbsp;</div>
                        <div class="dhx_cal_today_button"></div>
                        <div class="dhx_cal_date"></div>
                        <div class="dhx_cal_tab" name="day_tab" style="right:270px;"></div>
                        <div class="dhx_cal_tab" name="workweek_tab" style="right:204px"></div>
                        <div class="dhx_cal_tab" name="week_tab" style="right:140px"></div>
                        <div class="dhx_cal_tab" name="month_tab" style="right:76px;"></div>
                    </div>
                    <div class="dhx_cal_header">
                    </div>
                    <div class="dhx_cal_data">
                    </div
            </div>

        <div id="msg2" style="display:none"></div>
        <div id="pop1" title=""></div>   
        <div id="dialog" title=""></div> 

<!-- JavaScript at the bottom for fast page loading -->
  
<!-- AJAX Components JS -->
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxWindows/codebase/dhtmlxcommon.js" ></script> 

<!-- Scheduler -->
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxScheduler_v31/codebase/dhtmlxscheduler.js"  ></script>
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxScheduler_v31/codebase/ext/dhtmlxscheduler_recurring.js"  ></script> 
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxScheduler_v31/codebase/{locale}"></script>
<script src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxScheduler_v31/codebase/ext/dhtmlxscheduler_pdf.js"></script>

<!-- Calendar -->
<script type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar.js"></script>
<script type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxCalendar/codebase/dhtmlxcalendar_es.js"></script>

<!-- Tree -->
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTree/codebase/dhtmlxtree.js" ></script>
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTree/codebase/ext/dhtmlxtree_start.js" ></script>
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTree/codebase/ext/dhtmlxtree_ed.js" ></script>

<!-- Layout -->
<script type="text/javascript"  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxLayout/codebase/dhtmlxlayout.js"  ></script>
<script type="text/javascript"  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxLayout/codebase/dhtmlxcontainer.js"  ></script>

<!-- WindowX -->
<script  type="text/javascript" src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxWindows/codebase/dhtmlxwindows.js"></script>

<!-- Tabs -->
<script src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTabbar/codebase/dhtmlxtabbar.js"></script>
<script src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxTabbar/codebase/dhtmlxtabbar_start.js"></script>
<!-- ColorPicker -->
<script type="text/javascript"  src="{module_url}assets/jscript/dhtmlxSuite/dhtmlxColorPicker/codebase/dhtmlxcolorpicker.js"  ></script>

<!-- Jquery -->
<script src="{module_url}assets/jscript/libs/jquery-1.7.2.min.js"></script>
<script src="{module_url}assets/jscript/libs/jquery-ui-1.9.2.custom.min.js"></script>



<!-- scripts concatenated and minified via build script -->
<script src="{module_url}assets/jscript/plugins.js"></script>
<script src="{module_url}assets/jscript/script.js"></script>
<!-- end scripts -->

  

  
 <script type="text/javascript">              
                   
 // JQUERY ONLOAD //
$(document).ready(function(){

dhxWins = new dhtmlXWindows();               
dhxWins.setImagePath("{module_url}assets/jscript/dhtmlxSuite/dhtmlxWindows/codebase/imgs/");
        
        
//xxxxxxxxxxxxxx Scheduler xxxxxxxxxxxxxx

    scheduler.locale.labels.workweek_tab = "Semana-L"
    scheduler.config.xml_date="%Y-%m-%d %H:%i";
    scheduler.config.details_on_create = true;
    scheduler.config.first_hour = 8;
    scheduler.config.last_hour = 22;
    scheduler.config.multi_day = true;
    scheduler.config.show_loading=true;
    scheduler.config.details_on_dblclick=true;
    scheduler.config.full_day  = true;
    scheduler.config.time_step = 15;
    scheduler.config.event_duration = 30;
    scheduler.config.auto_end_date = true;
    scheduler.init('scheduler_here',null,"month");
    //scheduler.config.hour_size_px='50px'; //@todo:  Cookie

    scheduler.date.workweek_start = scheduler.date.week_start;
    scheduler.templates.workweek_date = scheduler.templates.week_date;
    scheduler.templates.workweek_scale_date = scheduler.templates.week_scale_date;
    scheduler.date.add_workweek=function(date,inc){ return scheduler.date.add(date,inc*7,"day"); }
    scheduler.date.get_workweek_end=function(date){ return scheduler.date.add(date,5,"day"); }

    var convert = scheduler.date.date_to_str("%j_%m_%Y_%H_%i");

    scheduler.attachEvent("onViewChange", function (mode , date){
         var convert2 = scheduler.date.date_to_str("%Y%m%d");
         $.get("{module_url}printer/set_print_mode/"+mode+"/"+convert2(date),{},function(data){console.log(data);});
         refresh(); 
         
    });

    // Agrega estrella en modo Mes para los autores
    scheduler.templates.event_bar_date=function(start,end,event){ // @todo: Chequear estrella
        if(event.autorID=='{idu}'){       
         return "<img src='{module_url}assets/images/star.png'  style='position:absolute' alt='Autor' title='Autor'/><span style='margin-left:12px;margin-right:4px'>"+scheduler.templates.hour_scale(start)+"</span>";
         }else{
         return "<span style='padding-right:4px'>"+scheduler.templates.hour_scale(start)+"</span>";
         }
     }

    // Dia y semana encabezados
     var format = scheduler.date.date_to_str("%l %d de %F");
      scheduler.templates.day_date=function(date){
         return format(date);
      }

     // Agrega las clases a los eventos x agendaID
     scheduler.templates.event_class=function(start,end,event){         
         if('agendaID' in event){
             return "agenda"+event.agendaID[0];
         }
        //
     }
     

     //Pone color en modo semanal o diario
     scheduler.templates.event_text=function(start,end,event){
        return "<span class='agenda"+event.agendaID+"' ><p style='font-weight:bold;padding:0px;margin:0px;' onclick='scheduler.showLightbox("+event.id+");'>"+event.text+"</p><p onclick='scheduler.showLightbox("+event.id+");'>"+event.detalle+"</p></span>";
    }

    // agrega la estrella para autores en modo semanal y diario
    scheduler.templates.event_header=function(start,end,event){
        if(event.autorID=='{idu'){
        return "<img src={module_url}assets/images/star.png' style='position:absolute;left:2px' alt='Autor' title='Autor'/>"+scheduler.templates.hour_scale(start);
         }else{
        return scheduler.templates.hour_scale(start);
         }
    }
     
// Event Changed
scheduler.attachEvent("onBeforeEventChanged", function(ev, native_event, is_new){
if(!is_new){
            json = {};
            json["event_id"]=ev.event_id;
            json["agendaID"]=$('#agenda option:selected').val();
            json["event_name"]=ev.text;
            json["start_date"]=convert(ev.start_date)
            json["end_date"]=convert(ev.end_date);
            json["mod"]=0;
            json=JSON.stringify(json);
                $.post('{module_url}main/lightbox_save_event/',{evento:json},function(data){
                   if(data.show==true){  
                      parent.msg(data.msg);   
                   }  
                },'json');

         
}
return true; 
});
         

// Event deleted
scheduler.attachEvent("onBeforeEventDelete", function(event_id){
   var ev = scheduler.getEvent(event_id);        
   var event_id=(ev.event_id)?(ev.event_id):(0);

   $.post('{module_url}main/delete_event/',{eventID:event_id},function(data){
     if(data.length>1)parent.msg(data);
       parent.dhxWins.window("lightbox").close();
  });
   parent.refresh(); 
  });

/* 
 * 
 *      LIGHTBOX
 * 
 * */


     scheduler.showLightbox=function(id){ 
        scheduler.startLightbox(id);
        var ev = scheduler.getEvent(id);             
        var event_id=(ev.event_id)?(ev.event_id):(0);
        sd=convert(ev.start_date);
        ed=convert(ev.end_date);
        var win_name="lb"+id;
        var win = dhxWins.createWindow(win_name, 1, 1, 500, 550); 

        dhxWins.window(win_name).attachURL('{module_url}main/get_lightbox/'+event_id+'/'+'/'+sd+'/'+ed+'/'+id);
        dhxWins.window(win_name).centerOnScreen();
      }

        //xxxxxxxxxxxxxx Tree xxxxxxxxxxxxxx

        tree_agendas2=new dhtmlXTreeObject("tree_agendas","","",0);
        tree_agendas2.setImagePath("{module_url}assets/jscript/dhtmlxSuite/dhtmlxTree/codebase/imgs/csh_bluebooks/");
        tree_agendas2.enableCheckBoxes(1);
        tree_agendas2.loadXML("{module_url}main/get_tree",xmlOk);
//        $.post("{module_url}main/get_tree",function(data){;
//                 console.log(data);
//        });
                    
        function xmlOk(){
            {tree_colors}        
            var lista=tree_agendas2.getAllChecked();
            var items = lista.split(",");
            $.post("{module_url}main/set_visibles/",{'items':items},function(data){
                 parent.refresh();
            });
        }

        function tree_change(id,estado){

            var list = tree_agendas2.getAllSubItems(id);
            var items = list.split(",");
            for(var i = 0; i< items.length;i++)
             tree_agendas2.setCheck(items[i],estado);

            var lista=tree_agendas2.getAllChecked();
            var items = lista.split(",");

            $.post("{module_url}main/set_visibles/",{'items':items},function(data){
                parent.refresh();
            });
            

        }
        tree_agendas2.setOnCheckHandler(tree_change);

        // Evito el error de XML
        function myErrorHandler(type, desc, erData){
                //Dummie
        }
        dhtmlxError.catchError("ALL",myErrorHandler);


        // xxxxxxxxxxx Date Picker    

        var dhxLayout = new dhtmlXLayoutObject(document.body, "2U");
        dhxLayout.cells("a").attachObject("menu");
        dhxLayout.cells("a").setWidth("200");
        dhxLayout.cells("a").setText("");
        dhxLayout.cells("b").attachObject("scheduler_here");
        dhxLayout.cells("b").setText("{username}");
        scheduler.update_view();
        dhxLayout.attachEvent("onCollapse", function(){
         scheduler.update_view();
        });
         dhxLayout.attachEvent("onPanelResizeFinish", function(){
         scheduler.update_view();
        });
         dhxLayout.attachEvent("onExpand", function(){
         scheduler.update_view();//
        });
        
        
//Modernizr.load({
//  test: true,
//  yep : '{base_url}jscript/agenda/script.js'
//});


    });// JQuery Onload

/* xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
 *              FUNCIONES
 * xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx */        


//        function print_page(){
//        window.open('{module_url}printpage/','preview','');
//        }

        function refresh(){
         var min_date=scheduler.getState().min_date;
         var max_date=scheduler.getState().max_date;
         var convert = scheduler.date.date_to_str("%Y-%m-%d");
         min_date=convert( min_date );  
         max_date=convert( max_date ); 
        $.get("{module_url}main/get_events/"+min_date+"/"+max_date,{}, function(data){
          scheduler.clearAll();
          scheduler.parse(data,"json");
        });

        var timer1=setTimeout("refresh()",300000);
        //$.get("{base_url}agenda/main/get_hora_actual",{}, function(data){$(".dhx_scale_hour:contains('"+data+"')").addClass("hora-actual");});
        }

        function setRowHeight(px){
         parent.scheduler.config.hour_size_px=px;
        }
                
        function msg(msg){     
            $('#msg2').text(msg);
            $('#msg2').slideDown(400).delay(2000).fadeOut(800);
        }
          
        function pop(title,msg){
        $( "#pop1" ).text(msg);
        $( "#pop1" ).dialog();
        $( "#pop1" ).dialog( "option", "title", title );
        }
        
        function open_opciones(){
        var win = dhxWins.createWindow('opciones', 1, 1, 600, 400); 
        var tb = dhxWins.window('opciones').attachTabbar();
        tb.setImagePath("{module_url}assets/jscript/dhtmlxSuite/dhtmlxTabbar/codebase/imgs/");
        tb.setHrefMode("ajax-html");
        tb.addTab("colores","Colores","100px");
        tb.setTabActive("colores");
        tb.enableAutoReSize(true);
        tb.setContentHref("colores","{module_url}main/get_opciones/");
         dhxWins.window('opciones').centerOnScreen();
        }
        
        function open_listado(){
            var win = dhxWins.createWindow('listado', 1, 1, 600, 400); 
            dhxWins.window('listado').attachURL('{module_url}listado/');
            dhxWins.window('listado').centerOnScreen();
        }
        
        // LIGHTBOX 
        
        function lightbox_close(id){
            scheduler.endLightbox(id);
            var win_name='lb'+id;
            dhxWins.window(win_name).close()
        }

        

        </script>
        

</body>
</html>