   $(document).on('click', "#pullBtn", function() {
        url = globals.base_url + 'gitmod/pull';
        $('#myModal').find('.modal-title').html('Pull');
        $('#myModal').find('.modal-body').html('<h1><i class="fa fa-circle-o-notch fa-spin"></i> Pulling from upstream...</h1>');
        $('#myModal').modal('show');

        $.ajax({
            url: url,
            type: 'POST',
            success: function(data) {
                $('#result').prepend(data.status);
                $('#myModal').modal('hide');
                reload_all();
            },
            statusCode: {
                404: function() {
                    alert("page not found");
                    $('#myModal').modal('hide');
                }
            }
        });
    });