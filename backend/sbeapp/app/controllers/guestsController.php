<?php

class guestsController extends app {

    function __construct() {
        parent::__construct();
    }

    function index() {
        global $APPVARS, $DB;
//        $now = date('Y-m-d H:i:s');
        //get the total number of guests registed in the DB
        $user = $APPVARS->user;

        $this->userRole = $user['user_level'];
        $res = $DB->count("SELECT COUNT(*) AS total FROM guests", "total");
        $this->totalGuests = $res['total'];

        //get the total number of events
        $res = $DB->count("SELECT COUNT(*) AS total FROM reference WHERE type='event'", "total");
        $this->totalEvents = $res['total'];

        if ($this->totalGuests == 0 && $this->totalEvents == 0) {
            $this->redirect('events/new');
        }

        //active event id
        $this->eventId = 4;

        if ($this->totalEvents > 0) {
            $eventSvc = new eventsServices();
            $this->events = $eventSvc->loadAll(1, 'id, name');
        }

        $this->view();
    }

//index function

    function import() {
        global $APPVARS;

        $user = $APPVARS->user;

        $userRole = $user['user_level'];
        if ($userRole == 'customer') {
            
            appServices::addFlashMessage("You dont have the right to import guest.", "info");
            $this->redirect("guests");
        }
        $eventSvc = new eventsServices();
        $this->events = $eventSvc->loadAll(1, 'id, name');
        $this->error = "";

        $this->post = $_POST;


        if (isset($_GET['event_id']) && trim($_GET['event_id']) != "") {
            $this->post['event_id'] = trim($_GET['event_id']);
        }

        if (appServices::isPostMethod() == false) {
            $this->view();
        }

        //validation checks
        if (@$_POST['event_id'] == "") {
            $this->error = "Please select event...";
        }

        if ($_FILES['file']['size'] == 0) {
            $this->error = "Please import CSV file.";
        } else {
            if ($this->error != "" && $_FILES['file']['error'] != UPLOAD_ERR_OK || !is_uploaded_file($_FILES['file']['tmp_name'])) {
                $this->error = "Unable to proceed with import. Please check the csv file.";
            }
        }
//        echo $this->error;

        if ($this->error != "") {
            $this->view();
        }

        $fileContents = file_get_contents($_FILES['file']['tmp_name']);
        $fileContents = iconv('ISO-8859-15', 'UTF-8', $fileContents);
	//print_r($fileContents);
        $fileContents = explode("\n", $fileContents);
        if (count($fileContents) == 0) {
            $fileContents = explode("\r\n", $fileContents);
        }if (count($fileContents) == 0) {
            $fileContents = explode("\025", $fileContents);
        }



        $eventId = trim($_POST['event_id']);
        $batchId = md5($eventId) . date('YmdHis');

        $method = "insert";
        $guests = array_map('str_getcsv', $fileContents);
        $guestsSvc = new guestsServices();

        global $GLOBAL_CONFIG, $DB, $APPVARS;
        $user = $APPVARS->user;

        $fields = $guestsSvc->getTableFields([], $method);
        $fields['batch_id'] = $batchId;
        $fields['event_id'] = $eventId;
        $fields['status'] = 'unregistered';
        $fields['registered_date'] = date('Y-m-d H:i:s');

        $importError = array();
        $importOK = 0;
        $emptyCode = 0;

        foreach ($guests as $i => $guest) {
            $fields['code'] = isset($guest[0]) ? trim($guest[0]) : ''; //$guestsSvc->generateBarcode($eventId);
            $fields['nric'] = isset($guest[1]) ? trim($guest[1]) : '';
            $fields['fname'] = isset($guest[2]) ? trim($guest[2]) : '';
            $fields['mname'] = isset($guest[3]) ? trim($guest[3]) : '';
            $fields['lname'] = isset($guest[4]) ? trim($guest[4]) : '';
            $fields['email'] = isset($guest[5]) ? trim($guest[5]) : '';
            $fields['address'] = isset($guest[6]) ? trim($guest[6]) : '';
            $fields['contact'] = isset($guest[7]) ? trim($guest[7]) : '';
            $fields['tableno'] = isset($guest[8]) ? trim($guest[8]) : '';

            $guestId = $DB->insert("INSERT INTO $guestsSvc->tableName SET " . parseArrayToFields($fields));
            if ($guestId == "") {
                appServices::auditLog($user['name'], 'Unable to import Line No', 'guest', 'importGuest');
                array_push($importError, "Unable to import Line No. $i.");
            } else {
                $importOK++;
            }

            if ($guest[0] == "") {
                $emptyCode++;
            }
        }

        if ($importOK == 0) {
            appServices::auditLog($user['name'], 'No records imported.', 'guest', 'importGuest');
            $this->error = "No records imported. Please try again...";
            $this->view();
        }

        if ($emptyCode > 0) {
            //re-generate the code for this batch of import for those empty code
            $uncodedGuests = $DB->find("SELECT * FROM $guestsSvc->tableName WHERE code='' && batch_id='$batchId';");
            if (count($uncodedGuests) > 0) {
                $res = $DB->count("SELECT COUNT(*) AS total FROM guests WHERE event_id=$eventId;", "total");
                $totalGuests = isset($res['total']) ? $res['total'] : 0;

                foreach ($uncodedGuests as $guest) {
                    $guest = (array) $guest;
                    $totalGuests++;
                    $code = str_pad($totalGuests, $GLOBAL_CONFIG->barcodeLength, '0', STR_PAD_LEFT);
                    $DB->execute("UPDATE $guestsSvc->tableName SET code='$code' WHERE id='" . $guest['id'] . "';");
                }
            }
        }

        appServices::auditLog($user['name'], 'Successfully imported $importOK records.', 'guest', 'importGuest');
        appServices::addFlashMessage("Successfully imported $importOK records.", "success");
        $this->redirect("guests/?eid=$eventId");
    }

// import function

