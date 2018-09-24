<?php

class sgPolicy {

    function __construct() {
        global $POLICY, $APPVARS;

        $this->policy = $POLICY;
        $this->appSession = new appSession;

        //echo $current_uri = parse_url( appUrl::get(), PHP_URL_PATH );
        $httpRefUrl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        $_SESSION['previousUrl'] = $httpRefUrl;

        //check whether the 
        if( isset($_GET['sgprurl']) ){
            global $GLOBAL_CONFIG;
            $qs = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
            
            //redirect so that the session set will take effect
            parse_str($qs, $qsArr);
            if( isset($qsArr['sgprurl']) )
                unset($qsArr['sgprurl']);
            $qs = http_build_query($qsArr);
            
//            $redirect = $GLOBAL_CONFIG->siteUrl.parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '?' . $qs;
            $redirect = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . '?' . $qs;
            header("Location: $redirect");
            exit;
        }
        
        $this->apply();
    }

    function apply() {

        //check the global URL exceptions (always allowed URLs)
        //if found in $POLICY->allowedExceptions, will just continue
        //and will not proceed for further checking
        if ($this->checkExceptions([], true)) {
            return;
        }

        $auth = $this->appSession->get('authenticated');

        //check whether there is an existing session value
        if (gettype($auth) == "string" && $auth == "") {
            $this->appSession->setValue('authenticated', false);

            //regenerate session id
            //$this->appSession->setValue( 'sessionId', SESSION_ID );
            $auth = false;
        }

        //if not authenticated, user will only allowed at the following pages
        $authStatus = userServices::checkFullAuthentication(false);

        $criteria = strtolower(trim($authStatus));

        //if policy criteria is not set, just continue
        if (!isset($this->policy->$criteria)) {
            return;
        }

        //get the policies based on the criteria
        $policy = $this->policy->$criteria;

        //get the redirecting url based on the authentication status
        $redirect = $this->getRedirectUrl($authStatus, $policy);

        //Validation #1: Check current URL in the list of ALLOWED url
        if( isset($policy['allowedUrls']) && count($policy['allowedUrls'])>0 ){
            $isInAllowedUrl = $this->checkExceptions($policy['allowedUrls']);
            //if in allowed, straight away just continue, no more checking for another exception
            if( $isInAllowedUrl )
                return;
        }
        
        //Validation #2: Check current URL is in the list of NOT ALLOWED urls
        if( isset($policy['exceptions']) && count($policy['exceptions'])>0 ){
            //check whether the current URL is in the list of NOT ALLOWED url
            //returns true if found in the not allowed url
            $isNotAllowedUrl = $this->checkExceptions( $policy['exceptions'] );
            if( $isNotAllowedUrl ) {    //controller is allowed, but current URL is not allowed
                $this->processRedirect($policy, $redirect);
            }
        }
        
        //Validation #3: Check current executing controller is allowed in the ALLOWED controllers list
        $isInController = $this->checkController($policy['controllers']);
        if( !$isInController ){
            $this->processRedirect($policy, $redirect);
        }
        
        return;
    }

    function processRedirect($policy, $redirect) {
        $qs = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
        
        //will saved this requesting url to pass in the redirecting url
        if (isset($policy['passRedirectURL']) && @$policy['passRedirectURL'] == true) {
            $passRedirectURL = base64_encode(appUrl::get());
            @$_SESSION['passRedirectURL'] = $passRedirectURL;
                //                parse_str($qs, $qsArr);
                //                $qsArr['prappUrl'] = $passRedirectURL;
                //                $qs = http_build_query($qsArr);
            $qs .= '&sgprurl=1';
        }
        appServices::redirect($redirect . '?' . $qs, false);
    }

    //check whether the currently executing controller is allowed to be called or execute
    //return boolean true if allowed, false not allowed
    //for false, need to redirect to its corresponding page.
    function checkController($arr) {
        if (count($arr) == 0)
            return true;

        global $APPVARS;
        return in_array($APPVARS->controllerName, $arr);
    }

    //check whether the current request url is in the exception list
    //this exception list contains the list of all the url (from routes.php) that is 
    //if found in allowedUrls (list of all allowed urls), will just continue
    //if found in exceptions (not allowed urls), will proceed to redirect
    //returns boolean, if false redirection is needed
    function checkExceptions( $urlList=[], $global = false) {
        global $APPVARS;

        //check whether in always allowed exception
        //this mostly contain a system based or those pre-degined system URIs
        //like page errors (eg. 403, 404, 500)
      
        if( $global==true && count($this->policy->allowedExceptions) > 0) {
            //if found, ignore and continue to display the page 
            if (in_array($APPVARS->requestUrl, $this->policy->allowedExceptions))
                return true;
        }

        //check whether this url is not empty
        //if empty means return true, consider as allowed url
        if ( count($urlList) == 0){
            return $urlList;
        }
            
        //check whether the url is found in urlList
        //returns true if found, else false
        return in_array($APPVARS->requestUrl, $urlList);
    }

    function getRedirectUrl($authStatus, $policy) {
        $url = '/';
        if (isset($policy['redirect'])) {
            return trim($policy['redirect']);
        }

        return "forbidden";
//        if( $authStatus=="NOT_AUTHENTICATED" || $authStatus=="USER_NOT_VERIFIED" ){
//        }else if( $authStatus=="USER_IS_BLOCKED" ){
//        }else if( $authStatus=="USER_NOT_ACTIVE" ){
//        }else{  //USER_AUTHENTICATED    
//        }
    }

}

?>