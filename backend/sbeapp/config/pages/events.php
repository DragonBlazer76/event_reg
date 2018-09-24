<?php
global $PAGECONFIG;
$PAGECONFIG = new stdClass();

$PAGECONFIG->pageTitle = "Events Manager";


$PAGECONFIG->actions = array(
    "index" => array(   //if set, will override the values set for the jsLoader
        "pageTitle" => "Events List",
         "uiComponents" => array("dataTables")
    ),
    "form" => array(   //if set, will override the values set for the jsLoader
//        "pageTitle" => "Events"
        "uiComponents" => array("dateRangePicker")
    ),
    "auditlog"=> array(   //if set, will override the values set for the jsLoader
         "pageTitle" => "AuditLog List",
        "uiComponents" => array("dateRangePicker")
    )
);
?>