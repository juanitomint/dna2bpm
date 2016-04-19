/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {

    // Save AJAX
    $('#form_profile').on('submit', function(e) {
        e.preventDefault();
        if ($(this).valid()) {
            var post = $(this).serializeArray();

            $.post(globals['base_url'] + 'user/profile/save', {
                'data': post
            }, function(result) {
                console.log(result);
                var feedback = JSON.parse(result);

                if (feedback.ok == 1) {
                    $('.content').prepend('<div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Profile saved!</div>');
                }
                else {
                    $('.content').prepend('<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><strong>Profile couldn\'t be saved!</div>');

                }
            });
        }
    });
    /**
     * Validate!
     */
    $('#form_profile').validate({
        // debug:true,
        rules: {
            lastname:"required",
            email: {
                required: true,
                email: true
            },
            passw: {
                minlength: 6,
                maxlength: 18,

            },

            passw2: {
                equalTo: "#passw",
                minlength: 6,
                maxlength: 18
            }

        }
    });

    $(".calendar").datepicker();

    var changeYear = $(".calendar").datepicker("option", "changeYear");
    var changeMonth = $(".calendar").datepicker("option", "changeMonth");

    $(".calendar").datepicker("option", "changeMonth", true);
    $(".calendar").datepicker("option", "changeYear", true);
    $(".calendar").datepicker("option", "yearRange", "1920:2013");



    // UPLOADER INIT
    var uploader = new plupload.Uploader({
        runtimes: 'html5,html4',
        browse_button: 'pickfiles', // you can pass in id...
        container: document.getElementById('container'), // ... or DOM Element itself
        url: globals['base_url'] + 'user/profile/upload',
        flash_swf_url: globals['base_url'] + 'jscript/plupload-2.1.2/Moxie.swf',
        silverlight_xap_url: globals['base_url'] + 'jscript/plupload-2.1.2/Moxie.xap',

        filters: {
            max_file_size: '10mb',
            mime_types: [{
                title: "Image files",
                extensions: "jpg,png"
            }]
        },

        init: {
            PostInit: function() {
                document.getElementById('filelist').innerHTML = '';
                document.getElementById('uploadfiles').onclick = function() {
                    uploader.start();
                    return false;
                };
            },
            UploadComplete: function(up, files) {
                var myfile = files[files.length - 1].name;
                var url = globals['myidu'] + "." + myfile.substr(-3);

                $('img.avatar').replaceWith("<img  id='avatar' class='avatar' src='" + globals['base_url'] + 'images/avatar/' + url + "'>");
            },
            FilesAdded: function(up, files) {
                plupload.each(files, function(file) {
                    document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                });
            },

            UploadProgress: function(up, file) {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
            },

            Error: function(up, err) {

                document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
            }
        }
    });

    uploader.init();


});
