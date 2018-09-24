<?php

global $PAGECONFIG;
$PAGECONFIG = new stdClass();

$PAGECONFIG->pageTitle = "DashBoard";

$PAGECONFIG->js = array(
);
$PAGECONFIG->css = array(
);

//ui components common for this controller
//for action-specific uiComponents, used ->actions array
$PAGECONFIG->uiComponents = array(
    'formValidator',
    '/plugins/bootstrap-fileinput/jasny-bootstrap.min.css',
  
  
);

$PAGECONFIG->properties = array(
//    "bodyId" => "sgHome",
//    "bodyClass" => "error-body no-top lazy"
);
$PAGECONFIG->jsLoader = array(
    "loadValidator",
    "sg/js/custom/form-validation.js",
    'th-default/plugins/Chart.js/Chart.min.js',
    'th-default/plugins/jquery.sparkline/jquery.sparkline.min.js',
    'th-default/js/index.js',
    'th-default/js/main.js',
);

$PAGECONFIG->actions = array(
    "index" => array(//if set, will override the values set for the jsLoader
        "pageTitle" => "dashboard",
        "jsLoader" => $PAGECONFIG->jsLoader,
        "uiComponents" => $PAGECONFIG->uiComponents
    )
);
?>