<?php
global $PAGECONFIG ;
$PAGECONFIG = new stdClass();

$PAGECONFIG->theme = "th-default" ;

$PAGECONFIG->jsLoader = array( "loadHome" ) ;
$PAGECONFIG->conversion = array() ;
$PAGECONFIG->properties = array(
    "bodyId" => "home",
    "bodyClass" => ""
) ;

$PAGECONFIG->actions = array(
    "errorpages" => array(
        "jsLoader" => array( "loadValidator" ),
        "properties" => array(
            "bodyClass" => "error-body breakpoint-1024 pace-done"
        )
    ),
    "index" => array(
        'cssAddOns' => array(
            array( "css"=>"js/slick/slick.css", "media"=>"screen" )
        ),
        "loadJsInit" => false
    )
);

?>
