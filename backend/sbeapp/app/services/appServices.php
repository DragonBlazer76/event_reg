<?php

class appServices {

    function __construct() {
        
    }

    public static function getAfterLoginRedirect( $userCat='' ) {
        if( isset($_SESSION['passRedirectURL']) && $_SESSION['passRedirectURL']!="" ){
            $redirect = base64_decode($_SESSION['passRedirectURL']);
            $_SESSION['passRedirectURL'] = '';
            unset($_SESSION['passRedirectURL']);
            return $redirect;
        }
        return strtolower($userCat)=="hirer" ? "user" : "newsfeed";
    }
    
    public static function isPostMethod() {
        return getRequestMethod()=="post";
    }
    public static function toRating($r='', $maxRate=5){
        
        if( $r=="" ){
            return $r;
        }
    
        $intRating = intval($r);
        $rating = ceil($r*10)/10;
        $toRating = "" ;    

        $isFloat = is_float($r);
        $floatVal = $isFloat==true ? (abs($intRating)-floor(abs($intRating))) : 0;
        
        if( $intRating==0 ){
            $toRating = "zero" ;
        }else if( $intRating==1 ){
            $toRating = "one" ;
        }else if( $intRating==2 ){
            $toRating = "two" ;
        }else if( $intRating==3 ){
            $toRating = "three" ;
        }else if( $intRating==4 ){
            $toRating = "four" ;
        }else if( $intRating==5 ){        
            $toRating = "five" ;
        }
    
        if( $isFloat==true && $floatVal>=5 ){
            $toRating = $toRating==="zero" ? "half" : $toRating."-half" ;
        }

        return $toRating ;
    }
    
    public static function getFlashMessageVar() {
        return 'flashmsg' ;
    }
    public static function aesEncrypt($plain_text){
       global $GLOBAL_CONFIG ;
       $secret =$GLOBAL_CONFIG->passPhrase;
       $iv = substr($secret, 0, 16);
       $encryptedMessage = openssl_encrypt($plain_text, 'AES-256-CBC', $secret,0,$iv);
       return $encryptedMessage;
       
    }
    public static function aesDecrypt($encryptedMessage){
       global $GLOBAL_CONFIG ;
       $secret =$GLOBAL_CONFIG->passPhrase;
       $iv = substr($secret, 0, 16);  
       $decryptedMessage = openssl_decrypt($encryptedMessage, 'AES-256-CBC', $secret,0,$iv);
       return $decryptedMessage;
  
    }
    
    //session based adding of flash messages
    public static function addFlashMessage( $message, $type="info", $log=false ) {
        global $APPVARS ;
        $varname = appServices::getFlashMessageVar() ;
        
        $arrMessages = isset($_SESSION[$varname]) ? $_SESSION[$varname] : array() ;
        $arrMessagesByType = count($arrMessages)>0 && isset($arrMessages[$type]) ? $arrMessages[$type] : array() ;
        
        $cssClass = $type=="error" ? "warning" : $type ;
        
        $message = array(
            "message" => $message,
            "cssClass" => $cssClass
        );
            
        array_push($arrMessagesByType, $message);
        $arrMessages[$type] = $arrMessagesByType ;
        
//        $cssClass = $type ;
//        if( $type=="error" ){
//            $cssClass = "warning" ;
//        }
//        
//        $arrMessages = array(
//            "type" => $type,
//            "message" => $message,
//            "cssClass" => $cssClass,
//            "iconClass" => $iconClass
//        );
        
        $_SESSION[$varname] = $arrMessages ;
        $APPVARS->$varname = $arrMessages ;
    }
    
    public static function getFlashMessage( $clr=true ) {
        global $APPVARS ;
        $varname = appServices::getFlashMessageVar() ;
        $flashMsgs = isset($_SESSION[$varname]) ? $_SESSION[$varname] : [] ;
        if(  $clr==true )
            appSession::clear($varname);
        if( count($flashMsgs)>0 ){
            return $flashMsgs ;
        }else if( count($flashMsgs)==0 && isset($APPVARS->$varname) ){
            return $APPVARS->$varname ;
        }
        return [] ;
    }
    
