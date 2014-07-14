<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of git
 *
 * @author juanb
 */
class git {

    //put your code here
    public function getBranchName() {
        if (is_file('.git/HEAD')) {
            $stringfromfile = file('.git/HEAD', FILE_USE_INCLUDE_PATH);

            $stringfromfile = $stringfromfile[0]; //get the string from the array

            $explodedstring = explode("/", $stringfromfile); //seperate out by the "/" in the string

            return trim(end($explodedstring)); //get the one that is always the branch name
        }
        return false;
    }

}
