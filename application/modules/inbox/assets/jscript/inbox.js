/**
 * dna2/inbox JS
 * 
**/
$(document).ready(function(){
var whereiam=$('#whereiam').val();

// add star
$(document).on('click','.fa-star',function(e){

    $(this).removeClass('fa-star');
    $(this).addClass('fa-star-o');
    var msgid=$(this).parents('tr').attr('data-msgid');
    $.post(globals.base_url+'inbox/inbox/set_star',{'state':'off','msgid':msgid},function(data){});
    e.stopPropagation();// avoid msg open
});

//remove star
$(document).on("click",".fa-star-o",function(){	

    $(this).removeClass('fa-star-o');
    $(this).addClass('fa-star');
    var msgid=$(this).parents('tr').attr('data-msgid');
    $.post(globals.base_url+'inbox/inbox/set_star',{'state':'on','msgid':msgid},function(data){});
    e.stopPropagation();// avoid msg open
});

// MSG Open
$(document).on("click",".msg",function(e){
	var msgid=$(this).attr('data-msgid');
	var this_msg=$(this);

    var url = globals.base_url+'inbox/inbox/get_msg';    
    $.post(url,{id:msgid,whereiam:whereiam},function(data){
    	var msg=JSON.parse(data);

        $('#myModal').find('.modal-title').html('<i class="fa fa-envelope"></i> '+msg.subject);
        $('#myModal').find('.modal-body').html(msg.body.replace(/(?:\r\n|\r|\n)/g, '<br />'));
        $('#myModal').modal('show');

        this_msg.removeClass('unread');
        this_msg.addClass('read');   
    });
    event.preventDefault();
});

// Action dropdown handle
$(document).on("click","#msg_action a,#msg_tag a",function(){
	var action=$(this).attr('data-action');
	var msgid=[];
	$('.msg').each(function(i,data){
		var checked=$(data).find('.icheckbox_minimal').hasClass('checked')
		if(checked){
			msgid.push($(data).attr('data-msgid'));
		}
	});

	// only if we have selection
	if(msgid.length){

		switch(action){
		case "read":
		    var url = globals.base_url+'inbox/inbox/set_read';    
		    $.post(url,{state:'read',msgid:msgid},function(data){
		    	msgid.forEach(function(id){
		    		$("[data-msgid='"+id+"']").removeClass('unread');
		    		$("[data-msgid='"+id+"']").addClass('read');
	    		});
		    });
			break;
		case "unread":
		    var url = globals.base_url+'inbox/inbox/set_read';    
		    $.post(url,{state:'unread',msgid:msgid},function(data){
		    	msgid.forEach(function(id){
		    		$("[data-msgid='"+id+"']").removeClass('read');
		    		$("[data-msgid='"+id+"']").addClass('unread');
	    		});
		    });
			break;
		case "junk":
		    var url = globals.base_url+'inbox/inbox/move';    
		    $.post(url,{'msgid':msgid,'folder':'trash'},function(data){
		    	msgid.forEach(function(id){
		    		$("[data-msgid='"+id+"']").hide('500');
	    		});
		    });
			break;	
		case "inbox":
		    var url = globals.base_url+'inbox/inbox/move';    
		    $.post(url,{'msgid':msgid,'folder':'inbox'},function(data){
		    	if(whereiam!='inbox'){
			    	msgid.forEach(function(id){
			    		$("[data-msgid='"+id+"']").hide('500');
		    		});
		    	}
		    });
			break;	
		case "delete":
		    var url = globals.base_url+'inbox/inbox/remove';    
		    $.post(url,{'msgid':msgid},function(data){
		    	msgid.forEach(function(id){
		    		$("[data-msgid='"+id+"']").hide('500');
	    		});
		    });
			break;
		case "tag":
			var tag=$(this).attr('data-priority');
			var url = globals.base_url+'inbox/inbox/set_tag';  

		    $.post(url,{'tag':tag,'msgid':msgid},function(data){

		    	msgid.forEach(function(id){
		    		$("[data-msgid='"+id+"']").removeClass('tag_extreme tag_high tag_normal tag_low tag_notag');
		    		$("[data-msgid='"+id+"']").addClass('tag_'+tag);
	    		});
		    });
		break;
	}
		
	}
	//console.log(msgid.length);

    
});

$(document).on("submit","[name='form_search']",function(e){
	/* Search */
	e.preventDefault();
	var find=$('#search').val();
    window.location=window.location+'/filter/'+find; ;

	
});

//=============== OLD


// delete && Move
$("a[name='delete']").on('click',function(){
    var msgid=$(this).attr('data-msgid');
    if($('[name="whereim"]').val()=='Trash'){
        // estoy en Trash, elimino mensaje
          $('#'+msgid).append('<span class="pull-right" style="margin-right:5px"><i class="icon-spinner icon-spin icon-large"></i> Wait... </span> ');
        $.post(globals.module_url+'inbox/remove',{'msgid':msgid},function(data){
        $('#'+msgid).hide('500');
        });
    }else{
        // Mando a Trash
          $('#'+msgid).append('<span class="pull-right" style="margin-right:5px"><i class="icon-spinner icon-spin icon-large"></i> Wait... </span> ');
        $.post(globals.module_url+'inbox/move',{'msgid':msgid,'folder':'trash'},function(data){
        $('#'+msgid).hide('500');
    });
    }
    // refresh count in lateral menu & top menu only in inbox
    if($('[name="whereim"]').val()=='Inbox'){
    var count=$('#inbox span.label').text();
    $('#inbox span.label').text(count-1);
    $('#menu-messages a span.label').text(count-1);
    }

});

// Recover
$("a[name='recover']").on('click',function(){
    var msgid=$(this).attr('data-msgid');
        // Mando a inbox     
        $('#'+msgid).append('<span class="pull-right" style="margin-right:5px"><i class="icon-spinner icon-spin icon-large"></i> Wait... </span> ');
        $.post(globals.module_url+'inbox/move',{'msgid':msgid,'folder':'inbox'},function(data){
        $('#'+msgid).hide('500');
    });
});

//





      

      
});//
