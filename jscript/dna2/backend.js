

/// START dcument.ready ////
$(document).ready(function(){
//----start Layout
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
    }
    $("#menu-list li a").button();
    $("#menu-list li a").click(function() {
        //console.log($(this));
        //----deselect all
        $("#menu-list li a").removeClass('ui-state-active');
        $(this).addClass('ui-state-active');

        var action=$(this).attr('id');
        console.log('calling:'+action);
        switch(action){
            case 'menu_def':
                load_def();
                break;

            case 'menu_views':
                load_views();
                break;

            case 'menu_options':
                load_options();
                break;

            case 'menu_users':
                load_users();
                break;

            case 'menu_groups':
                load_groups();
                break;

        }
        
    });
    
    //--load views into central frame
    load_views('idobj');
    initEditors();

    $('#pForm form .reload').live('click',function(){
        otype='reload'
        idform=$(this).parents('form').find('input[name=idform]').val();
        idobj=$(this).parents('form').find('input[name=idobj]').val();

        //console.log('Fetching 4 type'+otype);
        url=base_url+'dna2/be/get_properties/'+idform+'/'+idobj+'/'+idapp;
        $(this).parents('form').html('<img src="'+base_url+'css/ajax/loader18.gif"/>Loading properties for:'+idform).load(url,'',initButtons);

    });
    $('#pForm form .edit').live('click',function(){
        otype='reload'
        idform=$(this).parents('form').find('input[name=idform]').val();
        idobj=$(this).parents('form').find('input[name=idobj]').val();

        //console.log('Fetching 4 type'+otype);
        url=base_url+'dna2/form/edit/'+idobj;
        window.open(url);

    });
    $('#pForm form select[name=ident]').live('change',function(){
        ident=$('#pForm form select[name=ident] option:selected').val();
        url=base_url+'dna2/be/get_container/'+ident;
        $.ajax({
            url : url,
            async:false,
            type: 'GET',
            dataType:'json',
            success:function(data){
                console.log(data);
                $('#container').val(data.container);

            }
        });
    });
    $('#pForm form .save').live('click',function(){
        otype='reload'
        idform=$(this).parents('form').find('input[name=idform]').val();
        idobj=$(this).parents('form').find('input[name=idobj]').val();

        //console.log('Fetching 4 type'+otype);
        url=base_url+'dna2/be/save/'+idobj;
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
                    $('#pForm input[name=idform]').val(data.idform);
                    $('#pForm input[name=idobj]').val(data.idobj);
                    $('#pForm input[name=cname-view]').val(data.idobj);
                    order=$('#obj_sorter button.ui-state-active').html();
                    active=data.idobj;
                    load_views(order,active);

                }
            }
        });

    });
    //----------------------------
    $('#btn_new').live('click',function(){
        
        url=base_url+'dna2/be/get_properties/new/new/'+idapp;
        $('#pForm').load(url,'',function(){
            $('#eMSG').html('');
            $("#pForm button").button();
        });
    });
    $('.removeapp').live('click',function(){

        url=base_url+'dna2/be/removefromapp/'+idobj+'/'+idapp;
        $.ajax({
            url : url,
            async:false,
            type: 'GET',
            //dataType:'json',
            success:function(data){
                load_views();
            }
        });
    });

    ///---REMOVE object from DB
    $('.removedb').live('click',function(){
        idobj=$(this).attr('idobj');
        url=base_url+'dna2/be/delete_object_db/'+idobj+'/'+idapp;
        $('<div title="Confirm"><h1>You are about to delete:'+idobj+' from DB</div>').dialog({
            modal:true,
            buttons:{
                'Yes, do it':function(){
                    $(this).dialog('close');
                    $('#center').html('<img src="'+base_url+'css/ajax/loader18.gif"/>');
                    $.ajax({
                    
                        url : url,
                        async:false,
                        type: 'GET',
                        //dataType:'json',
                        success:function(data){
                            load_views();
                        }
                    });
                },
                'No, please don\'t!':function(){
                    $(this).dialog('close');
                }
            }
        })

    });
//---END $(document).ready
});

