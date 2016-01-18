
                


        <!-- Titulo && color-->

         <div class="form-group " id="event-color-picker"> <label>{lang calendar_title}</label>
            <div class="input-group">
             <input id="event-title" type="text" name="event-title" class="form-control" placeholder="{lang calendar_title}">
              <div class="input-group-btn">
                <!-- Color -->
                <div class="btn-group " style="width: 100%; margin-bottom: 10px;">
                    <button type="button" class="btn btn-default  dropdown-toggle" data-toggle="dropdown">{first_color_anchor}</button>
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
            <label>{lang calendar_message}</label>
            <textarea class="form-control" name="event-body" id="event-body" rows="3" placeholder="Enter ..."></textarea>
        </div>
        
                                
         <!-- Group events -->
         
         {if {user_can_create_group_events}}

            <div class="form-group">
            <label>{lang calendar_groups}</label>
            <div class="input-group">
                <select class="form-control"  id="select_group">
                    <option value="all" selected="selected">All groups</option>
                    {groups}
                </select>
              <div class="input-group-btn">
                <button type="button" class="btn btn-default dropdown-toggle"  id="add_group" title="Add group" > <i class="fa fa-plus"></i></button>
              </div>

            </div>
            </div>
            <div class="form-group" id="groups_box">

            </div> 

         {/if}
        
        <!-- Dates -->
        <div class="form-group">
            <label>{lang calendar_interval}:</label>
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input class="form-control pull-right range" name="event-interval" id="event-interval" type="text" placeholder="{lang interval}" value="{intervalo}">
            </div><!-- /.input group -->
        </div>
       <!-- allDay -->
        <div class="form-group">
            <label>{lang calendar_all_day}</label>
            <input type="checkbox" value="1" name="allday" id="allday"/>
        </div>     
        
        <!-- Send -->                    
        <div class="input-group-btn">
            <button id="add-new-event" type="button" class="btn btn-default btn-flat btn-block">{lang calendar_create_event}</button>
        </div>                        
