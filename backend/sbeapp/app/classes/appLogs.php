<?php
class appLogs extends appSession{
    
    public $logFile ;
    
    function __construct(){
    }
    
    function getLogFile( $file='', $headr=" : System Logs\r\n" ){
        
        $this->logFile = !isset($this->logFile) ? date('Ymd') : $this->logFile ;
        if( $file!="" ){
            $this->logFile = $file ;
        }
        
        $this->logFilepath = LOGS.$this->logFile.".log" ;
        
        //check if the path exists
        if( fileHandling::exists($this->logFilepath)==false ){
            fileHandling::write( $this->logFilepath, $this->config->siteName.$headr );
        }
    }
    
    //will saved all the log details from the source code
    //type, message and data provided
    //$print = true, output the error in json format, terminates the whole process
    function log($message,$type='error',$print=false){
        
        if( !isset($this->config) ){
            global $GLOBAL_CONFIG;
            $this->config = $GLOBAL_CONFIG ;
        }
        
        $dateNow = date('Y-M-d H:i:s e');
        $error = "[$dateNow] " . $this->config->logPrefix ."/". strtoupper($type) . " : " . $message . "\n" ;
        if( $this->config->logMode=="default" ){
            return error_log( $error );
        }
            
        //for custom logging
        $this->getLogFile();
        
        fileHandling::write( $this->logFilepath, $error, 'a' );
//        error_log( $error, 3, $this->logFilepath );
//        if( !error_log( $error, 3, $this->logFilepath ) ){
//            $this->appendLog( $error, $this->logFilepath );
//        }
        
        if( $print==true ){
            app::addOutput('status', $type);
            app::addOutput('message', $message);
            app::printOutput();
        }
    }

        function appendLog( $message, $file ){
            fileHandling::append( $file, $message );
        }
        
    public static function logToFile( $message, $type='error', $print=false ){
        $appLogs = new appLogs ;
        $appLogs->log($message,$type,$print);
    }
    
    function saveToDB( $membername, $msg="", $category="", $subcategory="", $data=array(), $reftype="admin" ){
        
        global $GLOBAL_CONFIG ;
        if( $GLOBAL_CONFIG->auditLog==false ){
            return;
        }
        global $DB;
        $post = array(
            "username" => $membername, 
            "message" => $msg, 
            "category" => $category, 
            "subcategory" => $subcategory, 
            "reftype" => $reftype, 
            "date_created" => date('Y-m-d H:i:s')
        );
  
        $document = $DB->insert("INSERT INTO auditlog SET " . parseArrayToFields($post));
       
        
//        $document = $db->insert('auditlog', $post);
        
        //save this log info to file too
        if( $GLOBAL_CONFIG->auditLogToFile==true ){
            appServices::log('AuditLog: '.$category.'/'.$subcategory.'/'.$membername.' : '.$msg, 'info');
        }
        
        return $document ;
    }
    
    //log the compile summary to log file
    function compileLogger($message,$APPCOMPILER='lessCompiler'){
        if( !isset($this->config) ){
            global $GLOBAL_CONFIG;
            $this->config = $GLOBAL_CONFIG ;
        }
        
        $dateNow = date('Y-M-d H:i:s e');
        $error = "[$dateNow] $APPCOMPILER " . $message . "\r\n-----------------\r\n" ;
             
        //for custom logging
        $this->getLogFile( date('Ymd')."_cs", " : Compiler Logs \r\n-----------------\r\n");
        fileHandling::write( $this->logFilepath, $error, 'a' );
//        error_log( $error, 3, $this->logFilepath );
//        if( !error_log( $error, 3, $this->logFilepath ) ){
//            $this->appendLog( $error, $this->logFilepath );
//        }
        
    }
    
}

?>
