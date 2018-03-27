/**
 * Main JS
 * Author: Juan Ignacio Borda
 **/
// angular.module('formioForm', ['formio'])

// What to do when the submit begins.
Formio.createForm(document.getElementById('formio'), globals.src)
    .then(function(form) {
        // What to do when the submit begins.
        form.on('submitDone', function(submission) {
            //----back to bpm engine
            window.location = globals.action_post + submission._id;
        });
    });


$('body').on('click', '.delete-resource', function(e) {
    e.preventDefault();
    var element = $(this);
    $.ajax({
        url: element.attr('href'),
        method: "DELETE",
        dataType: 'json',
        success: function(r) {
            if (r.response) {
                element.parent().parent().hide();
            }
        }
    });
});
