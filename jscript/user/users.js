$(document).ready(function(){
    initButtons();
    //--init Layot
    var wsize=300;
    var esize=500;
    if(nolayout=='0'){
        $('body').layout({
            defaults: {
                applyDefaultStyles: true

            },
            west: {
                minSize: wsize,
                maxSize: 2*wsize,
                size: wsize,
                resizable: true,
                closable:true,
                slidable:true
            },
            east: {
                minSize: esize,
                maxSize: 2*esize,
                size: esize,
                resizable: true,
                closable:true,
                slidable:true
            }


        });
    }//---END layout
    //---BTN SEARCH
    $('.btn_search').live('click',function(){
        order=$("#radio input[name=order]:checked").val();
        active=null;
        query=$('#query').val();
        loadUsers(idgroup,order,active,query);


    });
    //---BTN Back to Group
    $('#btn_back_group').live('click',function(){
        window.close();
    });
    //---BTN new
    $('#btn_new_user').live('click',function(){
        loadProperties('new',idgroup);
    });

    //---BTN Page
    $('.btn_page').live('click',function(){
        order=$("#radio input[name=order]:checked").val();
        active=null;
        query=$('#query').val();
        page=$(this).val();
        loadUsers(idgroup,order,active,query,page);

    });


    //---BTN DeleteDB
    $('.delete_user_db').live('click',function(){
        iduser=$(this).val();
        url=base_url+'user/admin/delete_user_db/'+iduser;
        mydialog=$('<div title="Confirm"><h1>You are about to delete user:'+iduser+' from DB</div>').dialog({
            modal:true,
            dialogClass:'dot7',
            buttons:{
                'No, please don\'t!':function(){
                    $(this).dialog('close');
                },
                'Yes, do it':function(){
                    $(this).dialog('close');
                    $('#center').html('<img src="'+base_url+'css/ajax/loader18.gif"/>');
                    $.ajax({

                        url : url,
                        async:false,
                        type: 'GET',
                        //dataType:'json',
                        success:function(data){
                            $(mydialog).dialog('close');
                            loadUsers(idgroup);
                        }
                    });
                }
            }
        })

    });
    //----BTN SAVE
    $('#pForm .btn_save').live('click',function(){

        iduser=$(this).parents('form').find('input[name=idu]').val();

        //console.log('Fetching 4 type'+otype);
        url=base_url+'user/admin/save_user/'+iduser;
        obj=$('#pForm form').serializeObject();
        postData=$.param({
            'obj':obj
        });

        $.ajax({
            url : url,
            async:false,
            type: 'POST',
            data:postData,
            dataType:'json',
            success:function(data){
                //console.log(data);
                $('#eMSG').append(data.result);
                //---- set new values if is new
                if(data.isnew){
                    $('#pForm input[name=idu]').val(data.iduser);
                    $('#pForm input[name=cname-view]').val(data.iduser);
                    
                    order=$('#obj_sorter button.ui-state-active').html();
                    active=data.iduser;
                    loadUsers(idgroup,order,active);

                }
            }
        });

    });



    loadUsers(idgroup);



});//---END document Ready


function loadUsers(idgroup,order,active,query,page){
    if(order==null) order='name';
    if(page==null)page=1;
    
    url=base_url+'user/admin/get_users/'+idgroup+'/'+order+'/'+page;
    //---load dialog
    $( "#dialog-message" ).dialog({
        modal: true,
        title: 'LOADING'
    });
    postData= (query!=null) ? {
        'query':query
    }: {};
    //----START AJAX load
    $.ajax({
        'url' : url,
        'async':false,
        'type':'POST',
        'data':postData,
        'success':function(data){
            $('#center').html(data);
            initButtons();
            $('#obj_sorter button.btn_'+order).addClass('ui-state-active');
            $("#accordion").accordion({
                change: function(event, ui) {
                    id=ui.newContent.find('input[name=id]').val();
                    idgroup=ui.newContent.find('input[name=idgroup]').val();
                    //console.log(idgroup);
                    //----prevent double load
                    if($('#pForm form input[name=id]').val()!=id){
                        url=base_url+'user/admin/get_properties/'+id+'/'+idgroup;
                        $('#pForm').html('<img src="'+base_url+'css/ajax/loader18.gif"/>Loading properties for:'+id);
                        loadProperties(id,idgroup);
                    }
                    
                },
                animated: false

            });
            if(active!=null){
                $("#accordion").accordion('activate',$('#'+active));
            } else{
                //---load first object properties
                id=$('#accordion .desc:first input[name=id]').val();
                loadProperties(id);
            }
            //---destroy dialog
            $("#dialog-message").dialog( "destroy" );
            //--add order functionality
            $('#radio').buttonset();
            $('#pages').buttonset();
            $('#radio .btn_order').click(function(){
                order=$(this).attr('value');
                query=$('#query').val();
                loadUsers(idgroup,order,null,query);
            });
        }//---END succes
    }); //---end AJAX

  


}//--END Function loadUsers
function loadProperties(id,idgroup){
    url=base_url+'user/admin/get_user_properties/'+id+'/'+idgroup;
    $('#pForm').html('<img src="'+base_url+'css/ajax/loader18.gif"/>Loading properties for:'+id).load(url,'',
        function(){
            $('#eMSG').html('');
            initButtons();
            //---init date picker
            $('.datePicker').datepicker({
                "dateFormat":dateFmt,
                'altField':'#birthDate',
                'altFormat': 'yyyy-mm-dd',
                changeMonth: true,
                changeYear: true,
                minDate: '-500M'

            });
            //---set locale 4 DP
            $( "#datepicker" ).datepicker( $.datepicker.regional[ "es" ] );
            //---init gender option
            $('#gender').buttonset();
            //----4 Other Groups
            $('#otherGroups').buttonset();
            $('#btn_otherGroups').click(function(){
                $( "#otherGroups" ).dialog({
                    modal: true,
                    resizable: true,
                    height: 400,
                    width: 400,
                    open:function(){
                        var max=0;
                        $('#otherGroups span').each(
                            function(){
                                if($(this).width()> max) max=$(this).width();
                            });
                        $('#otherGroups span').width(max);
                        $('#otherGroups span').css('text-align','left');
                    },
                    close:function(){
                        var o=new Array();
                        $('#otherGroups input:checked').each(
                            function(){
                                o.push($(this).val());
                            }
                            );
                        
                        $('#group').html(o.join(','));
                    }
                    
                });
            });
            //----4 permissions
            $('#btn_editPerm').click(function(){
                idu=$(this).val();
                perm=$(this).parents('form').find('[name=perm]').val();
                postData={
                    'perm':perm
                };
                $("#permDialog" ).load(base_url+'user/admin/get_apps/'+idu,postData,function(){
                    $("#permDialog" ).dialog({
                        modal: true,
                        resizable: true,
                        height: 400,
                        width: 600,
                        open:function(){
                        //  $('#accordionPerm').accordion();
                    
                        },
                        close:function(){
                            var o=new Array();
                            $('#permDialog input:checked').each(
                                function(){
                                    o.push($(this).val());
                                }
                                );

                            $('#perm').val(o.join(','));
                        }

                    });//---end Dialog
                });//---end Load
            });//---end click

            //----4 TEST
            $('#test').click(function(){
                window.location=base_url+'user/authenticate/byhash/'+$(this).attr('nick')+'/'+$(this).attr('passw');
            });
        });
}

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
