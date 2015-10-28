<?php

/**
 * output JSON object to browser
 */
function output_json($data = array()) {
    // check whether is an array or object
    $data = (is_array($data) or is_object($data)) ? $data : array();
    header('Content-type: application/json;charset=UTF-8');
    echo json_encode($data);
}

/* GET date with 28 days least */

function get_date_least_28_days() {
    $my_date = date('Y-m-j');
    $new_date = strtotime('-28 day', strtotime($my_date));
    $new_date = date('Y-m-j', $new_date);

    return $new_date;
}

/* GET date with 730 days least */
function get_date_least_2_years() {
    $my_date = date('Y-m-j');
    $new_date = strtotime('-730 day', strtotime($my_date));
    $new_date = date('Y-m-j', $new_date);

    return $new_date;
}
