$(document).ready(function(){
    
    $('#myModal').on('hidden', function () {
        
        url=globals.base_url+'dashboard';
        window.location=url;
        
    });
    
    $('#closeTask').click(
        function() {
            $('#myModal').hide();
        });
    $('#finishTask').click(
        function() {
            url=globals.base_url+'bpm/engine/run_post/model/'+globals.idwf+'/'+globals.idcase+'/'+globals.resourceId;
            window.location=url;
        });
    
});
//---launch modal window
$('#myModal').modal(globals.options);