    //set the cookie browser
    //accepts name, value, and the number of days to expire (optional)
    public static function setCookie( $name, $value, $expDays=365 ) {
        $expDays = time() + 60 * 60 * 24 * $expDays ;
        setcookie( $name, $value, $expDays, "/" ) ;
    }
    
    //return the cookie value
    //if didnt pass any parameter, will return all cookies as an array
    public static function getCookie( $name="all" ) {
        $cookies = isset($_COOKIE) ? $_COOKIE : [];
        if( $name=="all" || count($cookies)==0 ){
            return $cookies;
        }
        return isset($cookies[$name]) ? trim($cookies[$name]) : "";
    }
    
    //delete a cookie
    //if didnt pass any parameter, will delete all cookies from the browser
    public static function deleteCookie( $name="all" ) {
        if( $name=="all" ){
            foreach ($_COOKIE as $name => $value) {
                setcookie($name, '', 1);
            }
        }else{
            setcookie($name, '', 1);
        }
    }
    
    public static function redirect( $redirectUrl, $useapp=true ) {
        if( $useapp==true ){
            global $app ;
            if( method_exists($app, 'redirect') ){
                $app->redirect( $redirectUrl );
            }
            exit;
        }
        
        global $GLOBAL_CONFIG ;
        $siteUrl = substr($GLOBAL_CONFIG->siteUrl, -1)=='/' ? $GLOBAL_CONFIG->siteUrl : $GLOBAL_CONFIG->siteUrl.'/' ;
        
        header('Location: ' . $siteUrl.$redirectUrl );
        die();
    }
    
    public static function log($message, $type = 'error') {
        $appLogs = new appLogs();
        $appLogs->log($message, $type);
    }

    public static function auditLog($membername, $msg, $category, $subcategory = "", $reftype = "memberid") {
        $appLogs = new appLogs();
        $appLogs->saveToDB($membername, $msg, $category, $subcategory,$reftype);
    }

    //To format date in different formats
    public static function formatDate($date, $isToString=false, $prefix = false) {
        if(is_null($date)==true ){ return ''; }
        
        $dateType = "" ;
        $newDate = "" ;

        //check the date type
        if( gettype($date)=="object" ){
            $dateType = "mongo" ;
//            $newDate = $date->sec ;    
            $newDate = date("Y-m-d H:i:s", $date->sec) ;
        }else if( strpos($date, "T")>0 ){    //date is in ISO format
            $newDate = date("Y-m-d H:i:s", strtotime($date)) ;
            $dateType = "iso" ;
        }else{            
            $newDate = $date ;
        }
        
        
//        if (gettype($isToString)=="string" && $isToString != "" && $prefix == true) {
//            return $isToString . $fd;
//        } else if (gettype($isToString)=="string" && $isToString != "" && $prefix == false) {
//            return $fd . $isToString;
//        } else 
//        if( gettype($isToString)=="boolean" ) {
//            $isToString = "" ;
//        }
  
       
        if( $isToString=="parse" ){
            $newDate = explode(" ", $newDate);
            return $newDate[0];  //return Y-m-d format
        }else if( $isToString=="parse-to-date-time" ){
            return date('YmdHis', strtotime($newDate));
        }else if( substr($isToString,0,6)=="custom" ){        
            list(,$format) = explode('||',$isToString);
            return date($format, strtotime($newDate));
        }else if( $isToString=="parse-to-date" ){    
            return date('Ymd', strtotime($newDate));
        }else if( $isToString=="format-to-time" ){    
            return date('H:i a', strtotime($newDate));
        }else if( $isToString=="format-date" ){
            return date('d M Y', strtotime($newDate));
        }else if( $isToString=="format-date-noyear" ){
            return $dateType=="iso" ?date('d M', strtotime($newDate)) : date('d M', strtotime($newDate)) ;
        }else if( $isToString=="format-complete-date"  ){            
            return date('D, d M Y', strtotime($newDate));
        }else if( $isToString=="format-complete-date-time"  ){
            return date('D, d M Y',  strtotime($newDate)).' at '.date('H:i a', strtotime($newDate));
        }else if( $isToString=="format-complete-stringdate"  ){ 
             return date('d/M/Y', strtotime($newDate));
        }
        
        $formattedDate = getTimeElapsed( strtotime($newDate) );
        
        if( gettype($prefix)=="string" ){
            return $prefix.' '.$formattedDate ;
        }else if( gettype($prefix)=="boolean" && $prefix==true && $isToString!="" ){
            return $isToString.' '.$formattedDate ;
        }
        return $formattedDate ;
    } //formatDate 

