$(document).ready(function() {
    $('#finishTask-1').hide();
    $('#reader').html5_qrcode(
        function(data) {
            $('#read').html(data).addClass('alert alert-success');
            color = $('body').css('background-color');
            $('body').animate({
                backgroundColor: '#FFF'
            }, 200).animate({
                backgroundColor: color
            }, 100);

            url = globals.module_url + 'task/connector/qr/save_data/' + globals.idwf + '/' + globals.idcase + '/' + globals.resourceId;
            $.post(url, {
                'data': data,
                'resourceId': $('#qr_resourceId').val()
            }, function(res) {
                //---submit task
                if (typeof(res) == 'string')
                    res = $.parseJSON(res);
                    
                if (res.result == true) {
                    $('form').submit();
                }
            });

        },
        function(error) {
            $('#read_error').html(error);
        },
        function(videoError) {
            $('#vid_error').html(videoError);
        }
    );
});
