$(document).ready(function() {

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

    $(document).on('click', "#gitStatusReload", function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        if (url) {
            //@todo add a loading mask overlay
            var box = $(this).parents('.box');
            box.html('<i class="fa fa-2x fa-refresh fa-spin"></i>');
            $.ajax({
                url: url,
                context: box,
                async: false
            }).done(function(data) {
                $(this).replaceWith(data);
                init_sortable();
            });
        }
    });
    $(document).on('click', "#pushBtn", function() {
        url = globals.base_url + 'gitmod/push';
        $('#myModal').find('.modal-title').html('Push');
        $('#myModal').find('.modal-body').html('<h1><i class="fa fa-circle-o-notch fa-spin"></i> Pushing to upstream...</h1>');
        $('#myModal').modal('show');
        $.ajax({
            url: url,
            type: 'POST',
            success: function(data) {
                $('#result').prepend(data.status);
                $('#myModal').modal('hide');
            },
            statusCode: {
                404: function() {
                    alert("page not found");
                    $('#myModal').modal('hide');
                }
            }
        });
    });

    $(document).on('click', "#gitCommit", function() {
        $('#gitModal').modal('show');
    });

    $(document).on('click', "#commitBtn", function() {
        data = {
            'commitTxt': $(this).parent().parent().find('textarea').val()
        };
        url = globals.base_url + 'gitmod/commit';
        $('#gitModal').modal('hide');
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(data) {
                $('#result').prepend(data);
                reload_all();
            },
            statusCode: {
                404: function() {
                    alert("page not found");
                }
            }
        });
    });

    $(document).on('click', ".gitRevert", function(event) {
        event.preventDefault();
        data = {
            'files': [$(this).attr('href')]
        };
        url = globals.base_url + 'gitmod/revert';
        $('#gitModal').modal('hide');
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(data) {
                $('#result').prepend(data);
                reload_all();
            },
            statusCode: {
                404: function() {
                    alert("page not found");
                }
            }
        });
    });

    init_sortable();


});

/*
/   Init sortable
*/
function init_sortable() {
        $(".connectedSortable").sortable({
            placeholder: "sort-highlight",
            connectWith: ".connectedSortable",
            handle: ".box-header, .nav-tabs",
            forcePlaceholderSize: true,
            zIndex: 999999
        }).disableSelection();

        $(".todo-list").sortable({
            placeholder: "sort-highlight",
            handle: ".handle",
            forcePlaceholderSize: true,
            zIndex: 999999
        }).disableSelection();;

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
                // console.log(oldList, newList);
                if (newList != oldList) {

                    switch (oldList) {
                        case "staged":
                            // console.log('un-stage this file:' + data.filename);
                            url = globals.base_url + 'gitmod/unstage';
                            break;

                        case "status":
                            // console.log('stage this file:' + data.filename);
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

    }
    /*
    /   Reload all 
    */

function reload_all() {
    // $('#myModal').find('.modal-title').html('Reload All');
    // $('#myModal').find('.modal-body').html('<h1><i class="fa fa-circle-o-notch fa-spin"></i> Refreshing...</h1>');
    // $('#myModal').modal('show');
    $(".widget_url").each(

        function(index, item) {
            var url = $(item).text()
            if (url) {
                //@todo add a loading mask overlay
                var box = $(item).parents('.box');
                box.html('<i class="fa fa-2x fa-refresh fa-spin"></i>');
                $.ajax({
                    url: url,
                    context: box,
                    async: false
                }).done(function(data) {
                    $(this).replaceWith(data);
                });
            }
        });
    /*
    /  Enabled sortable again
    */
    init_sortable();
    // $('#myModal').modal('hide');
}
