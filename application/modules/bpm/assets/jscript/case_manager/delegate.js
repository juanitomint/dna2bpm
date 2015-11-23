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

});