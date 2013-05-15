$(document).ready(function(){
    //--load diagrams on center
    loadModels();
    $("#newDiagram").click(function(){
        idwf=$('#idwf').val();
        if(idwf){
            url=base_url+'jscript/bpm/editor.xhtml#model/'+idwf;
            window.open(url);
        }else{
    //TODO show some warning if idwf is empty
    }
    });

    $("#import").click(function(){
        idwf=$('#idwf_file').val();
        if(idwf){
            $('#center').html("<img src='"+base_url+"css/ajax/loadingAnimation.gif'/>");
            url=base_url+'bpm/repository/import/model/'+idwf;
            $('#msg').load(url,'',function(){
                $('#center').load(base_url+'bpm/browser/get_models');
            });

        }else{
    //TODO show some warning if idwf is empty
    }
    });
    //---BTN Page
    $('.btn_page').live('click',function(){
        order=$("#radio input[name=order]:checked").val();
        active=null;
        query=$('#query').val();
        page=$(this).val();
        loadModels(order,query,page);

    });
       //---BTN SEARCH
    $('.btn_search').live('click',function(){
        order=$("#radio input[name=order]:checked").val();
        active=null;
        query=$('#query').val();
        page=1;
        loadModels(order,query,page);


    });
});//---end document ready

function loadModels(order,query,page){
    if(order==null) order='data.properties.name';
    if(query==null) query='';
    if(page==null) page='1';
    $('#center').load(base_url+'bpm/browser/get_models/'+order+'/'+page+'/'+query,'',function(){

    //--add order functionality
            $('#radio').buttonset();
            $('#pages').buttonset();
            $('#radio .btn_order').click(function(){
                order=$(this).attr('value');
                query=$('#query').val();
                loadModels(order,query,page);
            });
    });
}