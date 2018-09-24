<?php
class fileHandling{
    
    function __construct(){
    }
    
    public static function exists( $path ){
        return file_exists( $path );
    }
    
    public static function write($path, $contents, $mode='w'){
        //check whether this path is writable
        if( fileHandling::exists($path) ){
            if( !is_writable($path) ){
                if( !chmod($path, 0755) ){
                    error_log( $contents );
                    return;
                }
            }
        }
        
        $fh = fopen($path, $mode);
        if( $fh ){
            fwrite($fh, $contents);
            fclose($fh);
            chmod($path, 0600);
        }
    }
    
    function append($path, $contents){
        $fh = fopen($path, "a");
        fwrite($fh, $contents);
        fclose($fh);
    }
    
    public static function get( $path ){
        return fileHandling::exists($path)==true ? file_get_contents($path) : "" ;
    }
    
}

?>