function preloadProps(){
    //----Get custom properties for object
    url=base_url+'dna2/form/get_properties/';

    $( "#dialog-message" ).dialog({
        modal: true,
        title: 'Pre-Loading objects templates'

    });
    j=1;
    qtty=o_types.length;
    ammt=100/qtty;
    for(o in o_types){
        oid=o_types[o];
        thisurl=url+oid;
        $.ajax({
            url : thisurl,
            async:false,
            success:function(data){
                custom_props[oid]= data;
                loadMsg='<span class="ui-icon ui-icon-circle-check" style="float:left;margin:0 7px 0px 0;"/>' + oid + ' Loaded!<br/>';
                $('#saveMsg').append(loadMsg);
                $("#saveMsg").attr({
                    scrollTop: $("#saveMsg").attr("scrollHeight")
                });
                $('#progressbar').progressbar('value',(j*ammt));
            }
        });
        j++;
    }

    $( "#dialog-message" ).dialog('destroy');
}
function preloadFrames(){

    $('#saveMsg').html('');
    $( "#dialog-message" ).dialog({
        modal: true,
        title: 'Pre-Loading Frame Data'

    });
    $('.sortable-list').children().each(function() {

        otype=$(this).find('[name=otype]').val();
        oindex=$(this).find('[name=oindex]').val();
        idframe=$(this).find('[name=idframe]').val();
        $('#pForm').append('<form name="prop_'+oindex+'" id="prop_'+oindex+'"><img src="'+base_url+'css/ajax/loader18.gif"/>Loading properties for:'+idframe+'</form>');

        thisurl=base_url+'dna2/form/get_properties/'+otype+'/'+imin+'/'+idframe;
        $.ajax({
            url : thisurl,
            async:false,
            success:function(data){

                $('#saveMsg').append('<span class="ui-icon ui-icon-circle-check" style="float:left;margin:0 7px 0px 0;"/>Frame:'+idframe+'  Loaded!<br/>');
                $("#saveMsg").attr({
                    scrollTop: $("#saveMsg").attr("scrollHeight")
                });

                $('#pForm #prop_'+oindex).html(data);
                $('#prop_'+oindex).hide();
            }
        });



    });
    $( "#dialog-message" ).dialog('destroy');
}

