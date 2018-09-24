<?php

global $PAGECONFIG;
$PAGECONFIG = new stdClass();

$PAGECONFIG->pageTitle = "Home";

$PAGECONFIG->js = array(
);
$PAGECONFIG->css = array(
    
);

//ui components common for this controller
//for action-specific uiComponents, used ->actions array
$PAGECONFIG->uiComponents = array(
    'formValidator',
      '/plugins/bootstrap-fileinput/jasny-bootstrap.min.css'

);

$PAGECONFIG->properties = array(
//    "bodyId" => "sgHome",
//    "bodyClass" => "error-body no-top lazy"
);
$PAGECONFIG->jsLoader = array(
    "loadValidator",
    "sg/js/custom/form-validation.js",
    'th-default/plugins/bootstrap-fileinput/jasny-bootstrap.js',
);

$PAGECONFIG->actions = array(
    "index" => array(//if set, will override the values set for the jsLoader
        "pageTitle" => "user",
        "jsLoader" => $PAGECONFIG->jsLoader,
        "uiComponents" => $PAGECONFIG->uiComponents
    )
);
?>
