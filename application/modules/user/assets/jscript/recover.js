// Recover Passw JS

$(document).ready(function(){
    
    console.log('----- recover ');
    

    
$( "#recover1" ).submit(function( event ) {
  event.preventDefault();
  var myform=$(this);
  var mail=$(this).find('[name="mail"]').val();
  var url=globals['module_url']+'recover/send';

     $.post(url,{mail:mail},function(resp){
         console.log(resp);
         if(resp.status==true){
             var msg ="<p class='text-info '><i class='fa fa-thumbs-o-up' aria-hidden='true'></i> "+resp.msg+"</p>";
         }else{
             var msg ="<p class='text-danger '><i class='fa fa-thumbs-o-down' aria-hidden='true'></i> "+resp.msg+"</p>";
         }
         
         myform.find('.footer').html(msg);
     },'json');

});


$( "form#formAuth" ).submit(function( event ) {
  event.preventDefault();


var myform=$(this);
var url=globals['module_url']+'recover/save_new_pass';
  
var passw=$('#password').val();
var passw2=$('#password2').val();
var token=$('#token').val();

if(passw!=passw2){
  $('#dummy').html('<h4 ><span class="label label-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Las contraseñas no coinciden</span></h4> ');
  $('#dummy').slideDown(400).delay( 1500 ).fadeOut( 400 );  
}else{

        $.post(url,{passw:passw,token:token},function(resp){
 
         if(resp.status==true){
            myform.find('button').hide();
           $('#dummy').html('<p><a class="btn btn-info btn-block" href="'+globals['base_url']+'"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> '+resp.msg+'</a></p>');
           $('#dummy').slideDown(400); 

  
         }else{
           $('#dummy').html('<h4 ><span class="label label-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> '+resp.msg+'</span></h4> ');
           $('#dummy').slideDown(400).delay( 1500 ).fadeOut( 400 );  
         }
         
     },'json');
    
}


            
            
});



});