/// START dcument.ready ////
$(document).ready(function(){

    var wsize=300;
    var esize=300;
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

    if(!nopreload) preloadProps();
    initButtons();
    //preloadFrames();

    //---------------------------------------------------------------
    //--------------INIT BTN SAVE------------------------------------
    //---------------------------------------------------------------
    $('#btnSave').click(function(){
        $( "#progressbar" ).progressbar({
            value: 32
        });

        //----SETUP DIALAGO
        $( "#dialog-message" ).dialog({
            modal: true,
            title: 'Saving...',
            heigth:300,
            width:550,
            buttons: {
                Ok: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
        //----Make some calculations 4 progressbar
        qtty=$('.sortable-list').children().length;
        ammt= Math.round(100/qtty);
        ammt= 100/qtty;
        url=base_url+'dna2/form/save_frame/'+idform+'/';
        column=0;
        columns=new Array();
        $('#saveMsg').html('');
        //----Iterate over sortable list
        j=1;
         thiscol=new Array();
        $('.sortable-list li.added').each(function(){
            //----Iterate over li elements
                index=$(this).find('input[name=oindex]').val();
                idframe=$(this).find('input[name=idframe]').val();
                idframe=(idframe)?idframe:'';
                thisurl=url+idframe;
                thiscol[j]='col[]='+idframe;
                //---add to the array;
                //
                //console.log(index,thisurl,j*ammt);
                if($('#prop_'+index).length){
                    //---make multiple selected
                    $('#saveMsg').append('Posting data for item:'+index+' Frame:'+idframe+'<br/>');
                    
                    frame=$('#prop_'+index).serializeObject();
                    data=$.param({
                        'column':column,
                        'index':index,
                        'frame':frame
                    });

                    postData=data;
                    $.ajax({
                        url : thisurl,
                        async:false,
                        type: 'POST',
                        data:postData,
                        //dataType:'json',
                        success:function(data){
                            //console.log(data);
                            thiscol[j]='col[]='+data.idframe;
                            $('#saveMsg').append(data);
                            $("#saveMsg").attr({
                                scrollTop: $("#saveMsg").attr("scrollHeight")
                            });

                        }
                    });
                }
                $('#progressbar').progressbar('value',(j*ammt));
                j++;
        });//---end sortable
            columns[column]=thiscol.join('&');
            column++;
            $('#saveMsg').append(j+' Frames saved');
        //console.log('columns',columns);
        //----now store columns into form
        j=1;
        for(column in columns){
            data=columns[column];
            formData=$.param({
                column:j
            });
            postData=formData+'&'+data;
            url=base_url+'dna2/form/save_column/'+idform+'/';
            $.ajax({
                url : url,
                async:false,
                type: 'POST',
                data:postData,
                success:function(data){
                    $('#saveMsg').append(data);

                }
            });
            j++;
        }




    });

    //---------------------------------------------------------------
    //--------------INIT BTN AddDefault-------------------------------
    //---------------------------------------------------------------
    $("#defaultOps button").click(function(){
        var arr = $(this).attr("name").split("2");
        var from = arr[0];
        var to = arr[1];
        $("#default_" + from + " option:selected").each(function(){
            $("#default_" + to).append($(this).clone());
            $(this).remove();
        });
        $('#default_left').sortOptions();
    });
    $('.btnDefault').live('click',function(){
        
        idop=$(this).parents('form').find('[name=idop]').val();
        form=$(this).parents('form')
        thisDefault=form.find('[name=default]');
        //                        $('#dialog').html('<img src="'+base_url+'css/ajax/loader18.gif"/>Loading');
        //----clear selection
        $('#default_right').html('');

        url=base_url+'/dna2/form/default_picker/'+idop;

        $('#defaultOps').dialog({
            title:  'Choose Default Options',
            autoOpen: true,
            width: 800,
            height: 360,
            modal: true,
            //resizable: true,
            //autoResize: true,
            buttons: {
                Ok: function() {
                    //console.log(form.find('[name=default]'));
                    thisDefault.html($('#default_right').html());
                    thisDefault.selectOptions(/./i);
                    $( this ).dialog( "close" );
                }
            }
        });
        //LOAD OPTIONS FROM JSON SERVICE
        $('#default_left').html('');
        $.getJSON(base_url+'dna2/form/Json_getoption/'+idop,function(option){
            $.each(option.data,function(i,item){
                $('#default_left').append('<option value="'+item.value+'">'+item.text+'</option>');
            });
            //---sort
            $('#default_left').sortOptions();
            //---now pass to right the defaults already in form
            thisDefault.selectOptions(/./);
            $("#default_right").html(thisDefault.html());
            $('#default_left').removeOption(thisDefault.selectedValues());

        });
    //end LOAD

    });
    //---------------------------------------------------------------
    //--------------INIT CheckAll------------------------------------
    //---------------------------------------------------------------

    $('.checkAll').live('click',function(){
        name=$(this).attr('name');
        //console.log($(this).parents('.column').find('.'+name));
        $(this).parents('.column').find('[name='+name+']').attr({
            checked: $(this).attr('checked')
        });
        $('#pForm form [name='+name+']').attr({
            checked: $(this).attr('checked')
        });
    });

    $('.checkOne').live('click',function(){
        name=$(this).attr('name');
        oindex=$(this).parents('li').find('[name=oindex]').val();
        $('#prop_'+oindex+' [name='+name+']').attr({
            checked: $(this).attr('checked')
        });
    });
    $('.checkProp').live('click',function(){
        name=$(this).attr('name');
        form=$(this).parents('form').attr('name').split('_');
        oindex=form[1];
        //oindex=$(this).parents('li').find('[name=oindex]').val();
        $($('.sortable-list').children().get(oindex-1)).find(' [name='+name+']').attr({
            checked: $(this).attr('checked')
        });
    });


    //---------------------------------------------------------------
    //--------------INIT BTN AddColumn-------------------------------
    //---------------------------------------------------------------
    $('#btnAddColumn').click(function(){
        col=$('.column').length+1;
        url=base_url+'dna2/form/get_newcol/'+col;
        $.ajax({
            url : url,
            async:false,
            type: 'get',
            success:function(data){
                $('.clearer').before(data);

            }
        });


        percent=(100/$('.column').length-$('.column').length)+'%';
        //alert('percent'+percent);
        $('.column').css('width',percent);
        initSortable();
    });

    //---------------------------------------------------------------
    //------------Start LAYOUT --------------------------------------
    //---------------------------------------------------------------
    //--draw original layout



    //---------------------------------------------------------------
    //--------------INIT BUTTONS-------------------------------------
    //---------------------------------------------------------------
    $("button").button();
    $(".button").button();

    //---set only vertical scroll 4 west
    $('#west').css('overflow', 'visible');

    //---fix content overflow
    $(window).resize(function(){
        $('#menu-side').height($('#west').height());
    });
    $(window).resize();


    //---------------------------------------------------------------
    //--------------INIT DRAGGABLE-----------------------------------
    //---------------------------------------------------------------

    $('#object-list li').draggable(

    //---options
    {

            helper: 'clone',
            connectToSortable: '#layout .sortable-list',
            revert: 'invalid'
        });

    //---------------------------------------------------------------
    //--------------INIT SORTABLE------------------------------------
    //---------------------------------------------------------------

    $('#layout .sortable-list').children().addClass('added');

    function initSortable(){
        $('#layout .sortable-list').sortable({
            connectWith: '#layout .sortable-list',
            placeholder: 'placeholder',
            handle: '.handle',
            receive: function(event, ui) {
                //console.log(ui.item.hasClass('source'));
                //----Check if has been droped from source object list
                if(ui.item.hasClass('source')){
                    //---Get the object type
                    otype=ui.item.find('[name=oid]').val();
                    //--get the new item
                    var item = $(this).find('li:not(.added)');
                    // do something with "item" - its your new pretty cloned dropped item ;]
                    item.html('<input type="hidden" name="otype" value="'+otype+'" /><input type="hidden" name="oindex" value="'+o_iterator+'" /><div class="content">item:'+o_iterator+'<br/>Type:'+otype+'</div>');
                    //---load properties

                    $('#pForm form:visible').hide();
                    $('#pForm').append('<form id="prop_'+o_iterator+'">'+custom_props[otype]+'</form>');
                    //---increase iterator value for uniqueness
                    o_iterator++;
                    //---remove the source class
                    item.removeClass('source');
                    //---add the 'added' class so we can find new items in the next run
                    $('#layout .sortable-list').children().removeClass('active');
                    item.addClass('added active');

                }



            },
            beforeStop: function(event, ui) {
            //console.log(event.type,event,ui,$(this));
            }


        });
    }
    percent=100/$('.column').length-$('.column').length+'%';
    //alert('percent'+percent);
    $('.column').css('width',percent);
    initSortable();


    $('.sortable-list li.added').live('click',function(e){

        $('#layout .sortable-list').children().removeClass('active');
        $('#pForm form:visible').hide();
        otype=$(this).find('[name=otype]').val();
        oindex=$(this).find('[name=oindex]').val();


        //console.log('activating:'+otype,'item:',oindex);
        //---if not exists then try to load
        if(!$('#pForm #prop_'+oindex).length){
            idframe=$(this).find('[name=idframe]').val();
            $('#pForm').append('<form name="prop_'+oindex+'" id="prop_'+oindex+'"><img src="'+base_url+'css/ajax/loader18.gif"/>Loading properties for:'+idframe+'</form>')
            url=base_url+'dna2/form/get_properties/'+otype+'/'+imin+'/'+idframe;
            $.get(url, function(data){
                $('#pForm #prop_'+oindex).html(data);
                initButtons();
            });
        }
        $('#pForm #prop_'+oindex).show();
        $(this).addClass('active');
    });


    $('.editor').click(function(event){
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
            dialogClass: 'dot7',
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

    //----Change idop
    $('#pTable .idop').live('change',function(){
        idop=$(this).val();
        idop_default=$(this).parents('form').find('.idop_default');
        $(idop_default).removeOption(/./);
        $(idop_default).removeOption('');
        $.getJSON(base_url+'dna2/form/Json_getoption/'+idop,function(option){
            $(idop_default).append('<option value="">None</option>');
            $.each(option.data,function(i,item){
                $(idop_default).append('<option value="'+item.value+'">'+item.text+'</option>');
            });
        //---sort
        //$(idop_default).sortOptions();
        });
    });

    //----Change type
    $('#pTable .type').live('change',function(){
        otype=$(this).val();
        //console.log('Fetching 4 type'+otype);
        url=base_url+'dna2/form/get_properties/'+otype+'/'+imin+'/'+idframe;
        $(this).parents('form').html('<img src="'+base_url+'css/ajax/loader18.gif"/>Loading properties for:'+idframe).load(url);

    });

    //----reload type
    $('#pForm form .reload').live('click',function(){
        otype='reload'
        idframe=$(this).parents('form').find('.idframe').val();

        //console.log('Fetching 4 type'+otype);
        url=base_url+'dna2/form/get_properties/'+otype+'/'+imin+'/'+idframe;
        $(this).parents('form').html('<img src="'+base_url+'css/ajax/loader18.gif"/>Loading properties for:'+idframe).load(url);
        

    });

//---END $(document).ready
})

function load_app(idapp){
    $('#center').load(base_url+'dna2/controlpanel/cp_apps/'+idapp,'',function(){
        $('#dnaTabs').tabs();
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

//-------------4 buttons
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