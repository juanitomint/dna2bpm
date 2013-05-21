/*
$('#view-table').live('submit',function(e){
        e.preventDefault();
        $.mobile.showPageLoadingMsg('a','loading...',true);
        $('.content-primary').load(globals.module_url+'print_tables',{
                'table':$('#search-table').val()
        },function(){
                $.mobile.hidePageLoadingMsg();  
                $('.content-primary').trigger("create");
        });
        return false;
});
$('#view-cuit').live('submit',function(e){
        e.preventDefault();
        $.mobile.showPageLoadingMsg('a','loading...',true);
        $('#result').load(globals.module_url+'business/get_data_cuit/'+$('#CUIT').val(),function(){
                $.mobile.hidePageLoadingMsg();
                $('#result').trigger("create");
                                        
        });
        return false;
});
$('#view-agenda').live('click',function(e){
        $.mobile.showPageLoadingMsg('a','loading...',true);
        $('#result').load(globals.module_url+'print_business/'+$('#CUIT').val(),function(){
                $.mobile.hidePageLoadingMsg();
                $('#result').trigger("create");
                                        
        });
});*/
$(document).ready(function (){
    
$('a').on('click',function(e){
    
    isMenu=$(this).attr('isMenu');
    isClear=$(this).hasClass('ui-input-clear')
    //---if has isMenu then load whole page
    if(!(isMenu||isClear)){
            
        e.preventDefault();
        
        $.mobile.showPageLoadingMsg('a','loading...',true);
        $('.content-primary').load($(this).attr('href'),function(){
            $.mobile.hidePageLoadingMsg();  
            $('.content-primary').trigger("create");
        });
        return false;
    }
});
$('form').on('submit',function(e){
    e.preventDefault();
    $.post($(this).attr("action"), $(this).serialize(), function(html) {
        $('.content-primary').html(html);
        $('.content-primary').trigger("create");
    });
    return false; // prevent normal submit
});
});