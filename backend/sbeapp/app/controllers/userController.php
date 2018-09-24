<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class userController extends app {

    function __construct() {
        parent::__construct();
    }

    function index() {
        global $APPVARS, $DB;
        $now = date('Y-m-d H:i:s');
         global $DB, $GLOBAL_CONFIG, $DB, $APPVARS;

        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        var_dump($input);exit;

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
            echo $uemail;
            if ($uname == "" || strlen($uname) == 0 || $uemail == "" || strlen($uemail) == 0) {
                $this->error = "Username and password are required.";
            }//if
            //check if email is valid email address format
//            if (filter_var($uemail, FILTER_VALIDATE_EMAIL) === false) {
//                $this->error = "$uemail is not a valid email address.";
//            }
            if ($this->error != "") {
                $this->view();
            }
            if (@$pwd) {
                $password = userServices::hashPassword($pwd);
            }else{
                $password =$user['password'];
            }
            echo $password;
            $updateQuery = $DB->update("UPDATE  users SET email='" . $uemail . "',name ='" . $uname . "',password ='" . $password . "' where id = $userId ");
            if (count($updateQuery) > 0) {
                $result = $DB->findOne("SELECT * FROM users where where id = $userId");
                $this->email = $result['email'];
                $this->name = $result['name'];
            }
            $this->view();
        }
       $this->view();

    } //index
    
    
}




?>

