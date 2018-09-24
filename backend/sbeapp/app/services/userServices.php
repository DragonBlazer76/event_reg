<?php

class userServices extends dbServices{

    function __construct() {
        $this->tableName = "user";      
    }

    function getFields( $type ){
        $fields = array('id','code','company_id','fname', 'lname' , 'status' ); 
        switch ($type) {
            case 'verification':
                $fields = array_merge($fields, ['verify_code','verify_code_expiry']);
                break;
            default:
                break;
        }
        return $fields;
    }
    
    function loadUser( $query ){
        global $DB;    
        return $DB->findOne("SELECT ".implode(",",$this->getFields('verification'))." FROM $this->tableName WHERE $query;");
    }
    
    function getUserType( $user=[] ){
        if( count($user)==0 ){
            global $APPVARS;
            if( !isset($APPVARS->user) || count($APPVARS->user)==0 ){
                return "";
            }
            $user = $APPVARS->user;
        }
        
        $userId = @$user->id; //isset($user['id']) ? $user['id'] : $user->id
        
        //query to get user's ACL id/type
        global $DB;
        
        $userType = $DB->findOne("SELECT UA.user_group_id, UG.name AS user_group_name 
            FROM `user_acl` UA INNER JOIN `user_groups` UG ON UG.id=UA.user_group_id 
            WHERE UA.user_id=$userId;");
        
        return $userType;
    }
    
    function isUserCompanyAdmin($user){
        $userType = $this->getUserType($user);
        if( count($userType)==0 ){
            return false;
        }
        if( gettype($userType)=="object" ){
            if( isset($userType->user_group_id) && @$userType->user_group_id==11 ) //for company administrator
                return true;
        }
        if( isset($userType['user_group_id']) && @$userType['user_group_id']==11 ) //for company administrator
            return true;
        return false;
    }
    
    public static function getMemberId($query = []) {
        //printArray($_SESSION['memberid']);
        if (count($query) > 0) {
            //load user's object and return the memberid only
            $u = userServices::loadUser($query);
            if (count($u) > 0) {
                return $u->memberid;
            } else {
                return '';
            }
        }

        if (userServices::isAuthenticated() == false) {
            return "";
        }

        return $_SESSION['mid'];
    }
    
    public static function isVerified($user) {
        return true;
    }//isVerified 

    public static function checkPassword($user, $password) {
        include_once(LIBRARIES . 'jBcrypt-v2.2/bcrypt.php');
        $bcrypt = new bCrypt;
        if ($password == "") {
            return false;
        }
        global $GLOBAL_CONFIG;
        $hashLogin = $bcrypt->hash($password, $GLOBAL_CONFIG->salt);
        return @$user->password == $hashLogin;
    }//checkPassword 

    public static function hashPassword($password) {
        include_once(LIBRARIES . 'jBcrypt-v2.2/bcrypt.php');
        $bcrypt = new bCrypt;
        if ($password == "") {
            return false;
        }
        global $GLOBAL_CONFIG;
        $hash = $bcrypt->hash($password, $GLOBAL_CONFIG->salt); 
        return $hash;
    }//hashPassword 

    public static function isAuthenticated() {
        return isset($_SESSION['authenticated']) ? $_SESSION['authenticated'] : false;
    }//isAuthenticated

    public static function checkFullAuthentication($autoRedirect = true, $user = array()) {
        
        if (!userServices::isAuthenticated()) {
            return "NOT_AUTHENTICATED";
        }

        global $APPVARS;

        $user = count($user) > 0 ? $user : $APPVARS->user;
        
        $isVerified = userServices::isVerified($user);

        if (!$isVerified) {
            return "USER_NOT_VERIFIED";
        }

        return "USER_AUTHENTICATED";
    }

    public static function checkMemberRole($currMemberId) {
        $role = "current";
        if (isset($_REQUEST) && @$_REQUEST['id'] !== "" && @$_REQUEST['id'] != NULL) {
            if ($_REQUEST['id'] !== $currMemberId) {
                $role = "Other";
            }
            if ($_REQUEST['id'] === $currMemberId) {
                $role = "current";
            }
        }

        return $role;
    }
    
    public static function sanitize($userData = array(), $options = array()) {
       
    }


}

?>
