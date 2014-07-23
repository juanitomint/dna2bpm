/**
 * Main JS
 * Author: Gabriel Fojo
 **/

$(document).ready(function() {

$('.form-extra').ajaxForm({
    target:'#tiles_after',
    replaceTarget:false
});
    // ==== Reload Widget
    $(document).on('click', '.reload_widget', function(event) {
        event.preventDefault();
        var box = $(this).parents('.box');
        var url = $(this).attr('href');

        $.ajax({
            url: url,
            context: box
        }).done(function(data) {
            $(this).replaceWith(data);
        });
    });

 // ==== Load Tiles 
    $(document).on('click', '.load_tiles_after', function(event) {
        event.preventDefault();
        var box = $(this).parents('.box');
        var url = $(this).attr('href');

        $.ajax({
            url: url,
            context: box
        })
                .done(function(data) {
            $('#tiles_after section').html(data);
        })
                .error(function(jqXHR,textStatus,errorThrown){
                    $('#tiles_after section').html(textStatus+errorThrown);
                })
        ;
    });
    
// ==== Load Modal 
    $(document).on('click', '.load_modal', function(event) {
        event.preventDefault();

        var url = $(this).attr('href');
        var title= $(this).attr('title')?($(this).attr('title')):('Title');
        $.ajax({
            url: url,
            context: document.body
        })
                .done(function(data) {
            $('#myModal').find('.modal-title').html(title);
            $('#myModal').find('.modal-body').html(data);
            $('#myModal').modal('show');

        })
                .error(function(jqXHR,textStatus,errorThrown){
                    $('#tiles_after section').html(textStatus+errorThrown);
                })
        ;
    });

// ==== Make the dashboard widgets sortable Using jquery UI
    $(".connectedSortable").sortable({
        placeholder: "sort-highlight",
        connectWith: ".connectedSortable",
        handle: ".box-header, .nav-tabs",
        forcePlaceholderSize: true,
        zIndex: 999999
    }).disableSelection();
    $(".box-header, .nav-tabs").css("cursor", "move");
    //jQuery UI sortable for the todo list
    $(".todo-list").sortable({
        placeholder: "sort-highlight",
        handle: ".handle",
        forcePlaceholderSize: true,
        zIndex: 999999
    }).disableSelection();
    ;




});