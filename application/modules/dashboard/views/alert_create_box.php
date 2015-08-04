<form id="alertform">
<!-- Titulo && color-->
 <div class="form-group " id="event-color-picker"> <label>{lang alert_title}</label>
    <div class="input-group">
      <div class="input-group-btn">
        <!-- Color -->
        <div class="btn-group " style="width: 100%; margin-bottom: 10px;">
            <button type="button" class="btn btn-default  dropdown-toggle" data-toggle="dropdown"><i class="fa fa-square text-info" id="alert-caret"></i> <span class="caret"></span></button>
            <ul class="dropdown-menu " >
                <li><a  href="#" data-class='info' ><i class="fa fa-square text-info"></i> Info</a></li>
                <li><a  href="#" data-class='success' ><i class="fa fa-square text-success"></i> Success</a></li>
                <li><a  href="#" data-class='warning'><i class="fa fa-square text-warning"></i> Warning</a></li>
                <li><a  href="#" data-class='danger' ><i class="fa fa-square text-danger"></i> Danger</a></li>
            </ul>
        </div>
    </div><!-- /input-group -->
     <input id="title" type="text" name="subject" class="form-control" placeholder="{lang alert_title}">
</div>
<input type='hidden' value='info' name='class' id='event-class'/>
</div>

<!-- Body -->
<div class="form-group">
    <label>{lang alert_body}</label>
    <textarea class="form-control" name="body" id="alert-body" rows="3" placeholder="{lang alert_body}"></textarea>
</div>

<!-- Groups && panels -->
<div class="form-group">
    <label>{lang alert_groups}</label>
    <input id="event-groups" type="text" name="target" class="form-control" placeholder="{lang alert_groups}">
</div>



<!-- Send -->                    
<div class="input-group-btn">
    <button id="add-new-alert" type="submit" class="btn btn-default btn-flat btn-block">{lang alert_create_event}</button>
</div>       
</form>
