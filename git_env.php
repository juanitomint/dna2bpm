<?php
/**
 * @author Juan Ignacio Borda <juanignacioborda@gmail.com>
 * @date Jun 15, 2015
 * 
 * ---------------------------------------------------------------
 * Git Enviroments & configs
 * ---------------------------------------------------------------
 *
 * Different environments will load different config files
 * 
 */
$branch='';
if (is_file('.git/HEAD')) {
    $stringfromfile = file('.git/HEAD', FILE_USE_INCLUDE_PATH);

    $stringfromfile = $stringfromfile[0]; //get the string from the array

    $explodedstring = explode("/", $stringfromfile); //seperate out by the "/" in the string

    $branch='_'.trim(end($explodedstring));
}

if(isset($_SERVER["HTTP_HOST"])){
    $_SERVER['CI_ENV']= $_SERVER["HTTP_HOST"].$branch;
}

/**
 * Custom error and 404 functions for debug
 * 
 */ 

// function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered'){
// xdebug_print_function_stack();
// exit(1);    
// }
// function show_404($url){
//     echo "$url<hr/>";
//     xdebug_print_function_stack();
// exit(1);    
// }