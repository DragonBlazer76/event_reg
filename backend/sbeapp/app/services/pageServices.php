<?php

class pageServices {

    function __construct($overrideTheme = '') {

        global $APPVARS, $GLOBAL_CONFIG, $GLOBAL_PAGECONFIG;
        $this->globalPageConfig = $GLOBAL_PAGECONFIG;
        $this->pageConfig = $this->globalPageConfig;
      
        
        if (!isset($PAGECONFIG) && isset($APPVARS->controllerName) && file_exists(CONFIG . 'pages/' . $APPVARS->controllerName . '.php')) {
            require_once CONFIG . 'pages/' . $APPVARS->controllerName . '.php';
            global $PAGECONFIG;
            
            //merge 2 page config options
            $this->pageConfig = (object)array_merge((array)$this->globalPageConfig, (array)$PAGECONFIG);
        }
        
        $APPVARS->siteUrl = $GLOBAL_CONFIG->siteUrl;
        $APPVARS->cdnSrc = $GLOBAL_CONFIG->cdnSource;
        $APPVARS->cdnImgSrc = $GLOBAL_CONFIG->cdnImgSource;
        $APPVARS->relPath = SITE_URL;
        $APPVARS->assetPath = SITE_URL . ASSETS;
        $APPVARS->assetUrl = SITE_URL . ASSETS;
        $APPVARS->widgets = array();
        $APPVARS->css = array();
        $APPVARS->js = array();
        
        if ($overrideTheme != "") {
            $this->pageConfig->theme = $overrideTheme;
        }

        //check & load page's theme
        if (isset($this->pageConfig->theme)) {
            $theme = $this->pageConfig->theme;
            $APPVARS->theme = $this->pageConfig->theme ;
            
            global $THEMECONFIG;
            
            //load the theme config
            if ((!isset($THEMECONFIG) || $THEMECONFIG == NULL) && file_exists(CONFIG . "themes/$theme.php")) {
                require_once CONFIG . "themes/$theme.php";
                $APPVARS->themeConfig = $THEMECONFIG;
            }
        }

        $APPVARS->page = array(
            "metaTitle" => $GLOBAL_CONFIG->siteName,
            "pageTitle" => isset($this->pageConfig->pageTitle) ? $this->pageConfig->pageTitle : $GLOBAL_CONFIG->siteName,
            "layout" => "twoColumns",
            "pageHeader" => "pageHeader",
            "isHome" => false,
            "properties" => array()
        );

        if (@$APPVARS->isAuthenticated == false && $APPVARS->controllerName == "home" && $APPVARS->actionName == "index") {
            $APPVARS->page["layout"] = "plain"; //oneColumn
            $APPVARS->page["pageHeader"] = "pageHeader-home";
            $APPVARS->page["isHome"] = true;
        } else if (@$APPVARS->isAuthenticated == false) {    //&& $APPVARS->controllerName=="home" && $APPVARS->actionName!="index" 
            $APPVARS->page["layout"] = "plain";
            $APPVARS->page["pageHeader"] = "pageHeader-home";
        } else if (@$APPVARS->isAuthenticated == true) {
//            userServices::getWidgets();
        }
      
        $this->loadUIComponents();
    } //end __construct function

    function loadUIComponents( $components=array() ){
        global $APPVARS, $UICOMPONENTS;
        
        if( count($components)==0 ){
            return;
        }

        foreach ($components as $name) {
            if( !isset($UICOMPONENTS->$name) ){
                continue;
            } //end if 
            
            $component = $UICOMPONENTS->$name;
            if( isset($component['css']) ){
                $APPVARS->css = array_merge($APPVARS->css, $component['css']);
            } //end if css
            
            if( isset($component['js']) ){
                $APPVARS->js = array_merge($APPVARS->js, $component['js']);
            } //end if js 
            
        } //end for each
    } //end loadUIComponents function

