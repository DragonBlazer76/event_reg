<?php

function setNoCache(){
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
}

function isActiveMenu( $criteria='', $printActive=true, $css='active' ){
    if( $criteria=='' ){ return false; }
    global $APPVARS ;
    if( $printActive==false )
        return $APPVARS->activeMenu==$criteria;
    echo $APPVARS->activeMenu==$criteria ? $css : '' ;
}

function getRequestMethod(){
    return trim(strtolower($_SERVER['REQUEST_METHOD']));
}

function isAjaxRequest(){
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        return true;
    }
    return false;
}

function isCompleteUrl( $url ){
    //$arrayPaths = array('http://','http://','http://');
    if( stripos($url, '://')!==false )
        return true;
    return false;
}

function cleanUrl( $url, $siteUrl=true ){
    $url = substr($url, 0, 1)=='/' ? substr($url, 1) : $url ; 
    //check whether http: is exists already in the site url
    if( $siteUrl==true && substr($url, 0, 4)!='http' ){
        global $GLOBAL_CONFIG;
        $url = $GLOBAL_CONFIG->siteUrl . $url ;
    }
    return $url;
}

function safeUrl( $s, $encode=true ){
    if( $s=="" ){ return $s; }
    if( $encode )
        return htmlentities(urlencode($s));
    return urldecode($s);
    //return htmlentities((urldecode($s)));
}

function randomValueBase64($len=0,$email='') {
    
//    global $GLOBAL_CONFIG;
//    $len = is_numeric($len)==true && $len>0 ? $len : $GLOBAL_CONFIG->codeLength ;
//    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
//    $randomString = '';
//    for ($i = 0; $i < $len; $i++) {
//        $randomString .= $characters[rand(0, strlen($characters) - 1)];
//    }
//    return $randomString;
    $random = "";

    //srand((double)microtime()*1000000);
    $data = "AbcDE123IJKLMN67QRSTUVWXYZ";
    $data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
    $data .= "0FGH45OP89";
    if ($email) {
        $data .=$email;
    } else {
        $email = "";
    } $data = base64_encode($data);
    for ($i = 0; $i < $len; $i++) {
        $random .= substr($data, (rand() % (strlen($data))), 1);
    }
    return $random;
}

function reArrayFiles(&$file_post) {
    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);
    for ($i = 0; $i < $file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }
    return $file_ary;
}

function fileExt($contentType) {
    $map = array(
        'application/pdf' => 'pdf',
        'application/zip' => 'zip',
        'application/rar' => 'rar',
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'text/css' => 'css',
        'text/html' => 'html',
        'text/javascript' => 'js',
        'text/plain' => 'txt',
        'text/xml' => 'xml',
        'text/csv' => 'csv',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.ms-powerpoint' => 'pot',
        'application/vnd.ms-powerpoint' => 'pps',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx'
    );
    if (isset($map[$contentType])) {
        return $map[$contentType];
    }
}

function fileImgExt($contentType) {
    $map = array(
        'image/gif' => 'gif',
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/png' => 'png',
        'image/bmp' => 'bmp',
        'image/psd' => 'psd',
    );
    if (isset($map[$contentType])) {
        return $map[$contentType];
    }
}

function isImage($contentType) {
    $isImage = false;
    $map = array('gif', 'jpeg', 'jpg', 'png', 'bmp', 'psd');
    if (in_array(strtolower($contentType), $map)) {
        $isImage = true;
    }
    return $isImage;
}

function isResumeType($contentType) {

    $contentType = fileExt($contentType);
    $isDoc = false;

    $map = array('pdf','doc', 'docx', 'odt');

    if (in_array(strtolower($contentType), $map)) {
        $isDoc = true;
    }
    return $isDoc;
}

function isDocument($contentType, $type) {
    $isDoc = false;
    $contentType = strtolower($contentType);
    $map = array('pdf', 'zip', 'rar', 'gif', 'jpg', 'png', 'css', 'html', 'js', 'txt', 'xml', 'csv', 'ppt', 'pot', 'pps', 'doc', 'docx');
    if ($type === 'portfolio') {
        $isDoc = isImage($contentType);
    } else {
        if (in_array(strtolower($contentType), $map)) {
            $isDoc = true;
        }
    }
    return $isDoc;
}

