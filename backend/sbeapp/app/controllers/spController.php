<?php
class spController extends app{
    
    function __construct(){
        parent::__construct();
        //$this->db = new sgMongoDb();
    }
    
    //do not edit this function errorPages
    function errorPages(){
        global $APPVARS;
        //override the layout
        //if redirecting to 404 -- change the redirection to / (landing page)
        if ($APPVARS->viewName == '404') {
            $this->redirect('/');
        }//if
            
        $this->view( 
            array(
                "theme" => "th-bs-forest",
                "layout" => "error",
                "view" => VIEWS . "error/" . $APPVARS->viewName . ".php"
            )
        );
    }
    
    function index(){
        global $APPVARS;
        
        $isUserAuthenticated = userServices::checkFullAuthentication() ;
        if( $isUserAuthenticated=="USER_AUTHENTICATED" && $APPVARS->viewName=="index" ){
            $this->redirect("user");
        }
        
        $title = $APPVARS->viewName=="index" ? "home" : $APPVARS->viewName ;
        $APPVARS->pageTitle = str_replace("_"," ",ucfirst($title)) ;        
        $APPVARS->htmlId = "public" ;
        
        $pagesArray = array('index' , 'service_provider' , 'hirer_how_it_works' , 'service_provider_how_it_works');

        $otherStaticPages = array('graphic_design', 'web_design', 'web_development', 'app_development', 'copywriting', 'translation',
            'service_provider', "service-provider",
            'about', 'privacypolicy', 'investors');
        
          
        if( in_array($APPVARS->viewName , $pagesArray) ) {
            global $SGCONVERSION;
            $SGCONVERSION->fbremarketing['isSgHome'] = true;
        }//if

        if( $APPVARS->viewName=="index" ){
            $path = "home/index.html";
            if(file_exists($path) ){
                echo file_get_contents($path);    
                exit;
            }
        }
        if( in_array($APPVARS->viewName , $otherStaticPages) ) {
            if( substr($_SERVER['REQUEST_URI'], -1)=='/' ){
//                $this->redirect( substr($_SERVER['REQUEST_URI'], 0, -1) );
                $this->redirect( $APPVARS->viewName );
            }
            if( $APPVARS->viewName == 'service-provider' || $APPVARS->viewName == 'service_provider'){
                $path = "home/service-provider/index.html";;
            }else {
                $path = "home/".$APPVARS->viewName."/index.html";
            }
            
            if(file_exists($path) ){
                echo file_get_contents($path);    
                exit;
            }
        }
        
        if( $isUserAuthenticated=="USER_AUTHENTICATED" ){
            
            $pageTitle = str_replace("_"," ",ucfirst($APPVARS->viewName)) ;
            
            //override the breadcrumb
            $this->setBreadcrumb( $pageTitle, "#", '', true );
            
            $APPVARS->htmlId = "public" ;
//            $APPVARS->activeSideMenu = false ;
                    
            //add 1 more css
            pageServices::addCSS("css/sg.public.css");
            
            $this->view( 
                array(
                    "theme" => "th-bs-forest",
                    "layout" => "one_column",
                    "view" => VIEWS . "sp/" . $APPVARS->viewName.".forest.php"
                )
            );
        }else{
            $this->view();
        }
        
    }
}
?>