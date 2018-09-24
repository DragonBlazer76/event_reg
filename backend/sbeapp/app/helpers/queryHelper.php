<?php
function parseArrayToFields($array, $separator=",") {
    $parsed = array();
    foreach($array as $key => $value) {
        array_push($parsed, "`$key`='$value'");
    }
    return implode($separator, $parsed);
}
?>