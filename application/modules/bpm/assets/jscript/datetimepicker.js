$(document).ready(function(){

    $('.fulldate').datetimepicker(
        {
            locale: globals.locale
        }
        );



    $('.month_year').datetimepicker(
        {

            format: 'MM/YYYY',
            locale: globals.locale

        }
        );
})