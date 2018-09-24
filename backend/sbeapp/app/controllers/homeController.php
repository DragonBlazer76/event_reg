<?php

class homeController extends app {

    function __construct() {
        parent::__construct();
    }

    function index() {
        global $APPVARS, $DB;
        $this->view();
    } //index

    function login() {
        global $APPVARS;
        
        if( $APPVARS->isAuthenticated==true ){
            $this->redirect("guests");
        }
        
        $doLogin = isset($_POST['task']) && trim(strtolower($_POST['task'])) == "login" ? true : false;
      
        if ($doLogin == false) {
            $this->view();
            exit;
        }

        $this->error = "";
        $this->errorCode = "";

        //proceed with the login process
        $p = $_POST;
        $uname = trim($p['email']);
        $pwd = trim($p['password']);
     
        if ($uname == "" || $pwd == "" || strlen($uname) == 0 || strlen($pwd) == 0) {
            $this->error = "Username and password are required.";
        }//if
        //check if email is valid email address format
        if (filter_var($uname, FILTER_VALIDATE_EMAIL) === false) {
            $this->error = "$uname is not a valid email address.";
        }

        if ($this->error != "") {
//            appServices::addFlashMessage($this->error, "error");
            $this->view();
        }
         
        //DB checking started here
        global $DB, $GLOBAL_CONFIG, $DB, $APPVARS;
        $result = $DB->findOne("SELECT * FROM users where email ='" . $uname . "'");
   
        if (count($result) == 0) {
            $this->error = "User not found!";
            $this->view();
        }//if

        $userSvc = new userServices();
        $dateNow = date('Y-m-d H:i:s');

        //proceed with validation
        if (userServices::checkPassword($result, $pwd) == false) {
            $this->error = "The email or password you entered is incorrect.";
            $this->view();
        }

        //if pwd correct check verified status
//        $validation['isVerified'] = userServices::isVerified($result);
        if ($result->status != "active") {
            $this->error = "Unable to log you in. Your account is not active yet.";
            $this->view();
        }

        //check this user's access level
        if ($result->user_level != "admin" && $result->user_level != "operator" && $result->user_level != "customer" ) {
            $this->error = "You dont have the right to access this section.";
            $this->view();
        }

        //generate session ID to be saved
        $result->sessionId = base64_encode($dateNow . $result->id . $result->email);
        $result->authenticated = true;
        $result->isVerified = true;

        $this->userLogin(@$result);
        $this->redirect("home/profile");
    } //end login function

    function logout() {
        //kill all session here and log out
        session_destroy();
        $this->redirect('login');
        die("home.logout here");
    } //end logout function

    private function forgotpwd() {

        $errors = array();

        $email = count(@$_POST['email']) > 0 ? trim(@$_POST['email']) : "";
        $pwd = count(@$_POST['password']) > 0 ? trim(@$_POST['password']) : "";
 

        //backend validation of required fields
        if (@$email == "" || empty($email)) {
            appServices::addFlashMessage("Email is required", "error");
            $this->redirect("home/forgot"); 
           
        }
        if (@$pwd == "" || empty($pwd)) {
             appServices::addFlashMessage("password is required", "error");
            $this->redirect("home/forgot"); 
        }

        global $DB;

        $user = $DB->findOne("SELECT id FROM `users` WHERE `email`='$email';");

        if (count(@$user) == 0) {
             appServices::addFlashMessage("The email you entered doesnot exists.", "error");
             $this->redirect("home/forgot"); 
            
        }//if
        else{
            if (@$pwd) {
                $password = userServices::hashPassword($pwd);
            }
            $updateQuery = $DB->update("UPDATE  users SET password='". $password . "' where email ='". $email."' ");
            if(count($updateQuery) > 0) {
                    appServices::addFlashMessage("Successfully updated your password", "success");
                   $this->redirect("home/login");   
            }
            
        }
        

        //update all these three reset_expiry , reset_code , code and send reset link to user
    } //forgotpwd

    function profile() {

        //defined global varible  here
        global $DB, $GLOBAL_CONFIG, $DB, $APPVARS;

        $this->error = "";
        $this->errorCode = "";
        //get current user login details
        $user = $APPVARS->user;
        $userId = $user['id'];
        $this->email = $user['email'];
        $this->name = $user['name'];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //proceed with the login process
            $p = $_POST;
            $uname = trim($p['name']);
            $uemail = trim($p['email']);
            $pwd = trim($p['password']);
            if ($uname == "" || strlen($uname) == 0 || $uemail == "" || strlen($uemail) == 0) {
                $this->error = "Username and password are required.";
            }//if
            //check if email is valid email address format
//            if (filter_var($uemail, FILTER_VALIDATE_EMAIL) === false) {
//                $this->error = "$uemail is not a valid email address.";
//            }
            if ($this->error!= "") {
                $this->view();
            }
            if (@$pwd) {
                $password = userServices::hashPassword($pwd);
            }else{
                $password = $user['password'];
            }
            
            $updateQuery = $DB->update('UPDATE users SET email=\''.$uemail.'\', `name`=\''.$uname.'\',`password`=\''.$password.'\' WHERE `id`=\''.$userId.'\';');
            if (count($updateQuery) > 0) {
                $result = $DB->findOne("SELECT * FROM users WHERE id = $userId");
                $result = (array) $result;
                $this->email = $result['email'];
                $this->name = $result['name'];
                
                appServices::addFlashMessage("You have successfully updated your profile.", "success");
            }
            $this->view();
        }
       $this->view();
       
    }
    function forgot(){
        $this->error = "";
        $this->errorCode = "";
        
       if($_SERVER['REQUEST_METHOD'] == 'GET'){
           $this->view();
       } 
       if($_SERVER['REQUEST_METHOD'] == 'POST'){
        if (appServices::isPostMethod()) {
            $this->forgotpwd();
        }
        $this->view();
       }
    }

}

?>
