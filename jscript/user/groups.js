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
    loadGroups();

    //----BTN SAVE
    $('#pForm .btn_save').live('click',function(){

        idgroup=$(this).parents('form').find('input[name=idgroup]').val();

        //console.log('Fetching 4 type'+otype);
        url=base_url+'user/admin/save_group/'+idgroup;
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
                    $('#pForm input[name=idgroup]').val(data.idgroup);
                    $('#pForm input[name=cname-view]').val(data.idgroup);
                    order=$('#obj_sorter button.ui-state-active').html();
                    active=data.idgroup;
                    loadGroups(order,active);

                }
            }
        });

    });
    //---BTN SEARCH
    $('.btn_search').live('click',function(){
        order=$("#radio input[name=order]:checked").val();
        active=null;
        query=$('#query').val();
        loadGroups(order,active,query);
     

    });

    //---BTN NEW
    $('.btn_new').live('click',function(){
        loadProperties('new');     
    });

    //---BTN OPEN
    $('#pForm .btn_open').live('click',function(){

        idgroup=$(this).parents('form').find('input[name=idgroup]').val();
        //console.log('Fetching 4 type'+otype);
        url=base_url+'user/admin/users/'+idgroup+'/name';
        window.open(url);

    });

    //---BTN DeleteDB
    $('.delete_group_db').live('click',function(){
        idgroup=$(this).val();
        url=base_url+'user/admin/delete_group_db/'+idgroup;
        mydialog=$('<div title="Confirm"><h1>You are about to delete group:'+idgroup+' from DB</div>').dialog({
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
                            loadGroups();
                        }
                    });
                }
                
            }
        })

    });
//---END document.ready
});

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
function loadGroups(order,active,query){
    if(order==null) order='';
    url=base_url+'user/admin/get_groups/'+order;
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
                    //console.log(idgroup);
                    //----prevent double load
                    if($('#pForm form input[name=idgroup]').val()!=id){
                        url=base_url+'user/admin/get_properties/'+id;
                        $('#pForm').html('<img src="'+base_url+'css/ajax/loader18.gif"/>Loading properties for:'+id);
                        loadProperties(id);
                    }
                    
                },
                animated: false

            });
            if(active!=null){
                $("#accordion").accordion('activate',$('#'+active));
            } else{
                //---load first object properties
                idgroup=$('#accordion .desc:first input[name=id]').val();
                loadProperties(idgroup);
            }
            //---destroy dialog
            $("#dialog-message").dialog( "destroy" );
            //--add order functionality
            $('#radio').buttonset();
            $('#radio .btn_order').click(function(){
                order=$(this).attr('value');
                query=$('#query').val();
                loadGroups(order,null,query);
            });
        }//---END succes
    }); //---end AJAX



}//--END DOCUMENT READY
function loadProperties(idgroup){
    url=base_url+'user/admin/get_group_properties/'+idgroup;
    $('#pForm').html('<img src="'+base_url+'css/ajax/loader18.gif"/>Loading properties for:'+idgroup).load(url,'',function(){
        $('#eMSG').html('');
        //----4 permissions
             $('#btn_editPerm').click(function(){
                 idu=$(this).val();
                 perm=$(this).parents('form').find('[name=perm]').val();
                 postData={'perm':perm};
              $("#permDialog" ).load(base_url+'user/admin/get_apps/',postData,function(){
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
        initButtons();
    });
}
