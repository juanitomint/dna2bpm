/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. ---
 */

jQuery(document).ready(function($) {
   
console.log('----- SSL');


   
   //== New key
    $(document).on('submit','#form_add_key',function(e){
        e.preventDefault();
        var url=globals['base_url']+"ssl/add_key";
        var data=$(this).serializeArray();

        $.ajax({
          url: url,
          method:'POST',
          data:data,
          dataType:'json',
        }).done(function(resp) {
            console.log(resp);
            var msg='';
                if(resp.status){
                    msg +='<div class="callout callout-info" id="fingerprint">';
                    msg += '<h4>Fingerprint generated</h4>';
                    msg +='<p>'+resp.fingerprint+'</p></div>';
                    $('.ajax_list_my_keys').html(resp.list);
                }else{
                    msg+='<div class="callout callout-danger" id="fingerprint">';
                    msg+='<h4>'+resp.error+'</h4></div>';
                }
                
                $('#dummy1').html(msg);
                $('#dummy1 #fingerprint').delay(2000).fadeOut();
        });
    });
    
    //== Delete key
    $(document).on('click','[data-cmd="delete"]',function(e){
        var url=globals['base_url']+"ssl/delete_my_key";
        var mytarget=$(this).parentsUntil('.box-primary');
        var fingerprint=$(this).attr('data-fingerprint');
        $.post(url,{fingerprint:fingerprint},function(resp){
           mytarget.html(resp.list);
           console.log(resp);
        },'json');
    });
    
    
    //== Encrypt MSG
    $(document).on('submit','#form_encrypt',function(e){
        e.preventDefault();
        var url=globals['base_url']+"ssl/wrapper_encrypt";
        //var mytarget=$(this).parentsUntil('.box-primary');
       
 
         var fingerprint=$(this).find('[name="fingerprint"]').val()
         var plain_text=$(this).find('[name="plain_text"]').val();
         var encrypted_text=$(this).find('[name="encrypted_text"]');
        $.post(url,{fingerprint:fingerprint,plain_text:plain_text},function(resp){
          //mytarget.html(resp.list);
          console.log(resp);
          encrypted_text.val(resp);
        });
    });
    
    
    //== Verify MSG
    $(document).on('submit','#form_verify',function(e){
        e.preventDefault();
        var url=globals['base_url']+"ssl/wrapper_verify";
        var mytarget=$(this).parent();
       
       
         var fingerprint=$(this).find('[name="fingerprint"]').val()
         var plain_text=$(this).find('[name="plain_text"]').val();
         var signature=$(this).find('[name="signature"]').val();
        $.post(url,{fingerprint:fingerprint,plain_text:plain_text,signature:signature},function(resp){
          mytarget.after(resp);
         // console.log(resp);
         // encrypted_text.val(resp);
        });
    });
    

//==  ready
});