    function loadThemeAssets() {
        global $APPVARS;

        if( !isset($APPVARS->themeConfig) ){
            return;
        }
        
        //check & load css files
        if( isset($APPVARS->themeConfig->css) && count($APPVARS->themeConfig->css)>0 ){
            //theme css must come first
            $APPVARS->css = array_merge($APPVARS->themeConfig->css, $APPVARS->css);
        } //end if $APPVARS->themeConfig->css
        
            //loading of cssAddOns
            if (isset($APPVARS->themeConfig->cssAddOns) && count($APPVARS->themeConfig->cssAddOns)>0 ){
                //addOns are loaded according to its defined index
                //e.g. authentication code based based on the
                if( isset($APPVARS->themeConfig->cssAddOns[$APPVARS->userAuthCode]) && count($APPVARS->themeConfig->cssAddOns[$APPVARS->userAuthCode])>0 ){
                    $APPVARS->css = array_merge($APPVARS->css, $APPVARS->themeConfig->cssAddOns[$APPVARS->userAuthCode]);
                }
            } //end if cssAddOns

        if( isset($APPVARS->themeConfig->js) && count($APPVARS->themeConfig->js)>0 ){
            //theme css must come first
            $APPVARS->js = array_merge($APPVARS->js, $APPVARS->themeConfig->js);
        } //end if $APPVARS->themeConfig->js
            
            //loading of jsAddOns
            if (isset($APPVARS->themeConfig->jsAddOns) && count($APPVARS->themeConfig->jsAddOns)>0 ){
                //addOns are loaded according to its defined index
                //e.g. authentication code based based on the
                if( isset($APPVARS->themeConfig->jsAddOns[$APPVARS->userAuthCode]) && count($APPVARS->themeConfig->jsAddOns[$APPVARS->userAuthCode])>0 ){
                    $APPVARS->js = array_merge($APPVARS->js, $APPVARS->themeConfig->jsAddOns[$APPVARS->userAuthCode]);
                }
            } //end if jsAddOns
    } //end loadThemeAssets function 

