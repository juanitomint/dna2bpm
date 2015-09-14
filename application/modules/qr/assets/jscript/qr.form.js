$(document).ready(function(){
    $('#reader').html5_qrcode(
        function(data){
            $('#read').html(data).addClass('alert alert-success');
            $('#readHidden').val(data);
            $('#formqr').submit();
            color=$('body').css('background-color');
            $('body').animate({backgroundColor: '#FFF'},200).animate({backgroundColor: color},100);
            
            
            
        },
        function(error){
            $('#read_error').html(error);
        }, function(videoError){
            $('#vid_error').html(videoError);
        }
        );
});