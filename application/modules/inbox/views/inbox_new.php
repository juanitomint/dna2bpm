<!-- INBOX WIDGET -->
       {if {reply}}
         <input type="hidden" name="reply" value="1" />
         <input type="hidden" name="reply_name" value="{reply_name}" />
         <input type="hidden" name="reply_title" value="{reply_title}" />
         <input type="hidden" name="reply_body" value="{reply_body}" />
         <input type="hidden" name="reply_idu" value="{reply_idu}" />
          <input type="hidden" name="reply_date" value="{reply_date}" />
        {/if}


<form class="form-horizontal" id="new_msg">
<!--  To -->
      
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang to}:</label>
    <div class="col-sm-10">
		    <input type="hidden" name="to" class="select2 form-control"   multiple="multiple" />
  </div>
  </div>
<!--  Title -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang subject}:</label>
    <div class="col-sm-10">
    <input type="text" name="subject" class="form-control"  placeholder="Subject">
     </div>
  </div>
<!--  MSG -->
  <div class="form-group">
    <label class="col-sm-2 control-label">{lang body}:</label>
    <div class="col-sm-10">
     <textarea rows="5" name="body" placeholder="Body" class="form-control">{user signature}</textarea>
     </div>
  </div>
 <!--  SEND -->
  <div class="form-group">
    <div class="col-sm-12">
      <button type="submit" class="btn btn-primary pull-right">Send</button>
     </div>
  </div> 

  

  
</form>

<script>
 $(document).ready(function(){

	  // ===== AJAX SELECT BOX
	  //$('.select2').select2();
	  $('.select2').select2({
	        placeholder: "To..",
 	        dataType: 'json',
	       	multiple:true,
	        ajax: {
	        type:"POST",     
	        url:	globals.base_url+"inbox/inbox/get_users",
	        data: function (term) {
	            return {
	                term: term          
	             };
	        },
	        results: function (result) {
		        //console.log(result);
	            return result;
	        }
	        }

	    });



		
 });

</script>