    //function to load all the site's assets including
    //#1 common css & js
    //#2 load theme's css & js
    //#3 per page config, css/js load only for specific page
    //#4 adding css/js from calling in controller's action & function
    //LESS Compiler : currently removed, not supported. Use compile.php instead
    function setupPage() {
        global $APPVARS, $UICOMPONENTS, $GLOBAL_CONFIG;

        //#1 load common css & js
        if( isset($this->pageConfig->jsDefaults) && count($this->pageConfig->jsDefaults)>0 ){
//            $APPVARS->js = array_merge($APPVARS->js, $this->pageConfig->jsDefaults);
            foreach( $this->pageConfig->jsDefaults as $f) {
                if (strpos($f, "*") === false) {
                    array_push($APPVARS->js, $f);
                } else {
                    $fs = explode("/", $f);
                    $fs = str_replace("*", "", end($fs));
                    $APPVARS->js = array_merge($APPVARS->js, loadFiles(ASSETS.$fs) );
                }
            }
        }
            
        if( isset($this->pageConfig->cssDefaults) && count($this->pageConfig->cssDefaults)>0 )
            $APPVARS->css = array_merge($APPVARS->css, $this->pageConfig->cssDefaults);
        
        //#2 load theme's css & js
        $this->loadThemeAssets();
        
        //#3 per page config, css/js load only for specific page
        if( isset($this->pageConfig->js) && count($this->pageConfig->js)>0 ){
            $APPVARS->js = array_merge($APPVARS->js, $this->pageConfig->js);
        }
        
        if( isset($this->pageConfig->css) && count($this->pageConfig->css)>0 ){
            $APPVARS->css = array_merge($APPVARS->css, $this->pageConfig->css);
        }
     
        //load UI Components sets for this controller
        if( isset($this->pageConfig->uiComponents) && count($this->pageConfig->uiComponents)>0 ){
            //this will load all the css & js files per component defined
            $this->loadUIComponents( $this->pageConfig->uiComponents );
        }
        
        //setting the page properties based on the pageConfig
        $APPVARS->page["properties"] = isset($this->pageConfig->properties) ? $this->pageConfig->properties : array();
            
        //check whether the controller's action is defined
        if( !isset($this->pageConfig->actions) && !isset($this->pageConfig->actions[$APPVARS->actionName]) ){
            //get the proper page title & meta
            $this->getTitle();
            return;
        }

        //per set action
        $actionConfig = isset($this->pageConfig->actions[$APPVARS->actionName]) ? $this->pageConfig->actions[$APPVARS->actionName] : [];
        
        //override the pageproperty if set in action
        if (isset($actionConfig["properties"])) {
            $APPVARS->page["properties"] = $APPVARS->page["properties"] = array_merge($actionConfig["properties"], $APPVARS->page["properties"]);
        }
        
        //override the layout based on controller::action
        if (isset($actionConfig["layout"]) && $actionConfig["layout"]!="" ) {
            $APPVARS->page["layout"] = $actionConfig["layout"];
        }
         
        //loads the data from MongoDB
        if (isset($actionConfig["dataLoader"]) && count($actionConfig["dataLoader"]) > 0) {
            $sgdl = new sgDataLoader;
            foreach ($actionConfig["dataLoader"] as $method=>$toInlineJs ){
                if (method_exists($sgdl, $method)) {
                    $sgdl->load($method, $toInlineJs);
                }
            }
        }
                
        //check if there's a separate css to be added
        if (isset($actionConfig['cssAddOns']) && count($actionConfig['cssAddOns']) > 0) {
            $APPVARS->css = array_merge($APPVARS->css, $actionConfig['cssAddOns']);
        }
                         
        //check if there's a separate js to be added
        if (isset($actionConfig['jsAddOns']) && count($actionConfig['jsAddOns']) > 0) {
            $APPVARS->js = array_merge($APPVARS->js, $actionConfig['jsAddOns']);
        }       
        
        //load individual uiComponents based on the controller's action set in each pageConfig
        //priority to load JS files will be based on per controller's action
        if (isset($actionConfig['uiComponents']) && count($actionConfig['uiComponents']) > 0) {
            $this->loadUIComponents( $actionConfig['uiComponents'] );
        }
        
        //js added through the function call addJS
        if (isset($APPVARS->addJSfiles) && count(@$APPVARS->addJSfiles) > 0) {
            $APPVARS->js = array_merge($APPVARS->js, $APPVARS->addJSfiles);
        }
            
        $loadInitJS = isset($this->pageConfig->initJs) ? $this->pageConfig->initJs : true;
        $loadInitJS = isset($actionConfig['initJs']) ? $actionConfig['initJs'] : $loadInitJS;
        
        if( !isset($this->pageConfig->jsInitFile) ){
            appServices::log('$GLOBAL_PAGECONFIG->jsInitFile is not defined.');
            $loadInitJS = false;
        }
        
        //load the service's js
        if ( $loadInitJS==true && isset($this->pageConfig->servicePath) && $this->pageConfig->servicePath!="" ){
            //load corresponding controller's ViewModel
            if (file_exists(ASSETS . $this->pageConfig->servicePath . $APPVARS->controllerName . '.js')){
                //load javascripts according to controller's config
                //Source @ /config/pages/<controller-name>
                array_push($APPVARS->js, $this->pageConfig->servicePath . $APPVARS->controllerName . '.js');
            }
        }      
        
        //load the controller's js
        if ( $loadInitJS==true && isset($this->pageConfig->controllerPath) && $this->pageConfig->controllerPath!="" ){
            //load corresponding controller's ViewModel
            if (file_exists(ASSETS . $this->pageConfig->controllerPath . $APPVARS->controllerName . '.js')){
                //load javascripts according to controller's config
                //Source @ /config/pages/<controller-name>
                array_push($APPVARS->js, $this->pageConfig->controllerPath . $APPVARS->controllerName . '.js');
            }
        }
        
        if ( $loadInitJS==true && isset($this->pageConfig->jsInitFile) && $this->pageConfig->jsInitFile!="" ){
            array_push($APPVARS->js, $this->pageConfig->jsInitFile);
        }
        
        //override the pagetitle as set in the pageconfig
        if( isset($actionConfig["pageTitle"]) ) {
            $APPVARS->page['pageTitle'] = ucwords($actionConfig["pageTitle"]);
        }//metaTitle
        
    } //end setupPage function

