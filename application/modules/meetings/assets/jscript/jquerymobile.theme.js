/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).bind("mobileinit", function () {

    // Navigation
    $.mobile.page.prototype.options.backBtnTheme    = "a";

    // Page
    $.mobile.page.prototype.options.headerTheme = "a";  // Page header only
    $.mobile.page.prototype.options.contentTheme    = "a";
    $.mobile.page.prototype.options.footerTheme = "a";

    // Listviews
    $.mobile.listview.prototype.options.headerTheme = "a";  // Header for nested lists
    $.mobile.listview.prototype.options.theme           = "a";  // List items / content
    $.mobile.listview.prototype.options.dividerTheme    = "a";  // List divider

    $.mobile.listview.prototype.options.splitTheme   = "a";
    $.mobile.listview.prototype.options.countTheme   = "a";
    $.mobile.listview.prototype.options.filterTheme = "a";
    
});

