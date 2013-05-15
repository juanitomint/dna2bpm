
$(document).ready(function(){
    
    $('#myModal').on('hidden',
        function() {
            url=globals.base_url+'dna2/dashboard';
            window.location=url;
        });
    
    $('#closeTask').click(function(){
        $('#myModal').hide();
    });

    $('#myModal').modal(globals.options);
});