    public static function addJS($file = '') {
        global $APPVARS;
        $array = isset($APPVARS->addJSfiles) ? $APPVARS->addJSfiles : [];
        array_push($array, $file);
        $APPVARS->addJSfiles = $array;
    } //end addJS function

    public static function addCSS($file = '') {
        global $APPVARS;
        $array = isset($APPVARS->addCSSfiles) ? $APPVARS->addCSSfiles : [];
       
        array_push($array, $file);
        $APPVARS->addCSSfiles = $array;
    } //end addCSS function

    function printInlineJS($isPrint = true) {
        global $APPVARS, $GLOBAL_CONFIG;

        if ($isPrint == false) {
            return $APPVARS->inlineJs;
        }

        $isUserAuthStatus = isset($APPVARS->userAuthCode) ? $APPVARS->userAuthCode : "";
        
        echo '<script type="text/javascript">'
        . 'var '.$GLOBAL_CONFIG->jsGlobalName.' = {'
        . ' timeout : ' . $GLOBAL_CONFIG->timeout
        . ', siteUrl : "' . $GLOBAL_CONFIG->siteUrl . '"'
//        . 'minStoryChar : ' . $GLOBAL_CONFIG->minChar                
//        . ', maxStoryChar : ' . $GLOBAL_CONFIG->maxChar
        . ', dFlashMsg : ' . $GLOBAL_CONFIG->displayFlashMessage
        . ', listCount : ' . $GLOBAL_CONFIG->listCount
        . ', auth : "' . $isUserAuthStatus . '"'
//        . ', authid : "' . $authMemberId . '"'
        . ' }; '; // var SG_GLOBALS = { 

        if (isset($APPVARS->inlineJs)) {
            foreach ($APPVARS->inlineJs as $dataName => $dataGroup) {
                echo " var " . strtoupper($dataName) . " = " . json_encode($dataGroup, true) . ";";
            }
        }

        echo '</script>';
    } //end printInlineJS function

    public static function getAssetPath() {
        global $APPVARS, $GLOBAL_CONFIG;
        return strlen($GLOBAL_CONFIG->cdnSource) > 0 ? $GLOBAL_CONFIG->cdnSource . ASSETS : SITE_URL . ASSETS;
    } //end getAssetPath function

    function printJS($isPrint = true) {
        global $APPVARS, $GLOBAL_CONFIG; 
        $printJS = array();
        $assetPath = strlen($GLOBAL_CONFIG->cdnSource) > 0 ? $GLOBAL_CONFIG->cdnSource : SITE_URL;
        foreach ($APPVARS->js as $f) {
            $isFile = substr(strtolower($f), -3);
            if (parse_url($f, PHP_URL_HOST) != "") {
                array_push($printJS, '<script type="text/javascript" src="' . $f . '"></script>');
            } else if ($isFile && $isFile == ".js") {
                if ($GLOBAL_CONFIG->useMinify == true) {
                    $f = getMinFile(ASSETS . $f, '.min.js', SITE_URL);
                } else {
                    $f = $assetPath . ASSETS . $f;
                }
                $f = gzCompressUrl($f);
                array_push($printJS, '<script type="text/javascript" src="' . $f . '"></script>');
            }
        } //end foreach

        if ($isPrint == true) {
            echo implode('', $printJS);
        } //end if $isPrint
        
        return $printJS;
    } //end printJS function

    function getConfig($s = '') {
        global $GLOBAL_CONFIG;
        if (isset($GLOBAL_CONFIG->$s)) {
            return $GLOBAL_CONFIG->$s;
        }
        return "";
    } //end getConfig function

