<?php
class appUrl {
    
    function __construct(){
    }
    
    //return current url
    public static function get(){
        return @$_SERVER['REQUEST_URI'];
    }
    
    public static function getPrevUrl(){
        $prevUrl = @$_SERVER['HTTP_REFERER'];
        return $prevUrl!="" ? $prevUrl : @$_SESSION['previousUrl'] ;
    }
            
    //check the corresponding controller & action according to the url
    public static function getUrlRoutes( $url ){
        global $ROUTES ;
        
        if( isset( $ROUTES[$url] ) ){ //check "raw" url from routes
            return explode(".", $ROUTES[$url]) ;
        }
        
        //if not found, try to clean the url before comparing the routes
        $url = appUrl::cleanRoutesUrl( $url );
        if( isset( $ROUTES[$url] ) ){ //check "raw" url from routes
            return explode(".", $ROUTES[$url]) ;
        }
        
//        appLogs::logToFile( "ROUTES: Invalid request URL. Url: " . appUrl::get() );
        return false;
    }
    
    //parse the URL
    //returns corresponding controller & action
    public static function getControllerPath( $url="" ){
        //$cPath = $url!="" ? $url : str_replace('//','/',parse_url(appUrl::get(), PHP_URL_PATH)); //controller path
        $url = $url!="" ? $url : appUrl::get() ;
        $cPath = str_replace('//','/',parse_url( $url, PHP_URL_PATH)); //controller path
        $sfPath = basename(ROOT) ; //site folder path

        if( stripos($cPath, $sfPath)==true ){
            $cPath = str_ireplace('/'.$sfPath, '', $cPath);
        }
        return $cPath;
    }
    
    public static function getControllerAction( $url="" ){
        $controllerPath = appUrl::getControllerPath();
        
        $router = appUrl::getUrlRoutes($controllerPath);
        
        $arrayPaths = $router!=false ? $router : array_values(array_filter(explode("/", $controllerPath )));
        $action = isset($arrayPaths[1]) ? $arrayPaths[1] : "index" ;
        
        return array(
            "url" => $controllerPath,
            "controller" => @$arrayPaths[0], 
            "action" => $action,
            "view" => isset($arrayPaths[2]) ? $arrayPaths[2] : $action 
        );
    }
    
    //clean the url, removing / at the end of the url request
    //used only when checking with routes
    public static function cleanRoutesUrl( $url ){
        return substr($url, -1)=='/' ? substr($url, 0, -1) : $url ;
    }
    
    //check if the request is valid
    //return status code if error, boolean true if OK
    public static function isValidRequest(){
        $isValid = appUrl::valid( @$_SERVER['REQUEST_URI'] );
    }
    
    //return corresponding pageError accdg to the statusCode
    public static function getErrorRedirect( $status_code ){
        switch($status_code) {
            case '403':
                $redirectUrl = "forbidden" ;
                break;
            case '404':
                $redirectUrl = "page-not-found" ;
                break;
            case '500':
                $redirectUrl = "server-error" ;
                break;
            default:
                $redirectUrl = substr($status_code, 0, 1)=='/' ? substr($status_code, 1) : $status_code ;
                break;
        }
        return $redirectUrl ;
    }
    
    //check if the request is valid
    //analyze http header request
    //return status code if error, boolean true if OK
    public static function valid( $url, $redirectOnErr=true, $returnType="ERROR_CODE" ){
        
        $http_response_header = @get_headers( $url );
        
        if( !isset($http_response_header[0]) ){
            return false ;
        }
        
        list($version,$status_code,$msg) = explode(' ',$http_response_header[0], 3);
        
        if( $status_code!=200 && $redirectOnErr==true ){
            
        }else if( $status_code!=200 && $redirectOnErr==false ){
            return $returnType=="BOOLEAN" ? false : $status_code ;
        }

        return true ;
//        switch($status_code) {
//            case 200:
//                    $error_status="200: Success";
//                    break;
//            case 401:
//                    $error_status="401: Login failure.  Try logging out and back in.  Password are ONLY used when posting.";
//                    break;
//            case 400:
//                    $error_status="400: Invalid request.  You may have exceeded your rate limit.";
//                    break;
//            case 404:
//                    $error_status="404: Not found.  This shouldn't happen.  Please let me know what happened using the feedback link above.";
//                    break;
//            case 500:
//                    $error_status="500: Twitter servers replied with an error. Hopefully they'll be OK soon!";
//                    break;
//            case 502:
//                    $error_status="502: Twitter servers may be down or being upgraded. Hopefully they'll be OK soon!";
//                    break;
//            case 503:
//                    $error_status="503: Twitter service unavailable. Hopefully they'll be OK soon!";
//                    break;
//            default:
//                    $error_status="Undocumented error: " . $status_code;
//                    break;
//        }
        
    }
    
}

?>