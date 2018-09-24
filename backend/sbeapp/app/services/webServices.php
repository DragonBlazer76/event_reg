<?php

class webServices {

    function __construct() {
        
    }

    public static function validation($post = []) {
        $validate = array("status" => false, "error" => "");
        if ($post['event_id'] == "" || $post['app_id'] == "" || $post['code'] == "") {
            //throw error all fields are required 
            //redirect to same page with all the input fields 
            $validate['error'] = "All fields are required.";
            return $validate;
        }

        if ($post['app_id'] <> "02ff578c61992e25dbfb00ad9757cb0533707848") {
            $validate['error'] = "App ID is wrong.";
            return $validate;
        }
        $validate['status'] = true;
        return $validate;
    }

}
?>

