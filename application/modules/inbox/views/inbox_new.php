<!-- INBOX WIDGET -->
       {if {reply}}
         <input type="hidden" name="reply" value="1" />
         <input type="hidden" name="reply_name" value="{reply_name}" />
         <input type="hidden" name="reply_title" value="{reply_title}" />
         <input type="hidden" name="reply_body" value="{reply_body}" />
         <input type="hidden" name="reply_idu" value="{reply_idu}" />
          <input type="hidden" name="reply_date" value="{reply_date}" />
        {/if}


<form  id="new_msg">
<!--  To -->
      
  <div class="form-group">
            <input type="hidden" name="{lang to}" class="select2 form-control"   multiple="multiple" />
  </div>
<!--  Title -->
  <div class="form-group">
    <input type="text" name="subject" class="form-control"  placeholder="Subject">
  </div>
<!--  MSG -->
  <div class="form-group">
     <textarea rows="5" name="body"  id="editor1" >{user signature}</textarea>
  </div>
 <!--  SEND -->

  <div class="form-group" style="margin-bottom: 40px">
      <button type="submit" class="btn btn-primary pull-right">{lang send}</button>
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

// ===== Ckeditor


CKEDITOR.replace( 'editor1' );

		
 });

</script>

