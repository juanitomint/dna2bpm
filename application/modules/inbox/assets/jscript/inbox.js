/**
 * dna2/inbox JS
 * 
**/
$(document).ready(function(){


// add star
$(document).on('click','.fa-star',function(){
    $(this).removeClass('fa-star');
    $(this).addClass('fa-star-o');
    var msgid=$(this).parents('tr').attr('data-msgid');
    $.post(globals.base_url+'inbox/inbox/set_star',{'state':'off','msgid':msgid},function(data){});
});

//remove star
$(document).on("click",".fa-star-o",function(){
    $(this).removeClass('fa-star-o');
    $(this).addClass('fa-star');
    var msgid=$(this).parents('tr').attr('data-msgid');
    $.post(globals.base_url+'inbox/inbox/set_star',{'state':'on','msgid':msgid},function(data){});
});

// MSG Open
$(document).on("click",".msg",function(){
	var msgid=$(this).attr('data-msgid');
	var this_msg=$(this);
    event.preventDefault();
//    var checked=$('.icheckbox_minimal',this).hasClass('checked'));
    var url = globals.base_url+'inbox/inbox/get_msg';    
    $.post(url,{id:msgid},function(data){
    	var msg=JSON.parse(data);
        $('#myModal').find('.modal-title').html(msg.subject);
        $('#myModal').find('.modal-body').html(msg.body);
        $('#myModal').modal('show');
        this_msg.removeClass('unread');
        this_msg.addClass('read');   
    });
});

// Action dropdown handle
$(document).on("click","#msg_action a",function(){
	var action=$(this).attr('data-action');
	var msgid=[];
	$('.msg').each(function(i,data){
		var checked=$(data).find('.icheckbox_minimal').hasClass('checked')
		if(checked){
			msgid.push($(data).attr('data-msgid'));

		}
	});
	
	// only if we have selection
	if(!msgid.length){
		
		
	}
	console.log(msgid.length);
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
      

      
});//
