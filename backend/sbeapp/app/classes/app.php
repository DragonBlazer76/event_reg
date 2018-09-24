<?php

class app extends appLogs {

    function __construct() {

        parent::__construct();
        global $APPVARS, $GLOBAL_CONFIG;

        $this->config = $GLOBAL_CONFIG;
        $this->output = array();
        $this->logFile = date('Ymd');

        if (isset($APPVARS->init)) {
            return;
        }

        //setting the environment variables of the apps
        //get the environment set
        if (isset($this->config->environment) && @$this->config->environment != '' && !isset($APPVARS->envConfig)) {
            if (!file_exists(CONFIG . 'env/' . $this->config->environment . '.php')) {
                appLogs::log("CONFIG: Unable to load environment config file at " . CONFIG . 'env/' . $this->config->environment . '.php.');
                exit();
            } else {
                include_once( CONFIG . 'env/' . $this->config->environment . '.php' );
                global $environmentVars;
                $APPVARS->envConfig = $environmentVars;
            }
        }

        if (!isset($APPVARS->envConfig)) {
            appLogs::log("CONFIG: envConfig is undefined or not loaded.");
            exit();
        }

        $this->sessionHandler();

        $APPVARS->isAuthenticated = userServices::isAuthenticated();
        $APPVARS->userAuthCode = appAuth::getAuthenticationCode();
//
        $APPVARS->user = array();
    } //end __construct function 

    //initialize the controller:action
    function init() {
        new appDatabase(true);
        global $APPVARS, $DB;
        $APPVARS->init = true;

        $params = appUrl::getControllerAction();
        $APPVARS->requestUrl = @$params['url'];
        $APPVARS->siteUrl = substr($this->config->siteUrl, -1) == '/' ? $this->config->siteUrl : $this->config->siteUrl . '/';

        $mid = userServices::getMemberId();

        //for error pages handling, no need to execute below codes
        if (@$params['controller'] == 'sp' && @$params['action'] == 'errorPages') {
            return $this->initController(@$params['controller'], @$params['action'], @$params['view']);
        }

        //initiate DB Connection here
        new appDatabase(true);

        //set the timeout for session
        if ($this->config->timeout > 0) {
            //check for config timeout & whether user is authenticated
            //sets the last activity time of the user
            $this->setValue('timeout', time());
        }

        if ($APPVARS->isAuthenticated == true && $mid !== "") {
            $user = $DB->findOne("SELECT * FROM  users WHERE `id`='$mid';");
            $APPVARS->user = (array) $user;
        }//end if isUserAuthenticated

        if (!isset($params['controller']) || @$params['controller'] == "") {
            appLogs::log('APPINIT: ' . $params['controller'] . ' is empty. Request Url: ' . appUrl::get());
            $this->redirect("404");
        } else if (substr(@$params['controller'], 0, 1) == "/") { //redirect within the application
            $this->redirect($params['controller']);
        } else if (substr(@$params['controller'], 0, 4) == "http") { //redirect outside the application            
            unset($params['url']);
            header("Location: " . implode(".", $params));
            exit;
        }

        return $this->initController(@$params['controller'], @$params['action'], @$params['view']);
    } //end init function 

    function initController($controllerName, $action = 'index', $view = 'index') {

        $controller = $controllerName . 'Controller';

        //set the controller & action name to global variables
        //can be access anywhere
        global $APPVARS, $GLOBAL_CONFIG;
        $APPVARS->controllerName = strtolower($controllerName);
        $APPVARS->activeMenu = $APPVARS->controllerName;
        $APPVARS->actionName = strtolower($action);
        $APPVARS->viewName = strtolower($view);
        $APPVARS->setupApp = false;

        //initiate the policy only if the request is not coming from ajax
        if (!isAjaxRequest()) {
            new sgPolicy();
        }

        //check whether the controller is found
        if (!class_exists($controller)) {
            appLogs::log("APPINIT: $controller Controller does not exists. Request Url: " . appUrl::get());
            $this->redirect("404");
        } else if (!method_exists($controller, $action)) {
            appLogs::log("APPINIT: $controller::$action does not exists. Request Url: " . appUrl::get());
            $this->redirect("404");
        } else {
            $c = new $controller();
            if (isAjaxRequest() == false) {
                $this->setBreadcrumb(isset($c->controllerTitle) ? $c->controllerTitle : $APPVARS->controllerName, $APPVARS->controllerName, 'controller');
                if ($action != "index") {
                    $this->setBreadcrumb($action, "#", '');
                }
            }
            $c->$action();
        }

        exit;
    }

//end initController function 

