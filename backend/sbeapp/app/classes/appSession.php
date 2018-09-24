<?php
class appSession{
    
    function __construct(){
    }
    
    function setValue( $varname, $value){
         
        @$_SESSION[ $varname ] = $value ;
       
       
    }
    
    public function set( $varname, $value){
        $this->setValue( $varname, $value );
    }
    
    public function user( $validation ){
        if( gettype($validation)=="object" ){
            $validation = (array) $validation;
        }
        if(isset($validation['sessionId'])){$this->setValue('sessionId', $validation['sessionId'] );}
        if(isset($validation['id'])){$this->setValue('mid', $validation['id'] );}
        if(isset($validation['authenticated'])){$this->setValue('authenticated', $validation['authenticated'] );}
        if(isset($validation['checkPassword'])){$this->setValue('checkPassword', $validation['checkPassword'] );}
        if(isset($validation['isVerified'])){$this->setValue('isVerified', $validation['isVerified'] );}
    }

    public function get( $varname ){
        return !isset($_SESSION[$varname]) ? "" : @$_SESSION[$varname] ;
    }
    
    public function delete( $varname ){
        if( isset($_SESSION[$varname]) ){
            @$_SESSION[$varname] = '' ;
            unset($_SESSION[$varname]);
        }
    }
    
    public static function clear( $varname ){
        if( isset($_SESSION[$varname]) ){
            @$_SESSION[$varname] = '' ;
            unset($_SESSION[$varname]);
        }
    }
    
    function destroy(){
        if( count(@$_SESSION)>0 ){
            foreach( $_SESSION as $varname=>$value ){
                unset($_SESSION[$varname]);
            }
        }
        session_destroy();
    }
    
    function clearLogout(){
        $this->redirect("/");
    }
    
    function userLogout(){
        global $GLOBAL_CONFIG;
        
        $params = array();
        $sessionHandler = $GLOBAL_CONFIG->sessionHandler=='' ? true : false;
        
        if( $sessionHandler=="MongoSession" ){
            $mongoDb = new mongoDb(false);
            $params = $mongoDb->getConnParams();
            $params['cookie_domain'] = parse_url( $GLOBAL_CONFIG->siteUrl, PHP_URL_HOST);
            
            $mngSessionId = MongoSession::getSessionId();
        }
        
        $this->destroy();
        
        if( $sessionHandler=="" ){
            //redirect to home page
            $this->redirect("/logoutclr");
        }
        
        if (isset($_COOKIE['PHPSESSID'])) {
            unset($_COOKIE['PHPSESSID']);
            setcookie('PHPSESSID', null, -1, @$params['cookie_domain']);
        }
    
        if( $mngSessionId!="" ){
            global $APPVARS;
            MongoSession::getSessionDestroy($mngSessionId);
            
            //delete data from the model
            $APPVARS->mongodb->delete( 'sessions' , array( '_id' => $mngSessionId ));
        }
        
        //redirect to home page
        $this->redirect("/logoutclr");
    }
    
}

?>
