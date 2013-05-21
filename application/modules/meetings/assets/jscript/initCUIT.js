function initCUIT(){   
        $("#CUIT").val('');
        $('#result').html('');
        $("#CUIT").mask('99-99999999-9',{
                completed:function(){
                        val=this.val();
                        l=val.replace(/\-/g,'').trim().length;
                        if(l==11){
                                $.mobile.showPageLoadingMsg('a','loading...',true);
                                $('#result').load(globals.module_url+'business/get_data_cuit/'+val,
                                        function(){
                                                $.mobile.hidePageLoadingMsg();      
                                                $('#result').trigger("create");
                                        });
                                                        
                        }else{
                                $('#result').html('');
                        }
                        return true;
                }
                                        
        });
}
$(document).bind('pageinit',initCUIT);