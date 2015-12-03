$(document).ready(function() {

    // ===== AJAX SELECT BOX
    //$('.select2').select2();
    $('#from').select2({
        ajax: {
            dataType: 'json',
            type: "POST",
            url: globals.base_url + "inbox/inbox/get_users",
            data: function(term) {
                return {
                    term: term
                };
            },
            results: function(result) {
                return result;
            }
        }

    });

    $('#to').select2({
        ajax: {
            dataType: 'json',
            type: "POST",
            url: globals.base_url + "inbox/inbox/get_users",
            data: function(term) {
                return {
                    term: term
                };
            },
            results: function(result) {
                return result;
            }
        }

    });

    $(document).on('click', '#delegate-btn', function(e) {
        var url = globals.base_url + 'bpm/case_manager/delegate/' + globals.idwf + '/' + globals.idcase + '/' + $("#from").val() + '/' + $("#to").val();
        $.ajax({
            url: url,
            type: 'POST',
            success: function(data) {
            console.log(data);
             if(data.ok){
                 window.location=globals.base_url;
             }
            },
            error: function(err) {
                alert(err.toString());
            }
        });
    });
});