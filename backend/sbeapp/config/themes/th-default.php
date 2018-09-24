<?php
global $THEMECONFIG ;
$THEMECONFIG = new stdClass();

$themeFolder = basename(__FILE__, ".php") ;
    
$THEMECONFIG->css = array(
    "$themeFolder/bootstrap/css/bootstrap.min.css",
    "$themeFolder/css/AdminLTE.min.css",
    "$themeFolder/plugins/iCheck/square/blue.css"
) ;
        
$cssAddOnsAuthenticated = array(
    "https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css",
    "$themeFolder/css/skins/_all-skins.min.css"
);

$THEMECONFIG->cssAddOns = array(
    "USER_AUTHENTICATED" => $cssAddOnsAuthenticated
) ;
        
$THEMECONFIG->js = array(
    "/th-default/bootstrap/js/bootstrap.min.js"
) ;

$jsAddOnsAuthenticated = array(   //NOT_AUTHENTICATED
    "$themeFolder/plugins/fastclick/fastclick.min.js",
    "$themeFolder/js/app.min.js",
    "$themeFolder/plugins/sparkline/jquery.sparkline.min.js",
    "$themeFolder/plugins/slimScroll/jquery.slimscroll.min.js",
    "$themeFolder/js/ui.common.js"
);

$THEMECONFIG->jsAddOns = array(
    "USER_AUTHENTICATED" => $jsAddOnsAuthenticated
);

?>