    function form($isSave = false) {
        if ($isSave == false) {
            $this->guestId = isset($_GET['id']) ? trim($_GET['id']) : "";
            $this->eventId = isset($_GET['event_id']) ? trim($_GET['event_id']) : "";
            ;
        } else {
            $this->guestId = isset($_POST['id']) ? trim($_POST['id']) : "";
            $this->eventId = isset($_POST['event_id']) ? trim($_POST['event_id']) : "";
            ;
        }

        $guestSvc = new guestsServices();
        $eventSvc = new eventsServices();

        //get all the list of events 
        $this->events = $eventSvc->loadAll();
        $this->method = "insert";

        if ($this->guestId != "") {
            //load the guest detail
            $this->guest = (array) $guestSvc->load(" id=$this->guestId ");

            //load the event detail
            if (count($this->guest) > 0) {
                $this->eventId = $this->guest['event_id'];
                $this->event = (array) $eventSvc->load(" id=$this->eventId ");
            }

            $this->post = $this->guest;
            $this->method = "edit";
        }

        $this->view(array(
            "view" => VIEWS . "guests/form.php"
        ));
    }

// form function

    function save() {

//        if (appServices::isPostMethod() == false) {
//            appServices::addFlashMessage("You are not allowed to access this page.", "error");
//            $this->redirect("profile");
//        }

        global $GLOBAL_CONFIG, $DB, $APPVARS;

        //proceed with saving the event details
        $guestSvc = new guestsServices();
        $eventSvc = new eventsServices();

        $this->post = $_POST;
        $this->error = "";
        $method = @$this->post['id'] != "" ? "update" : "insert";
        $this->guestId = @$this->post['id'];
        $user = $APPVARS->user;
    

        $userRole = $user['user_level'];
        //$userRole ="operator";
        //validation of required fields 
        if (@$this->post['fname'] == "" || @$this->post['status'] == "" || @$this->post['event_id'] == "" || @$this->post['code'] == "") {
            $this->error = "Code, First & Last Name are required.";
        }

        if (@$this->post['status'] == "verified" && (@$this->post['app_id'] == "" || @$this->post['station'] == "")) {
            $this->error = "App ID & Station are required for verified status guest.";
        }

        //additional checking make sure the code is unique
        if ($guestSvc->isDuplicateCode(@$this->post['code'], @$this->post['event_id'], @$this->post['id']) == true) {
            $this->error = "Code already exists. Please change to another code.";
        }

        if ($this->error != "") {
            $this->form(true);
            exit;
        }

        $fields = $guestSvc->getTableFields($this->post, $method);

        unset($fields['id']);
        $successMsg = "";

        if ($method == "insert") {
             if(@$this->post['code']){
                $fields['code'] = $this->post['code']; 
             }else{
                $fields['code'] = $guestSvc->generateBarcode(trim($this->post['event_id']));  
             }
            $fields['registered_date'] = date('Y-m-d H:i:s');
            $guestId = $DB->insert("INSERT INTO $guestSvc->tableName SET " . parseArrayToFields($fields));
            $isUpdated = $guestId !== false ? true : false;
            $this->guestId = $guestId;
            appServices::auditLog($user['name'], 'New guest has been successfully created', 'guest', 'Insert');
            $successMsg = "New guest has been successfully created!";
        } else {
            if ($userRole == 'operator') {
                appServices::addFlashMessage("You don't have permission to edit the information. Please contact Administrator.", "error");
                $this->form();
            }
//            unset($fields['code']);
            unset($fields['batch_id']);
            unset($fields['registered_date']);
            appServices::auditLog($user['name'], 'You have successfully modified this guest', 'guest', 'Update');

            $fields['modified_date'] = date('Y-m-d H:i:s');
            $eventId = trim($this->post['id']);
            $isUpdated = $DB->execute("UPDATE $guestSvc->tableName SET " . parseArrayToFields($fields) . " WHERE id=$this->guestId");
            $successMsg = "You have successfully modified this guest!";
        }

        if ($isUpdated == false) {
            appServices::addFlashMessage("Problem encounter while saving your changes. Please try again.", "error");
            $this->form();
        }

        appServices::addFlashMessage($successMsg, "success");
        $this->redirect("guests/details?id=$this->guestId");
    }

    function details() {
        $id = isset($_GET['id']) ? trim($_GET['id']) : "";
        if ($id == "") {
            appServices::addFlashMessage("Invalid requests", "error");
            $this->redirect("profile");
        }

        $guestSvc = new guestsServices();
        $this->post = $guestSvc->load(" id=$id");
        $this->view();
    }

    function deleteGuest() {
        global $APPVARS;
        $guestId = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : "";
        $user = $APPVARS->user;
        $userRole = $user['user_level'];
        if ($userRole == 'customer' || $userRole == 'operator' ) {
            appServices::addFlashMessage("You dont have the right to delete guest.", "info");
            $this->redirect("guests");
        }
        if ($guestId == "") {
            $this->redirect("guests");
        }

        $eventId = isset($_REQUEST['eid']) ? trim($_REQUEST['eid']) : "";

        //proceed with the DB delete
        global $DB;
        $DB->execute("DELETE FROM guests WHERE id='$guestId';");
        appServices::auditLog($user['name'], 'Guest has been deleted successfully', 'guest', 'Delete');

        appServices::addFlashMessage("Guest has been deleted successfully.", "success");
        $this->redirect("guests?eid=$eventId");
    }

}
?>

