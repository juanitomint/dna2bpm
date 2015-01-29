/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
    //session_rectify_ajax();
    $("#is_session").hide();
    $('#myModal').modal('show');


    /*RECTIFICA HREF*/
    $('[class^="rectifica-link_"]').click(function(event) {
        var arr = "01-2014";
        var parameter = null;
        var input_period = null;
        var anexo = null;

        $("#error").html('<i class="fa fa-info-circle"></i> Si rectifica, la información asociada y relacionada será borrada del sistema');
        parameter = $(this).attr('href');
        arr = parameter.split('/');

        if (arr[5] == undefined) {
            input_period = arr[2];
            anexo = arr[3];
        } else {
            input_period = arr[4];
            anexo = arr[5];
        }

        event.preventDefault();
        $.get(globals.module_url + "unset_period");
        $("input[name$='input_period']").val(input_period);
        $("input[name$='anexo']").val(anexo);
        $("#show_anexos").hide();
        $("#is_session").show();
        $("#no_session").hide();
    });


    /*fixed*/
    $('[class^="rectifica-warning_"]').click(function(event) {

        /*VARS*/
        var get_period = null;
        var parameter = null;
        var arr = null;
        var input_period = null;
        var anexo = null;

        get_period = $("#sgr_period").html();
        parameter = $(this).attr('href');
        arr = parameter.split('/');

        if (arr[5] == undefined) {
            input_period = arr[2];
            anexo = arr[3];
        } else {
            input_period = arr[4];
            anexo = arr[5];
        }

        /*SIN DATOS */
        if (input_period == undefined) {
            //           
        } else {
            event.preventDefault();
            $.get(globals.module_url + "unset_period_active");
            bootbox.confirm("El período actual seleccionado ( " + get_period + " ) va a dejar de estar activo por ( " + input_period + " ), desea continuar?", function(result) {
                if (result) {
                    $("#error").html('<i class="fa fa-info-circle"></i> Si rectifica, la información asociada y relacionada será borrada del sistema');
                    $("#sgr_period").html("Rectifica");
                    $("#no_movement").hide();
                    $("#is_session").show();
                    $("#no_session").hide();
                    $("input[name$='input_period']").val(input_period);
                    $("input[name$='anexo']").val(anexo);
                }
            });
        }



    });


    $('.dp').datepicker();
    $('[id^="others_"]').hide();

    // Dashboard Accordion ||
    $('[data-toggle="collapse"]').on('click', function() {
        var is_open = $(this).parents('div').next().hasClass('in');
        if (is_open) {
            $('i', this).removeClass('fa-chevron-up');
            $('i', this).addClass('fa-chevron-down');
        } else {
            $('i', this).removeClass('fa-chevron-down');
            $('i', this).addClass('fa-chevron-up');
        }


    })


    /*RECTIFICAR*/
    $('[id^="rectify_"]').change(function() {
        var option_value = $(this).val();
        if (option_value == 3) {
            $('[id^="others_"]').show();
        } else {
            $('[id^="others_"]').hide();
        }
    });


    function session_rectify_ajax() {
        $.ajax(
                {
                    type: "POST",
                    url: globals.module_url + 'check_session_period',
                    success: function(resp) {
                        if (resp) {
                            //  $("#is_session").show();
                            //  $("#no_session").hide();
                        } else {
                            // $("#no_session").show();
                            // $("#is_session").hide();
                        }
                    }
                });
    }

    function add_no_movement() {
        var no_movement = $('#no_movement').val();
        var data = {'no_movement': no_movement};
        $.ajax(
                {
                    /* this option */
                    async: false,
                    cache: false,
                    type: "POST",
                    dataType: "text",
                    url: globals.module_url + 'set_no_movement',
                    data: {'data': data},
                    success: function(resp) {
                        var new_resp = null;
                        if ((resp == "error141")) {
                            new_resp = "No puede declararse “SIN MOVIMIENTO” si se informó el anexos 12.5 del mismo período o si hay CUIT’s de socios con saldo de deuda, calculados del histórico del anexo 14.";
                            bootbox.alert(new_resp, function() {
                                location.reload();
                            });
                        } else {

                            new_resp = (resp == "ok") ? "El periodo " + no_movement + " fue asociado con Exito" : "Error verifique la informacion";
                            bootbox.alert(new_resp, function() {
                                location.reload();
                            });

                        }



                    }
                });
    }

    $('button.no_movement').click(function() {
        var no_movement = $('#no_movement').val();
        bootbox.confirm("Confirma la asociacion del periodo " + no_movement + " como SIN MOVIMIENTO?", function(result) {
            if (result) {
                add_no_movement();
            }
        });
    });

    $('#dashboard_tab1 a:first').tab('show');
});
function onUpdateReady() {
    // alert('found new version!');
    location.reload();
}
window.applicationCache.addEventListener('updateready', onUpdateReady);
if (window.applicationCache.status === window.applicationCache.UPDATEREADY) {
    onUpdateReady();
}


