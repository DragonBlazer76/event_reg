<?php
global $POLICY ;
  
$POLICY = new stdClass();

$POLICY->allowedExceptions = array( //always allowed
    "web/geteventdetails", "/page-not-found", "/server-error", "web/setgueststatus", "/forgot", "/logout"
);

$POLICY->not_authenticated = array(
    "controllers" => array("home", "sp" , "reqs","web"),           //allowed controllers
    "exceptions" => array(              //not allowed
        "/logout",
    ),
    "redirect" => "login",
    "passRedirectURL" => true
);

//user is authenticated
$POLICY->user_authenticated = array(
    "controllers" => array(),           //if empty, all controllers will be allowed
    "exceptions" => array(              //not allowed
        "/register", "/login", "/verify", "/forgot", 
        "/reset", "/remember", "/resend","web/geteventdetails","web/setgueststatus"
    ),
    "redirect" => 'guests'
);

//authenticated but account is not verified yet
$POLICY->user_not_verified = $POLICY->not_authenticated ;

?>
