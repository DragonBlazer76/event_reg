<?php

class appAuth extends app {

    function __construct() {
        global $GLOBAL_CONFIG;
        $this->config = $GLOBAL_CONFIG;
    }

    static function isAuthenticated(){
        return false;
    }
    
    static function getAuthenticationCode(){
        if( userServices::isAuthenticated() ){
            return "USER_AUTHENTICATED";
        }
        return "NOT_AUTHENTICATED";
    }
    
}

?>
