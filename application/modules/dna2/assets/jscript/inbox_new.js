/**
 * dna2/inbox JS
 * 
**/
$(document).ready(function(){


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