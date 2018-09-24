<?php
class appDatabase{
    
    function __construct( $autoConnect=false ){
        if( $autoConnect==true ){
            $this->connect();
        }
    } //end __construct function
    
    function connect(){
        global $APPVARS, $GLOBAL_CONFIG;
        $engine = $GLOBAL_CONFIG->dbEngine;
        $this->engine = $engine;
        
        //check if class exists
        if( !method_exists('appDatabase', $engine)){
            appServices::log("APPINIT: appDatabase::$engine does not exists.");
            die("APPINIT: appDatabase::$engine does not exists.");
        }
        
        //check whether the DB Configuration is set
        if( !isset($APPVARS->envConfig->$engine) ){
            appServices::log("APPINIT: envConfig::$engine does not exists.");
            die("APPINIT: envConfig::$engine does not exists.");
        }
        
        $this->$engine( $APPVARS->envConfig->$engine );
    } //end connect function
    
    private function mysql( $dbConfig ){
        global $DB, $APPVARS, $APPVARS;
        
        if( isset($APPVARS->initDB) && $APPVARS->initDB==true ){
            return;
        }
        
        $APPVARS->initDB = true ;
        
        //initialize the connection
        $DB = new mysql( true, $dbConfig['database'], $dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['charset'] );
       
//        $DB->SelectRows("user", 'simcoury@yahoo.com');
//        var_dump( $DB->RecordsArray() );
    } //end mysql function
    
}

?>
