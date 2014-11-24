$(document).ready(function() {
    $("#commitBtn").click(function() {
        data = {
            'commitTxt': $(this).parent().parent().find('textarea').val()
        };
        url = globals.base_url + 'gitmod/commit';
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(data) {
                $('#result').html(data);
                //@todo reload staged
            },
            statusCode: {
                404: function() {
                    alert("page not found");
                }
            }
        });
    })
    $(".connectedSortable").sortable({
        start: function(event, ui) {
            item = ui.item;
            oldList = ui.item.parent().attr('id');
        },
        stop: function(event, ui) {
            data = {
                'files': [ui.item.find('.filename').text()]

            };
            newList = ui.item.parent().attr('id');
            console.log(oldList, newList);
            if (newList != oldList) {

                switch (oldList) {
                    case "staged":
                        console.log('un-stage this file:' + data.filename);
                        url = globals.base_url + 'gitmod/unstage';
                        break;

                    case "status":
                        console.log('stage this file:' + data.filename);
                        url = globals.base_url + 'gitmod/stage';
                        break;
                }
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    success: function(data) {
                        $('#result').prepend(data);
                    },
                    statusCode: {
                        404: function() {
                            alert("page not found");
                        }
                    }
                });
            }
        }
    });

});