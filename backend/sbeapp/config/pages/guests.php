<?php
global $PAGECONFIG;
$PAGECONFIG = new stdClass();

$PAGECONFIG->pageTitle = "Guests List";

$PAGECONFIG->actions = array(
    "index" => array(//if set, will override the values set for the jsLoader
        "pageTitle" => "Guests List",
        "uiComponents" => array("dataTables")
    )
);
?>