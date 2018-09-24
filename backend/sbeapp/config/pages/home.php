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
);

$PAGECONFIG->properties = array(
//    "bodyId" => "sgHome",
//    "bodyClass" => "error-body no-top lazy"
);
$PAGECONFIG->jsLoader = array(
    "loadValidator",
    'th-default/js/login.js',
    "sg/js/custom/form-validation.js"
);

$PAGECONFIG->actions = array(
    "index" => array(//if set, will override the values set for the jsLoader
        "pageTitle" => "Welcome to SB"
    ),
    "login" => array(
        "pageTitle" => "Login",
        "jsLoader" => $PAGECONFIG->jsLoader,
        "uiComponents" => $PAGECONFIG->uiComponents
    ),
    "register" => array(
        "pageTitle" => "Login",
        "jsLoader" => $PAGECONFIG->jsLoader,
        "uiComponents" => $PAGECONFIG->uiComponents
    ),
    "forgot" => array(
        "pageTitle" => "Forgot Password",
        "jsLoader" => $PAGECONFIG->jsLoader,
        "uiComponents" => $PAGECONFIG->uiComponents
    ),
    "reset" => array(
        "pageTitle" => "Reset Password",
        "jsLoader" => $PAGECONFIG->jsLoader,
        "uiComponents" => $PAGECONFIG->uiComponents
    ),
);
?>
