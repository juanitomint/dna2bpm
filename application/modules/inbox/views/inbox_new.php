<!-- INBOX WIDGET -->
       {if {reply}}
         <input type="hidden" name="reply" value="1" />
         <input type="hidden" name="reply_name" value="{reply_name}" />
         <input type="hidden" name="reply_title" value="{reply_title}" />
         <input type="hidden" name="reply_body" value="{reply_body}" />
         <input type="hidden" name="reply_idu" value="{reply_idu}" />
          <input type="hidden" name="reply_date" value="{reply_date}" />
        {/if}


<form class="form-horizontal">
<!--  To -->

  <div class="form-group">
    <label class="col-sm-2 control-label">To:</label>
    <div class="col-sm-10">
          <select name="to" class="select2" multiple="multiple" style="width:400px;">

          </select>
    </div>
  </div>
<!--  Title -->
  <div class="form-group">
    <label class="col-sm-2 control-label">Subject:</label>
    <div class="col-sm-10">
    <input type="text" class="form-control"  placeholder="Subject">
     </div>
  </div>
<!--  MSG -->
  <div class="form-group">
    <label class="col-sm-2 control-label">Body:</label>
    <div class="col-sm-10">
     <textarea rows="5" name="body" placeholder="Body" class="form-control"></textarea>
     </div>
  </div>
 <!--  SEND -->
  <div class="form-group">
    <label class="col-sm-10 control-label"></label>
    <div class="col-sm-2">
      <button type="submit" class="btn btn-primary ">Send</button>
     </div>
  </div> 

  

  
</form>