function load_defs(){}
function load_views(order,active){
    if(order==null) order='';
    url=base_url+'dna2/be/load_views/'+idapp+'/'+order;
    //---load dialog
    $( "#dialog-message" ).dialog({
        modal: true,
        title: 'LOADING'

    });
    //----
    $.ajax({
        url : url,
        async:false,
        success:function(data){
            $('#center').html(data);
            initButtons();
            $('#obj_sorter button.btn_'+order).addClass('ui-state-active');
            $("#accordion").accordion({
                change: function(event, ui) {
                    idform=ui.newContent.find('input[name=idform]').val();
                    idobj=ui.newContent.find('input[name=idobj]').val();
                    
                    //console.log(idform);
                    url=base_url+'dna2/be/get_properties/'+idform+'/'+idobj+'/'+idapp;
                    $('#pForm').html('<img src="'+base_url+'css/ajax/loader18.gif"/>Loading properties for:'+idform).load(url,'',function(){
                        $('#eMSG').html('');
                        $("#pForm button").button();
                    });
                },
                animated: false

            });
            if(active!=null){
                $("#accordion").accordion('activate',$('#'+active));
            } else{
                //---load first object properties
                idobj=$('#accordion .desc:first input[name=idobj]').val();
                idform=$('#accordion .desc:first input[name=idform]').val();
                url=base_url+'dna2/be/get_properties/'+idform+'/'+idobj+'/'+idapp;
                $('#pForm').html('<img src="'+base_url+'css/ajax/loader18.gif"/>Loading properties for:'+idform).load(url,'',function(){
                    $('#eMSG').html('');
                    $("#pForm button").button();
                });
            }
            //----------------------------------------
            $('#type').live('change',function(){
                idform=$(this).parents('form').find('input[name=idform]').val();
                idobj=$(this).parents('form').find('input[name=idobj]').val();
                url=base_url+'dna2/be/get_properties/'+idform+'/'+idobj+'/'+idapp+'/'+$(this).find('option:selected').val();
                $('#pForm').load(url,'',function(){
                    $('#eMSG').html('');
                    $("#pForm button").button();
                });
            });
            $('#btn_edit_filters').live('click',function(){
                idobj=$(this).parents('form').find('input[name=idobj]').val();
                jsonstring=$('#pTable textarea[name=filters]').val();
                url=base_url+'dna2/be/get_json_editor/'+idobj;
                postData={
                    'jsonstring':jsonstring
                };
                $('#jsonEditor').load(url,postData).dialog({
                    modal:true,
                    resizable: true,
                    autoResize: true,
                    width:480,
                    buttons:{
                        'Save':function(){
                            //---clear unused spaces / deleted conditions
                            $('#jTable tr input[name=frame\[\]]').each(function(index,element){
                                if(!element.value){
                                    $(element).parents('tr').remove();
                                }
                            });
                            //---now serialize form and submit
                            postData=$('#jsonEditor form').serializeObject();
                            url=base_url+'dna2/be/decode_filters/'+idobj;
                            $.ajax({
                                url : url,
                                async:false,
                                type: 'POST',
                                data:postData,
                                //dataType:'json',
                                success:function(data){
                                    //console.log(data);
                                    $('#pTable textarea[name=filters]').val(data);
                                    $('#jsonEditor').dialog('close');

                                }
                            });
                        },
                        '[+]Add condition':function(){
                            clon=$('#jTable tbody tr').last().clone();
                            $(clon).css('display','');
                            console.log(clon);
                            $('#jTable tbody tr').last().before(clon);
                        }
                    }
                });

            });
            $('.json_remove').live('click',function(){
                $(this).parents('tr').remove();
            });
            $('#obj_sorter button').click(function(){
                //console.log($(this).attr('value'));
                load_views($(this).attr('value'),null);
            
            });
            //---destroy dialog
            $("#dialog-message").dialog( "destroy" );
        }
    });
//----select the button

}
function load_options(){}
function load_users(){}
function load_groups(){}


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

function initButtons(){
    $("button").button();
    //---fg buttons
    //all hover and click logic for buttons
    $(".fg-button:not(.ui-state-disabled)")
    .hover(
        function(){
            $(this).addClass("ui-state-hover");
        },
        function(){
            $(this).removeClass("ui-state-hover");
        }
        )
    .mousedown(function(){
        $(this).parents('.fg-buttonset-single:first').find(".fg-button.ui-state-active").removeClass("ui-state-active");
        if( $(this).is('.ui-state-active.fg-button-toggleable, .fg-buttonset-multi .ui-state-active') ){
            $(this).removeClass("ui-state-active");
        }
        else {
            $(this).addClass("ui-state-active");
        }
    })
    .mouseup(function(){
        if(! $(this).is('.fg-button-toggleable, .fg-buttonset-single .fg-button,  .fg-buttonset-multi .fg-button') ){
            $(this).removeClass("ui-state-active");
        }
    });

//---fg buttons
};

function initEditors(){
    //----------------------------
    $('.editor').live('click',function(event){
        event.preventDefault();
        //--Set variables
        object=$(this).attr('code_object');
        context=$(this).attr('code_context');
        language=$(this).attr('code_language');
        $('#codeEditor [name=object]').val(object);
        $('#codeEditor [name=context]').val(context);
        $('#codeEditor [name=language]').val(language);
        //---load code from database
        url=base_url+'dna2/form/load_code/'+object+'/'+context+'/'+language;

        $.ajax({
            url : url,
            async:false,
            type: 'get',
            success:function(data){
                $('#code').html(data);


            }
        });

        $('#codeEditor').dialog({
            title:  'Code Editor:'+object+':'+context+':'+language,
            autoOpen: true,
            width: 800,
            height: 500,
            modal: true,
            resizable: true,
            autoResize: true,
            buttons: {
                Save: function() {

                    postData={
                        code:editAreaLoader.getValue("code")
                    };
                    url=base_url+'dna2/form/save_code/'+object+'/'+context+'/'+language;
                    $.ajax({
                        url : url,
                        async:false,
                        type: 'POST',
                        data:postData,
                        success:function(data){
                            $('#codeMsg').append(data);
                        }
                    });
                //console.log(form.find('[name=default]'));
                //$( this ).dialog( "close" );
                },
                Restore: function() {
                    //console.log(form.find('[name=default]'));
                    $( this ).dialog( "close" );
                }
            },
            open: function(){
                $('#codeEditor form').css('height','95%');
            //---Init Editor
            //

            }
        });
        //----END DIALOG
        editAreaLoader.init({
            id: "code",	// id of the textarea to transform
            start_highlight: true,	// if start with highlight
            allow_resize: "none",
            allow_toggle: false,
            word_wrap: true,
            language: "en",
            syntax: $(this).attr('code_language')
        });


    });
//----END .editor
}

