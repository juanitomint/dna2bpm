$(document).ready(function(){
    $('#myModal').modal(globals.options);
    
    $('#myModal').on('hidden',
        function() {
            url=globals.base_url+'dashboard';
            //window.location=url;
        });
    
    $('#testTask').click(function(){
        url=globals.base_url+'bpm/test/run_test/'+globals.idwf+'/'+globals.idcase+'/'+globals.resourceId;
        data={script: editor.getValue()}
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
        data={script: editor.getValue()}
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
     var editor = ace.edit("editor");
     editor.setTheme("ace/theme/monokai");
     editor.session.setMode("ace/mode/php");
});