<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Create Event</h3>
                <div class="box-tools pull-right">
            <button data-original-title="Collapse" class="btn btn-default btn-sm" data-widget="collapse" data-toggle="tooltip" title=""><i class="fa fa-minus"></i></button>
            <button data-original-title="Remove" class="btn btn-default btn-sm" data-widget="remove" data-toggle="tooltip" title=""><i class="fa fa-times"></i></button>
        </div>
    </div>
    <div class="box-body">



        <!-- Titulo && color-->

         <div class="form-group " id="event-color-picker"> <label>Titulo</label>
            <div class="input-group">
             <input id="event-title" type="text" name="event-title" class="form-control" placeholder="Titulo">
              <div class="input-group-btn">
                <!-- Color -->
                <div class="btn-group " style="width: 100%; margin-bottom: 10px;">
                    <button type="button" class="btn btn-default  dropdown-toggle" data-toggle="dropdown"><i class="fa fa-square" style="color:{first_color}"></i> <span class="caret"></span></button>
                    <ul class="dropdown-menu" >
                       {ul}
                    </ul>
                </div>
            </div><!-- /input-group -->
        </div>
        <input type='hidden' value='{first_color}' name='event-color' id='event-color'/>
        </div>


        <!-- Mensaje -->
        <div class="form-group">
            <label>Mensaje</label>
            <textarea class="form-control" name="event-body" id="event-body" rows="3" placeholder="Enter ..."></textarea>
        </div>
        

                                    
         <!-- Group events -->
         
         {if {user_can_create_group_events}}
            <div class="form-group">
                <label>Groups</label>
                <input id="event-groups" type="text" name="event-groups" class="form-control" placeholder="1,2,3,#">
            </div>     
         {/if}
        
        <!-- Dates -->
        <div class="form-group">
            <label>Intervalo:</label>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input class="form-control pull-right range" name="event-interval" id="event-interval" type="text" placeholder="Intervalo" value="{intervalo}">
            </div><!-- /.input group -->
        </div>
       <!-- allDay -->
        <div class="form-group">
            <label>Todo el día</label>
            <input type="checkbox" value="1" name="allday" id="allday"/>
        </div>     
        
        <!-- Send -->                    
        <div class="input-group-btn">
            <button id="add-new-event" type="button" class="btn btn-default btn-flat btn-block">Crear evento</button>
        </div>                        
    </div>
</div>