/**
 * dna2/inbox JS
 * 
**/
$(document).ready(function(){

// Open msg area
    $('UL.msgs .subject').live('click',function(event){
    $(this).next().slideToggle();
    event.preventDefault();
    var msgid=$(this).parent().attr('id');
    $.post(globals.module_url+'inbox/set_read',{'state':1,'msgid':msgid},function(data){})
        $(this).addClass('muted');
    });
    
// add star
$('.icon-star-empty').live('click',function(){
    $(this).removeClass('icon-star-empty');
    $(this).addClass('icon-star');
    var msgid=$(this).parent().attr('id');
    $.post(globals.module_url+'inbox/set_star',{'state':1,'msgid':msgid},function(data){});
});

// remove star
$('.icon-star').live('click',function(){
    $(this).addClass('icon-star-empty');
    $(this).removeClass('icon-star')
    var msgid=$(this).parent().attr('id');
    $.post(globals.module_url+'inbox/set_star',{'state':0,'msgid':msgid},function(data){});
});

// New Message Ajax Submit
$("form").on( "submit", function( event ) {
event.preventDefault();
var data=$(this).serializeArray();
$.post(globals.module_url+'inbox/send',{'data':data},function(resp){alert(resp)});

});




//$('#inbox_new #to').live('change',function(){
//    var to=$(this).val();
//    alert(to);
//});

      
});
