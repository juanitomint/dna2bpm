/**
 * dna2/inbox JS
 * 
**/
$(document).ready(function(){
var whereiam=$('#whereiam').val();
var target=".dummy_msgs";

	         
// ajax handle
$(document).on('click','.ajax',function(e){
	e.preventDefault();// avoid msg open
	var url=$(this).attr('href');
	if((url)=="#")return;
	reload(url,target,whereiam);
    
});

// Refresh
$(document).on('click','#bt_refresh',function(e){
	var whereiam=$('#whereiam').val();
	var url = globals['base_url']+"inbox/print_folder/"+whereiam;
	reload(url,target,whereiam);
});


// add star
$(document).on('click','.fa-star',function(e){
    $(this).removeClass('fa-star');
    $(this).addClass('fa-star-o');
    var msgid=$(this).parents('tr').attr('data-msgid');
    $.post(globals.base_url+'inbox/inbox/set_star',{'state':'off','msgid':msgid},function(data){
    	update_counters();
    });

    e.stopPropagation();// avoid msg open
});

//remove star
$(document).on("click",".fa-star-o",function(e){	

    $(this).removeClass('fa-star-o');
    $(this).addClass('fa-star');
    var msgid=$(this).parents('tr').attr('data-msgid');
    $.post(globals.base_url+'inbox/inbox/set_star',{'state':'on','msgid':msgid},function(data){
    	update_counters();	
    });
    
    e.stopPropagation();// avoid msg open
});

// MSG Open
$(document).on("click",".msg",function(e){
	var msgid=$(this).attr('data-msgid');
	var this_msg=$(this);
	var whereiam=$('#whereiam').val();
    var url = globals.base_url+'inbox/inbox/get_msg';    
    $.post(url,{id:msgid,whereiam:whereiam},function(data){

    	 var msg=JSON.parse(data);
    	 var mybody=msg.body.replace(/(?:\r\n|\r|\n)/g, '<br />');

        $('.modal-title').html('<i class="fa fa-envelope"></i> '+msg.subject);
        $('.modal-body').html(mybody);
        $('#myModal').modal('show');     
        $('#printboard').html('<h1>'+msg.subject+'</h1>'+mybody);// printzone
       
        this_msg.removeClass('unread');
        this_msg.addClass('read');  
        update_counters();	
    });

	
    e.preventDefault();
});

// New MSG Submit
$(document).on('submit','#new_msg',function(e){
	e.preventDefault();
	console.log('sending');
        
        var config={'status':'warning','body':'Mensaje Enviado!!!'};
        myalert=BT_alert(config);

        $('#myModal').find('.modal-body').html(myalert);
	var data=$(this).serializeArray();
        //====== AJAX
        $.ajax({
        type: "POST",
        url: globals.base_url+"inbox/inbox/send",
        data: {data:data},
        success: function(resp){
        $('#myModal').find('.modal-body').html('Message sent!');
	},
        error: function(resp){
        $('#myModal').find('.modal-body').html('Message couldn\'t be sent!');
	}
        }).done(function( ) {
               	var whereiam=$('#whereiam').val();
                var url = globals['base_url']+"inbox/print_folder/"+whereiam;
                reload(url,target,whereiam);
                $('#myModal').modal('hide');
         });


       //<i class="fa fa-spinner fa-spin fa-2x" ></i>
  });

// Action dropdown handle
$(document).on("click","#msg_action a,#msg_tag a",function(){
	var action=$(this).attr('data-action');
	var msgid=[];
	var whereiam=$('#whereiam').val();
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
		    	update_counters();	
		    });
			break;
		case "unread":
		    var url = globals.base_url+'inbox/inbox/set_read';    
		    $.post(url,{state:'unread',msgid:msgid},function(data){
		    	msgid.forEach(function(id){
		    		$("[data-msgid='"+id+"']").removeClass('read');
		    		$("[data-msgid='"+id+"']").addClass('unread');
	    		});
		    	update_counters();	
		    });
			break;
		case "junk":
		    var url = globals.base_url+'inbox/inbox/move';    
		    $.post(url,{'msgid':msgid,'folder':'trash'},function(data){
		    	msgid.forEach(function(id){
		    		$("[data-msgid='"+id+"']").hide('500');
	    		});
		    	update_counters();	
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
		    	update_counters();	
		    });
			break;	
		case "delete":
		    var url = globals.base_url+'inbox/inbox/remove';    
		    $.post(url,{'msgid':msgid},function(data){
		    	
		    	msgid.forEach(function(id){
		    		$("[data-msgid='"+id+"']").hide('500');
	    		});
		    	update_counters();	
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

	var filter=$('#search').val();
	var folder=$('#whereiam').val();

	var url = globals.base_url+'inbox/inbox/print_folder/'+folder+'/';  
	$.post( url, {filter:filter},function( data ) {
		$(target).html(data);
	});

	
});


//== Turn off check all when flip pages

$(document).on('click','.pagination',function(){
	$("#check-all").iCheck("uncheck");
});

//=============== OLD


// delete && Move
//$("a[name='delete']").on('click',function(){
//    var msgid=$(this).attr('data-msgid');
//    if($('[name="whereim"]').val()=='Trash'){
//        // estoy en Trash, elimino mensaje
//          $('#'+msgid).append('<span class="pull-right" style="margin-right:5px"><i class="icon-spinner icon-spin icon-large"></i> Wait... </span> ');
//        $.post(globals.module_url+'inbox/remove',{'msgid':msgid},function(data){
//        $('#'+msgid).hide('500');
//        });
//    }else{
//        // Mando a Trash
//          $('#'+msgid).append('<span class="pull-right" style="margin-right:5px"><i class="icon-spinner icon-spin icon-large"></i> Wait... </span> ');
//        $.post(globals.module_url+'inbox/move',{'msgid':msgid,'folder':'trash'},function(data){
//        $('#'+msgid).hide('500');
//    });
//    }
//    // refresh count in lateral menu & top menu only in inbox
//    if($('[name="whereim"]').val()=='Inbox'){
//    var count=$('#inbox span.label').text();
//    $('#inbox span.label').text(count-1);
//    $('#menu-messages a span.label').text(count-1);
//    }
//
//});

// Recover
//$("a[name='recover']").on('click',function(){
//    var msgid=$(this).attr('data-msgid');
//        // Mando a inbox     
//        $('#'+msgid).append('<span class="pull-right" style="margin-right:5px"><i class="icon-spinner icon-spin icon-large"></i> Wait... </span> ');
//        $.post(globals.module_url+'inbox/move',{'msgid':msgid,'folder':'inbox'},function(data){
//        $('#'+msgid).hide('500');
//    });
//});

//


});//


