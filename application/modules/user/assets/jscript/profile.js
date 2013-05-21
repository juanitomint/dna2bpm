/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function() {
$(".calendar").datepicker();
   
    var changeYear = $( ".calendar" ).datepicker( "option", "changeYear" );
    var changeMonth = $( ".calendar" ).datepicker( "option", "changeMonth" );

    $( ".calendar" ).datepicker( "option", "changeMonth", true );
    $( ".calendar" ).datepicker( "option", "changeYear", true );
    $( ".calendar" ).datepicker( "option", "yearRange", "1920:2013" );

});