    function getTitle() {
        global $APPVARS, $GLOBAL_CONFIG;

        //first priority to pick up the page title
        //$APPVARS->pageTitle, set to override in controllers
        $printTitle = isset($APPVARS->pageTitle) ? $APPVARS->pageTitle : "";

        //if no overriding pageTitle, then get the pageTitle defined in each controller's actions config
        ///config/pagess/<controller>.php
        if ($printTitle == "" && isset($APPVARS->page["pageTitle"])) {
            $printTitle = strlen($APPVARS->page["pageTitle"]) > 0 ? $APPVARS->page["pageTitle"] : $printTitle;
        } //end if $printTitle

        //if still the pageTitle is not set, get the pageTitle via controller name
        if ($printTitle == "") {
            $restrictedControllers = array('cfg', 'reqs', 'snconnect', 'sp', 'test', 'upload');
            if (!in_array(strtolower($APPVARS->controllerName), $restrictedControllers)) {
                $printTitle = $APPVARS->controllerName;
            }
        } //end if $printTitle

        $printTitle = ucwords($printTitle);

        if ($printTitle != "" && $GLOBAL_CONFIG->siteName != "") {
            if ($GLOBAL_CONFIG->appendSN == 'post') {
                $APPVARS->page["metaTitle"] = $printTitle . ' - ' . $GLOBAL_CONFIG->siteName;
            } else if ($GLOBAL_CONFIG->appendSN == 'pre') {
                $APPVARS->page["metaTitle"] = $GLOBAL_CONFIG->siteName . ' - ' . $printTitle;
            } else {
                $APPVARS->page["metaTitle"] = $printTitle;
            }
        } else if ($printTitle == "" && $GLOBAL_CONFIG->siteName != "") {
            $printTitle = $GLOBAL_CONFIG->siteName;
        } //end if $printTitle
    } //end if getTitle function

    function printCSS($isPrint = true) {
        global $APPVARS, $GLOBAL_CONFIG;
        $files = array();
        $lastCssFiles = array();
                
        if (isset($APPVARS->addCSSfiles) && @count($APPVARS->addCSSfiles) > 0) {
            $APPVARS->css = array_merge($APPVARS->css, $APPVARS->addCSSfiles);
        } //end if addCSSfiles

        foreach ($APPVARS->css as $f) {
   
            if (is_array($f)) {
                $media = isset($f['media']) && $f['media'] != "" ? 'media="' . $f['media'] . '"' : '';
                $fPlain = $APPVARS->theme . $f['css'];
                if ($GLOBAL_CONFIG->useMinify == true) {
                    //$f['css'] = getMinFile(ASSETS . $APPVARS->theme . $f['css'], '', SITE_URL);
                    $f['css'] = getMinFile(ASSETS . $f['css'], '', SITE_URL);
                } else {
//                    $f['css'] = SITE_URL . ASSETS . $APPVARS->theme . $f['css'];
                    $f['css'] = SITE_URL . ASSETS . $f['css'];
                }
               
                $isFile = substr(strtolower($f['css']), -4);
                if ($isFile == ".css") {
                    if( !isCompleteUrl($f['css']) ){
                        $f['css'] = gzCompressUrl($f['css'], $fPlain);
                    }
                    $cssLink = '<link rel="stylesheet" href="' . $f['css'] . '" ' . $media . '>';
                    if (isset($f['lastCss']) && @$f['lastCss'] == true) {
                        array_push($lastCssFiles, $cssLink);
                    } else {
                        array_push($files, $cssLink);
                    }
                }
            } else {
                $isFile = substr(strtolower($f), -4);
                if ($isFile == ".css"){
                    if( isCompleteUrl($f) ){
                        array_push($files, '<link rel="stylesheet" href="' . $f . '">');
                    }else{
                        $fPlain = $APPVARS->theme . $f;
                        $fPlain = $f;
                        if ($GLOBAL_CONFIG->useMinify == true) {
                            $f = getMinFile(ASSETS . $f, '', SITE_URL);
                        } else {
                            $f = SITE_URL . ASSETS . $f;
                        }

                        $f = gzCompressUrl($f, $fPlain);
                        array_push($files, '<link rel="stylesheet" href="' . $f . '">');
                    }
                } //end if $isFile
            } //end if is_array
        } //end foreach loop

        if (count($lastCssFiles) > 0) {
            $files = array_merge($files, $lastCssFiles);
        }

        if ($isPrint == true) {
            echo implode('', $files);
        }
        return $files;
    } //end printCSS function 

} //end class pageServices
?>