//====== Reload : update the count in folders and the content of msgs
function reload(url,target,whereiam){
	update_counters();
	$.post( url, function( data ) {
		$(target).html(data);

		//==== icheck
	    $('input[type="checkbox"]').iCheck({
	        checkboxClass: 'icheckbox_minimal',
	        radioClass: 'iradio_minimal'
	    });
	    //When unchecking the checkbox
	    $(document).on('ifUnchecked', "#check-all", function(event) {
	        //Uncheck all checkboxes
	        $("input[type='checkbox']", ".table-mailbox").iCheck("uncheck");
	    });
	    //When checking the checkbox
	    $("#check-all").on('ifChecked', function(event) {
	        $("input[type='checkbox']", ".table-mailbox").iCheck("check");
	    });
	    //====
	    $('.nav-stacked li').removeClass('active');

	});
}

function update_counters(){
	// Keep counters 
	var letscount = globals['base_url']+"inbox/print_count_msgs/";
        
	$.post( letscount, function( data ) {
		var json=JSON.parse(data);
		for (var prop in json) {
			$('.'+prop).html(json[prop]);
                        console.log(prop+' '+json[prop]);
			}
	});
        
        // Toolbar inbox
        var toolbar = globals['base_url']+"inbox/print_toolbar/";
       	$.post( toolbar, function( data ) {
            $( "#toolbar_inbox" ).replaceWith( data);
            //console.log(data);
	});
        
}