    public static function convert($size, $format = 'MB') {
        $divr = " ";

        if ($size === 1000000000) {
            return array($value => 1, $format => "GB");
        }
        $divr = 1024; //to mb
        $value = Math . round(($size / $divr) * 100) / 100;

        if ($value > 1000) {
            $value = Math . round(($value / $divr) * 100) / 100;
            $format = "MB";
        }
        return array($value => value, $format => $format);
    } //convert

    
    public static function truncate( $str, $len=30 ){
        return strlen($str)<=$len ? $str : substr($str, 0, $len)."...";
    } //end truncate function
    
    public static function toBoolean ( $v ) {
        
        if( empty($v) || count(@$v) === 0 || gettype($v) === "boolean" ){ 
            return $v;
        }
        
        return $v === "true" ? true : false;
        
    } //end toBoolean function 
    
    public static function profileStatusCriteria() {
        $criteria = array(
            "emailVerification" => array(
                "percentage" => 10,
                "validation" => "auto",
                "remarks" => "Auto"
            ),
            "portfolio" => array(
                "percentage" => 30,
                "validation" => 1,
                "remarks" => "To receive 30% , minimum one complete portfolio have to post."
            ),
            "profilePhoto" => array(
                "percentage" => 5,
                "validation" => 1,
                "remarks" => "5% receive after upload profile photo"
            ),
            "aboutMe" => array(
                "percentage" => 10,
                "validation" => "check-minimum-char",
                "validation_min_1" => array("min" => 100, "value" => 10),
                "validation_min_2" => array("min" => 150, "value" => 10),
                //"remarks" : "100 valid characters receive 5%, 150 valid characters and above receive 10%"
                "remarks" => "Share a short description of yourself with the community in at least 100 characters."
            ),
            "resume" => array(
                "percentage" => 5,
                "validation" => "check-minimum-char",
                "validation_min_1" => array("min" => 150, "value" => 5),
                "remarks" => "To receive 5%, resume should have minimum 150 valid characters"
            ),
            "testimonials" => array(
                "percentage" => 5,
                "validation" => 1,
                "remarks" => "To receive 5%, minimum one testimonial from any source"
            ),
            "skills" => array(
                "percentage" => 15,
                "validation" => "check-minimum-count",
                "validation_min_1" => array("min" => 2, "value" => 15),
                "validation_min_2" => array("min" => 4, "value" => 15),
                "validation_min_3" => array("min" => 6, "value" => 15),
                "remarks" => ""
            //"remarks" : "5% for 2, 10% for 4, 15% for 6 and above"
            ),
            "personalID" => array(
                "percentage" => 10,
                "validation" => "check-minimum-field;check-receive-payment",
                "validation_min_1" => array("min" => 1, "value" => 5),
                "validation_min_2" => array("min" => 10, "value" => 10),
                "remarks" => "5% receive upon enter the at least one field, 10% receive after first payment received or paid"
            ),
            "bankDetails" => array(
                "percentage" => 5,
                "validation" => 1, //"check-receive-payment",
                "remarks" => "5% receive after first payment receive or paid"
            ),
            "address" => array(
                "percentage" => 5,
                "validation" => 1, //"check-receive-payment",
                "remarks" => "5% receive after first payment receive or paid"
            )
        );
        return $criteria;
    } //profileStatusCriteria
    
}

?>