function iconTypes($contentType) {
    $icon = "";
    $arrFilTypes = array("txt", "pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "zip", "tar", "gzip", "gz",
        "html", "htm", "xml", "sgml", "mpeg", "mpg", "mp4", "webm", "avi", "mov", "bmp", "psd", "ai", "tiff",
        "svg", "wav", "mp3", "css", "js", "jpeg", "jpg", "gif", "png"
    );
    $arrCssTypes = array("text", "pdf", "word", "word", "excel", "excel", "powerpoint", "powerpoint", "zip", "zip", "zip", "zip",
        "markup", "markup", "markup", "markup", "video", "video", "video", "video", "video", "video", "image", "photoshop", "illustrator", "image",
        "image", "audio", "audio", "coding", "coding", "image", "image", "image", "image", "image"
    );
    if (in_array(strtolower($contentType), $arrFilTypes)) {
        $key = array_search($contentType, $arrFilTypes);
    }
    if ($key > 0) {
        if (array_key_exists($key, $arrCssTypes)) {
            $icon = $arrCssTypes[$key];
        }
    }
    return $icon;
}

function isEmailValid($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function getControllerAction() {
    $controllerPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $siteFolderPath = basename(ROOT);

    if (stripos($controllerPath, $siteFolderPath) == true) {
        $controllerPath = str_ireplace($siteFolderPath, '', $controllerPath);
    }
    return $controllerPath;
}

function fSizeValidation($type, $size) {
    global $GLOBAL_CONFIG;
    $app = new app();
    $log = array();
    $maxSize = $GLOBAL_CONFIG->maxfileSize;
    if ($type === 'avatar') {
        if (isset($size) && $size >= $maxSize) {
            $log['status'] = "error";
            $log['messge'] = "Opps Sorry!! FileSize exceeded 10MB" . $size;
            $app->log('fileSize', $log['messge']);
        } else {
            $log['status'] = "success";
            $log['messge'] = "SucessFully Uploaded your Profile Picture";
            $app->log('fileSize', $log['messge']);
        }
        return $log;
    }
}

function printArray($a) {
    echo '<pre>';
    print_r($a);
    echo '</pre>';
}

//return random arrays|objects from the given array @ parameter#1
function arrayRandom( $array, $count=3 ){
    if( count($array)<=$count ){
        return $array;
    }
    $randmResults = array(); //reset the projects object
    $keys = array_rand($array, $count);
    if( count($keys)>0 ){
        foreach ($keys as $i) {
            if (isset($array[$i])) {
                array_push($randmResults, $array[$i]);
            }
        }
    }else{
        $randmResults = $array;
    }
    return $randmResults;
} //end arrayRandom function 

function arrayMergeIndex( $array1, $array2 ) {
    foreach( $array1 as $index=>$value ){
//        if( isset($array2[$index]) ){
        $array2[$index] = $value ;
    }
    return $array2;
}

function isMultiArray($array) {
    return (count($array) != count($array, 1));
}

//will clean up the array values like
//removing duplicate values in an array 
//deleting values from an array
function sanitizeArray( $srcArray, $remove=[] ){
    $srcArray = array_unique($srcArray);
    if( count($remove)>0 ){
        foreach($remove as $str ){
            if(($key = array_search($srcArray, $srcArray)) !== false) {
                unset($srcArray[$key]);
            }
        }
    }
    return $srcArray;
}

function lang( $code = "", $print = true, $arrValues = array(), $html = true ){
    $localization = new localization();
    return $localization->get( $code, $arrValues, $print );
}

function getAvg( $sum, $total, $round=false, $decimal=0 ){
    if( $total==0 ){ return $total; }
    
    $avg = $sum/$total ;
    
    if( $round==false ){
        return $avg;
    }
    
    if( $decimal==0 ){
        return round($avg);
    }
    
    return round($avg, $decimal);
}

function getPercentage( $num ){
    return $num*100 ;
}

function formatNumber( $value, $type='' ){
    if( $type=="page_header" ){
        if( $value<=10 )
            return $value ;
        if( $value>90 )
            return "90+" ;
        return ceil(($value/10)*10)."+";
    }else if( $type=="file_size" ){
        $s = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $e = floor(log($bytes)/log(1024));
        return sprintf('%.2f '.$s[$e], ($bytes/pow(1024, floor($e))));
    }
    
    if( !is_numeric($value) ){
        die("formatNumber function accepts a number only.");
    }
    return number_format($value, 2, '.', ',');
}

function getDateDiff( $date1, $date2='', $return='days' ){
    $date2 = $date2=="" ? date('Y-m-d H:i:s') : $date2 ;
    $str = strtotime($date1) - strtotime($date2);
    $divr = 60 * 60 * 24;
    if( $return=='hours' ){
        $divr = 60 * 60  ;
    }else if( $return=='minutes' ){
        $divr = 60  ;
    }else if( $return=='seconds' ){    
        $divr = 1;
    }
    return floor($str/$divr);
}

function secondsToTime($inputSeconds, $return='') {

    $secondsInAMinute = 60;
    $secondsInAnHour  = 60 * $secondsInAMinute;
    $secondsInADay    = 24 * $secondsInAnHour;

    // extract days
    $days = floor($inputSeconds / $secondsInADay);

    // extract hours
    $hourSeconds = $inputSeconds % $secondsInADay;
    $hours = floor($hourSeconds / $secondsInAnHour);

    // extract minutes
    $minuteSeconds = $hourSeconds % $secondsInAnHour;
    $minutes = floor($minuteSeconds / $secondsInAMinute);

    // extract the remaining seconds
    $remainingSeconds = $minuteSeconds % $secondsInAMinute;
    $seconds = ceil($remainingSeconds);

    // return the final array
    $obj = array(
        'd' => (int) $days,
        'h' => (int) $hours,
        'm' => (int) $minutes,
        's' => (int) $seconds,
    );
    
    return ($return!='' && isset($obj[$return])) ? $obj[$return] : $obj;
}

function dateAdd( $int=0, $format='', $date='' ){
    $dateServices = new dateServices();
    return $dateServices->dateAdd( $int, $format, $date );
}

function getTimeElapsed($ptime){
    $etime = time() - $ptime;
    if ($etime < 1)
        return 'just now';

    $a = array( 365 * 24 * 60 * 60  =>  'year',
                 30 * 24 * 60 * 60  =>  'month',
                      24 * 60 * 60  =>  'day',
                           60 * 60  =>  'hour',
                                60  =>  'minute',
                                 1  =>  'second'
                );
    
    $a_plural = array( 'year'   => 'years',
                       'month'  => 'months',
                       'day'    => 'days',
                       'hour'   => 'hours',
                       'minute' => 'minutes',
                       'second' => 'seconds'
                );

    $type = "" ;
    foreach ($a as $secs => $str){
        $d = $etime / $secs;
        if ($d >= 1){
            $r = round($d);
            $type = ($r > 1 ? $a_plural[$str] : $str) ;
            if( $type=="day" ){
                return "yesterday";
            }else if( $type=="hour" ){
                return "an hour ago";
            }else if( $type=="minute" ){    
                return "a minute ago";
            }else if( $type=="second" || $type=="seconds" ){
                return "just now";
            }
            return  $r . ' ' . $type . ' ago';
        }
    }
}

function toBoolean($var){
    return filter_var( $var , FILTER_VALIDATE_BOOLEAN);
} 

function toLog( $msg, $logmsgs=[] ){
    if( gettype( $msg )=="array" ){
        $logmsgs = array_merge( $logmsgs, $msg );
    }else if( $msg!="" ){
        array_push($logmsgs, $msg);
    }
    return $logmsgs;
}
    
function dlConfHelper( $name, $loadConfig=true ){
    $sgdl = new sgDataLoader;
    $sgdl->loadConfig( strtolower($name), true ) ;
}

//check whether a file is minified by using file extension
function isMinFile( $filename, $type=".min.css" ){
    return strtolower(substr($filename, -strlen($type)))==$type ? true : false;
}

//return the filename of minified file
//if no file found for minified version, return just the filename
function getMinFile( $filename, $type=".min.css", $siteUrl='' ){
    $type = $type!="" ? $type : ".min.css" ;
    if( !isMinFile($filename, $type) ){
        $minFilename = substr_replace($filename, $type, -($type==".min.css"?4:3) );
    }else{
        $minFilename = $filename;
    }
    $filename = file_exists(ROOT.$minFilename) ? $minFilename : $filename;
    return $siteUrl . $filename;
}

function gzCompressUrl( $file, $path="" ){
    global $GLOBAL_CONFIG, $APPCOMPILER;
    return $file;
}

function isFilePath( $fileOrPath ){
    if( is_dir($fileOrPath)==true ){ return true; }
    if( $fileOrPath=="." || $fileOrPath==".." ){ return true; }
    return false;
}

?>