    function getJsonError() {
        $error = '';
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = 'No errors';
                break;
            case JSON_ERROR_DEPTH:
                $error = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $error = 'Unknown error';
                break;
        }
        return $error;
    }

//end getJsonError function 

    function redirect($redirectUrl = '') {
        global $GLOBAL_CONFIG;

        $siteUrl = substr($GLOBAL_CONFIG->siteUrl, -1) == '/' ? $GLOBAL_CONFIG->siteUrl : $GLOBAL_CONFIG->siteUrl . '/';

        $redirectUrl = appUrl::getErrorRedirect($redirectUrl);

        //check $redirectUrl has an http
        if (strpos($redirectUrl, 'http') !== false) { //found http at the beginning
            $siteUrl = '';
        }

        ob_start();
        @header('Location: ' . $siteUrl . $redirectUrl);
        die();
    }

//end redirect function 
    //throw the result (eg. error) into page
    //the next script will not be executed.
    function pageError($message, $errType = "error", $log = false) {

        if ($log == true) {
            appLogs::log($message, $errType);
        }

        //check whether the request is coming from the xmlhttprequest or ajax
        if (isAjaxRequest() == true) {
            global $GLOBAL_CONFIG;

            $debug = isset($GLOBAL_CONFIG->debug) ? $GLOBAL_CONFIG->debug : false;

            if ($debug == true) {
                $reqMethod = getRequestMethod();
                $this->addOutput('method', $reqMethod);
                $this->addOutput('reqUrl', appUrl::get());
            }

            //do not allow to redirect to any page error
            //throw json output instead
            $this->addOutput('status', $errType);
            $this->addOutput('message', $message);
            $this->printOutput();
        }

        //user will be redirected the previous page
        //and display the flash messages to the user
        appServices::addFlashMessage($message, $errType);
        $redirect = appUrl::getPrevUrl();

        $this->redirect($redirect);
        exit;
    }

//end pageError function 
    //function to collect data, saved in array
    //and output as a json object
    function addOutput($key, $value) {
        //check whether this value can be encoded as json
        $isJson = json_encode($value);
        if ($isJson === false) {
            return error_log("appVars::addOutput Problem with json encoding using key ($key). Error: " . json_last_error_msg() . json_encode($value));
        }

//        $this->output[$key] = is_array($value) ? array_values($value) : $value ;
        $this->output[$key] = $value;
    }

//end addOutput function 
    //print the array data output in corresponding output format 
    //eg in json object
    function printOutput() {
        //$this->config->outputFormat=="json" ){
        //header('Content-Type: application/json');
        $encoded_json = json_encode($this->output, JSON_FORCE_OBJECT);
        $this->addOutput('isJsonReturnType', "true");
        if ($encoded_json == false) {
            $error = $this->getJsonError();
            $this->addOutput('status', 'error');
            $this->addOutput('message', $error);
            echo json_encode($this->output, true);
        } else {

            if (!isset($this->output['status'])) {
                $this->addOutput('status', 'success');
                $this->addOutput('message', '');
            }

            echo json_encode($this->output, true);
        }
        die();
        exit;
    }

