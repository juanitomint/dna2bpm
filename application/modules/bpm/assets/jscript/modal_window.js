$(document).ready(function(){
    
    $('#myModal').on('hidden',
        function() {
            url=globals.base_url+'dashboard';
            window.location=url;
        });
    
    $('#closeTask').click(function(){
        $('#myModal').hide();
    });

    $('#myModal').modal(globals.options);
});