


<!-- Titulo && color-->
 <div class="form-group" id="modal-color-picker">
    <div class="input-group">
     <input id="modal-title" type="text" name="modal-title" class="form-control" placeholder="{lang calendar_title}" value="{title}">
      <div class="input-group-btn">
        <!-- Color -->
        <div class="btn-group " style="width: 100%; margin-bottom: 10px;">
            <button type="button"  class="btn btn-default color-picker dropdown-toggle" data-toggle="dropdown" >{first_color_anchor} <span class="caret"></span></button>
            <ul class="dropdown-menu" id="color-chooser">
               {ul}
            </ul>
        </div>
    </div><!-- /input-group -->
</div>
<input type='hidden' value='{color}' name='modal-color' id='modal-color' />
</div>

        
<!-- MSG -->
<div class="form-group">
<textarea class="form-control" style="min-height:120px" id="modal_detail">{body}</textarea>
</div>
  


 <!-- Group events -->
 
 {if {user_can_create_group_events}}

    <div class="form-group">
    <label>{lang calendar_groups}</label>
    <div class="input-group">
        <select class="form-control"  id="modal-select_group">
            <option value="all" selected="selected">All groups</option>
            {groups}
        </select>
      <div class="input-group-btn">
        <button type="button" class="btn btn-default dropdown-toggle"  id="modal-add_group" title="Add group" > <i class="fa fa-plus"></i></button>
      </div>

    </div>
    </div>
    <div class="form-group" id="modal-groups_box">{group_box}</div> 

 {/if}

<!-- Fecha -->
<div class="form-group">
        <!-- Dates -->
        <div class="form-group">
            <div class="input-group">
                <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                </div>
                <input class="form-control pull-right modal_range" name="event-interval" id="modal-event-interval" type="text" placeholder="{lang calendar_interval}" value="{intervalo}">
            </div><!-- /.input group -->
        </div>
</div>

<!-- allDay -->
<div class="form-group">
    <label>{lang calendar_all_day}</label>
    <input type="checkbox" value="1" {allDay}  id="modal_allDay"/>
</div> 


         
<div class="form-group">
 {if {user_can_update}}
 <button type="button" class="btn btn-primary" id="modal_save" data-id="{_id}"><i class="fa fa-floppy-o"></i> {lang calendar_save} </button>
 {/if}
 {if {user_can_delete}}
 <button type="button" class="btn btn-danger " id="modal_delete" data-id="{_id}"><i class="fa fa-trash-o"></i> {lang calendar_delete}</button>
 {/if}
</div>
<span class="text-info"><i class="fa fa-pencil-square-o"></i> {author}</span>