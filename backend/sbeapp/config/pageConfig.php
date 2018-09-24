<?php

global $GLOBAL_PAGECONFIG, $GLOBAL_CONFIG;

$GLOBAL_PAGECONFIG = new stdClass();

$GLOBAL_PAGECONFIG->theme = "th-default";

$GLOBAL_PAGECONFIG->widgets = array();

$GLOBAL_PAGECONFIG->jsInitFile = "common/js/app-init.js";

$GLOBAL_PAGECONFIG->controllerPath = "common/js/controllers/"; //controller's paths (e.g. sg/js/ViewModels/)
$GLOBAL_PAGECONFIG->servicePath = "common/js/services/"; //controller's paths (e.g. sg/js/ViewModels/)
$GLOBAL_PAGECONFIG->customPath = "common/js/customs/"; //controller's paths (e.g. sg/js/ViewModels/)
//
//common JS, independent of the theme
$GLOBAL_PAGECONFIG->jsDefaults = array(
    'common/js/jQuery-2.1.4.min.js',
    'common/js/angular-1.2.27/angular.min.js',
    'common/js/angular-1.2.27/angular-route.min.js',
    'common/js/angular-1.2.27/angular-loader.js',
    'common/js/app-common.js'
   
);

//common CSS, independent of the theme
$GLOBAL_PAGECONFIG->cssDefaults = array(
  
    
);
?>
