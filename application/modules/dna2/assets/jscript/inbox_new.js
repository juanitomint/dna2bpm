/**
 * dna2/inbox JS
 * 
**/
$(document).ready(function(){


  // ===== AJAX SELECT BOX
  
  $('.select2').select2({
        placeholder: "To..",
        dataType: 'json',
        multiple:true,
        ajax: {
        type:"POST",     
        url:globals['module_url']+"inbox/get_users",
        data: function (term) {
            return {
                term: term          
             };
        },
        results: function (result) {
            return result;
        }
        }

    });  
    
    // ===== REPLY
    
    if($('[name="reply"]').val()==1){
        var reply=$('[name="reply"]').val();
        var reply_name=$('[name="reply_name"]').val();
        var reply_title=$('[name="reply_title"]').val();
        var reply_idu=$('[name="reply_idu"]').val();
        var reply_body=$('[name="reply_body"]').val();
        var reply_date=$('[name="reply_date"]').val();
        $(".select2").select2("data", [{id:reply_idu,text:reply_name}]);
        $("[name='subject']").val("Reply:[ "+reply_title+" ]");
        $("[name='body']").val(">>>>>>>>>>>>> "+reply_date+" >>>>>>>>>>>>>\n"+reply_body);
     };
    

    
// New Message Ajax Submit
$("#inbox_new").live( "submit", function( event ) {
event.preventDefault();
var data=$(this).serializeArray();

$.post(globals.module_url+'inbox/send',{'data':data},function(resp){
    
    $('#inbox_new').prepend('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+resp+' mensaje/s enviado/s</div>');
    clear_form_elements();
});
     
});




      
});
//

function clear_form_elements() {

	    $('form').find(':input').each(function() {
	        switch(this.type) {
	            case 'password':
	            case 'select-multiple':
	            case 'select-one':
                    case 'textarea':
	            case 'text':
	                $(this).val('');
	                break;
	            case 'checkbox':
	            case 'radio':
	                this.checked = false;
	        }
	    });	 
}