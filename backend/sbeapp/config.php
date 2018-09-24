<?php

$ssl = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? true : false;
$siteUrl = $ssl == true ? 'https' : 'http';
$siteUrl .= '://' . $_SERVER['HTTP_HOST'];

$hostname = $_SERVER["HTTP_HOST"];
$dirname = dirname($_SERVER["SCRIPT_NAME"]);
$siteUrl .= (strlen($dirname) > 2 ? $dirname : '') . '/';

if (strpos($hostname, "localhost") === false) {
    $environment = 'development';
} else {
    $environment = 'development';
}

/* Global Configuration File */
global $GLOBAL_CONFIG;

$GLOBAL_CONFIG = new stdClass();
$GLOBAL_CONFIG->lang = 'en-GB';                                 //language: currently support en (english 
$GLOBAL_CONFIG->environment = $environment;                    //values: development, staging|production

$GLOBAL_CONFIG->siteName = 'Event Registration';
$GLOBAL_CONFIG->country = 'Singapore'; //default country name
$GLOBAL_CONFIG->countryCode = 'SG'; //default country code
$GLOBAL_CONFIG->appendSN = 'post';  //values: pre, post or ''
$GLOBAL_CONFIG->sourceName = 'APPI';
$GLOBAL_CONFIG->siteUrl = $siteUrl;
$GLOBAL_CONFIG->cdnSource = $siteUrl;
$GLOBAL_CONFIG->cdnImgSource = $environment . "assets/";

$GLOBAL_CONFIG->salt = '$2a$10$gkXsjrBM7fygZOckZsFnEO';

$GLOBAL_CONFIG->codeLength = 15;    //code length
$GLOBAL_CONFIG->barcodeLength = 5;    //code length
$GLOBAL_CONFIG->listCount = 10;    //no. of items listed in all listing pages
$GLOBAL_CONFIG->displayFlashMessage = 10000;    //1000 = 1 second

///////////////////////////////////////////////////////////////////////////////////////////
//APP|SYSTEM CONFIGURATION
///////////////////////////////////////////////////////////////////////////////////////////
$GLOBAL_CONFIG->outputFormat = 'json';

//LOG & AUDITLOG 
$GLOBAL_CONFIG->logMode = 'custom';         //for file logging
$GLOBAL_CONFIG->logPrefix = 'APPI';          //for file logging
$GLOBAL_CONFIG->auditLog = true;            //audit log, log save to MongoDB
$GLOBAL_CONFIG->auditLogToFile = true;      //audit log, allow saving to the logfile?

$GLOBAL_CONFIG->jsGlobalName = 'gAPP';
//
//DEFAULT TIMEZONE
$GLOBAL_CONFIG->timezone = 'Asia/Singapore : ';

//DB CONFIGURATION
$GLOBAL_CONFIG->dbEngine = 'mysql'; //or mongodb
$GLOBAL_CONFIG->sessionHandler = ''; //if empty, use default php session handler (default: server file) Other Value: database
//SESSION TIMEOUT
$GLOBAL_CONFIG->timeout = 30;  //minutes, session timeout, user is inactive for X minutes, kill the session
$GLOBAL_CONFIG->inviteTimeout = 3;  //days, before someone can send invites again

//PAGES & COMPILERS
$GLOBAL_CONFIG->useMinify = false;
$GLOBAL_CONFIG->debug = false;   //display all php messages (e.g. notice, warnings, etc)

//allowed fileSize
$GLOBAL_CONFIG->allowedSize = array();

//fileSize Maximum Limit
$GLOBAL_CONFIG->maxfileSize = '10485760';

//Site Templating
$GLOBAL_CONFIG->defaultTheme = 'th-default';
$GLOBAL_CONFIG->useMockUpView = false;
?>
