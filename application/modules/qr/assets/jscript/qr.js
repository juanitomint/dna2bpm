$(document).ready(function(){
    $('#reader').html5_qrcode(
        function(data){
            $('#read').html(data).addClass('alert alert-success');          
            color=$('body').css('background-color');
            $('body').animate({backgroundColor: '#FFF'},200).animate({backgroundColor: color},100);
            
            url=globals.redir;
            $.post(url,{
                'data': data
            }
            ,function(res){
                $('#result').html(res).addClass('alert alert-success');
            });
            
        },
        function(error){
            $('#read_error').html(error);
        }, function(videoError){
            $('#vid_error').html(videoError);
        }
        );
});
