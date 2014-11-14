$(document).ready(function(){
    
    $('#myModal').on('hidden',
        function() {
            url=globals.base_url+'dashboard';
            window.location=url;
        });
    
    $('#testTask').click(function(){
        url=globals.base_url+'bpm/test/run_test/'+globals.idwf+'/'+globals.idcase+'/'+globals.resourceId;
        data={script:editAreaLoader.getValue("editArea")}
         $.ajax({
        url : url,
        type: 'POST',
        data:data,
        //dataType:'json',
        success:function(data){
            $('#results').html(data);
        }
        });
    
    });
    $('#saveTask').click(function(){
        url=globals.base_url+'bpm/test/save_script/'+globals.idwf+'/'+globals.resourceId;
        data={script:editAreaLoader.getValue("editArea")}
         $.ajax({
        url : url,
        type: 'POST',
        data:data,
        //dataType:'json',
        success:function(data){
            $('#results').html(data);
        }
        });
    
    });
    editAreaLoader.init({
			id: "editArea"	// id of the textarea to transform		
			,start_highlight: true	// if start with highlight
			,allow_resize: "both"
			,allow_toggle: true
			,word_wrap: true
			,language: "en"
			,syntax: "php"	
		});
    $('#myModal').modal(globals.options);
});