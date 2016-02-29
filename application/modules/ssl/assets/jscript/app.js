/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor. ---
 */

jQuery(document).ready(function($) {
   
console.log('----- SSL');

    //=== submit
   
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
            console.log(resp.status);
                if(resp.status){
                    $('#fingerprint p').html(resp.fingerprint);
                    $('#fingerprint').show();
                    $('.ajax_list_my_keys').html(resp.list);
                }
        });
    });
    
    
    $(document).on('click','[data-cmd="delete"]',function(e){
        var url=globals['base_url']+"ssl/delete_my_key";
        var mytarget=$(this).parentsUntil('.box-primary');
        var fingerprint=$(this).attr('data-fingerprint');
        $.post(url,{fingerprint:fingerprint},function(resp){
           mytarget.html(resp.list);
           console.log(resp);
        },'json');
        


    });
    
    

//==  ready
});