//end printOutput function 
    //this file will output all the contents in the views
    //based on the current controller and its views
    function view($override = []) {
        global $APPVARS, $GLOBAL_CONFIG;

        $theme = '';
        $APPVARS->layout = "default";

        if (is_array($override) && count($override)>0 ) {
            $theme = isset($override['theme']) && $override['theme'] != "" ? $override['theme'] : $theme;
            $APPVARS->layout = isset($override['layout']) && $override['layout'] != "" ? $override['layout'] : $APPVARS->layout;
        }

        $pageServices = new pageServices($theme);
        $pageServices->setupPage();

        //$APPVARS->viewPath = VIEWS . $APPVARS->controllerName . '/' . ($override!=""?$override:$APPVARS->viewName) . '.php' ;
        $APPVARS->viewPath = VIEWS . $APPVARS->controllerName . '/' . $APPVARS->viewName . '.php';
        $mockUpView = VIEWS . $APPVARS->controllerName . '/' . $APPVARS->viewName . '.mock.php';
        $APPVARS->useMockUpView = false;

        //override the layout, views, etc  variables
        if (count($override) > 0) {
            if (isset($override['layout']) && @$override['layout'] != "")
                $APPVARS->page["layout"] = $override['layout'];
            if (isset($override['view']) && @$override['view'] != "") {
                $APPVARS->page["viewPath"] = $override['view'];
                $APPVARS->viewPath = $override['view'];
            }
        } else if (isset($APPVARS->layout)) {
            $APPVARS->page["layout"] = $APPVARS->layout;
        }

        if (isset($GLOBAL_CONFIG->useMockUpView) && $GLOBAL_CONFIG->useMockUpView == true) {
            $APPVARS->useMockUpView = $GLOBAL_CONFIG->useMockUpView;
            if (fileHandling::exists($mockUpView) == true) {
                $APPVARS->viewPath = $mockUpView;
            }
        }

        if (fileHandling::exists($APPVARS->viewPath) == true) {
            include_once( VIEWS . "layout.php" );
            exit;
        }

        //if view not found, check for index.php for default page
        $APPVARS->viewPath = VIEWS . $APPVARS->controllerName . '/index.php';

        if (fileHandling::exists($APPVARS->viewPath) == true) {
            include_once( VIEWS . "layout.php" );
            exit;
        }

        //if still not found, throw error
        die('View not found for ' . $APPVARS->controllerName . '::' . $APPVARS->actionName . '.');
    } //end view function 

    function userLogin($validation) {
        $this->user($validation);
    } //end userLogin function 

    function setBreadcrumb($title, $url = '', $type = '', $index = -1) {
        global $APPVARS;
        $breadcrumb = isset($APPVARS->breadcrumb) ? $APPVARS->breadcrumb : [];
        $title = ucwords($title);

        //$index===true get the last index

        if (gettype($index) == "boolean" && $index === true && count($breadcrumb) >= 1) {
//        if( gettype($index)=="boolean" && $index===true && count($breadcrumb)>=1 ){
            array_pop($breadcrumb);
        } else if (gettype($index) == "integer" && $index >= 0 && count($breadcrumb) >= 1) {
            if (isset($APPVARS->breadcrumb[$index])) {
                $APPVARS->breadcrumb[$index]['title'] = $title;
                $APPVARS->breadcrumb[$index]['url'] = $url;
                $APPVARS->breadcrumb[$index]['hash'] = $type == "hash";
            }
            //do nothing
            return;
        } else if ($index == "add") {
            
        }

        $url = $url != "#" ? $APPVARS->siteUrl . $url : $url;

        array_push($breadcrumb, array(
            "title" => $title,
            "url" => $url,
            "hash" => $type == "hash"
        ));
        $APPVARS->breadcrumb = $breadcrumb;
    }

//end setBreadcrumb function 

    function sessionHandler() {
        global $GLOBAL_CONFIG;

        session_start();

        //if empty, use default php session handler
        if ($GLOBAL_CONFIG->sessionHandler == '') {
            return;
        }

        if ($GLOBAL_CONFIG->dbEngine == 'mysql') {
            //pending here, session saved to MySQL
            return;
        }

//        $classSession = trim($GLOBAL_CONFIG->sessionHandler);
//        $method = 'getConnParams';
//
//        if (!class_exists($classSession)) {
//            appLogs::log("APPINIT: $classSession Class does not exists. Request Url: " . appUrl::get());
//            return;
//        }
//
//        $mongoDb = new mongoDb(false);
//        $params = $mongoDb->getConnParams();
//        $params['cookie_domain'] = parse_url($GLOBAL_CONFIG->siteUrl, PHP_URL_HOST);
//
//        if ($params['isReplica'] == false) {
//            unset($params['connection_opts']);
//        }
//
//        unset($params['isReplica']);
//        unset($params['conf']);
//
//        $params['cache'] = 'nocache';
//        $params['cache_expiry'] = 180;
//
//        MongoSession::config($params);
//
//        MongoSession::init();
//
//        session_start();
//
//        //setting cookie added here
//        $cname = 'PHPSESSID';
//        $mngSessionId = MongoSession::getSessionId();
//        $PHPSESSID = isset($_COOKIE[$cname]) ? trim($_COOKIE[$cname]) : "";
//        if ($PHPSESSID == "" || $PHPSESSID != $mngSessionId) {
//            setcookie($cname, $mngSessionId);
//            $_COOKIE[$cname] = $mngSessionId;
//        }
    }

//end sessionHandler function
}

?>
