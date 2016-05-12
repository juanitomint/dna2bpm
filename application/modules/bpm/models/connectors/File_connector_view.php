<div style="padding-top: 30px;" class="data-header">    
<h4>
    <!--<i class="fa fa-caret-right"></i> -->
    <i class="fa fa-file"></i> 
    {properties name}
</h4>
    
</div>
<p>{properties documentation}</p>
<!-- Files Block -->
<!--<h5>{lang drop_here}</h5>-->
<div class="file-input" resourceId="{data_resourceId}">
{if {input_output} == 'Input' }
  <div style=" height: 115px; border-width: 2px; margin-bottom: 20px; color: #aaa; border-style: dashed;
      border-color: #ccc; line-height: 15px; text-align: center" class="dropfile {dropClass}" id="drop-zone">
    <span>
        <br/>
        <br/>
        <i class="fa fa-2x fa-cloud-upload"></i>
        <br/>
        {lang drop_here}
    </span>
  </div>
{/if}    
       <div class="list-group">
{files}
           <div class="alert alert-success alert-dismissible" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <a style="border-radius: 0px;" href="{base_url}{relative_path_encoded}/{name_encoded}" target="_blank">{name}</a>
            </div>      
{/files}
        </div>
</div>    
   
<!-- END Files Block -->
