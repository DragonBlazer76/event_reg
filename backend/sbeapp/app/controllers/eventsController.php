<?php

class eventsController extends app {

    function __construct() {
        parent::__construct();
    }

    function index() {
        global $APPVARS, $DB;
        $user = $APPVARS->user;
        $userRole = $user['user_level'];
        if ($userRole == 'customer') {
            appServices::addFlashMessage("You dont have the right to access this section.", "info");
            $this->redirect("guests");
        }
        //get the total number of guests registed in the DB
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
        //$fields =array('id', 'name','code','description','type','status',);
        if ($this->totalEvents > 0) {
            $eventSvc = new eventsServices();
            $this->events = $eventSvc->loadAll(1);
        }
        $this->view();
    }

//index

    function deleteEvent() {
        global $DB, $APPVARS;
        $user = $APPVARS->user;
        $userRole = $user['user_level'];
        
        if ($userRole == 'customer' || $userRole == 'operator') {
            appServices::addFlashMessage("You dont have the right to delete the Event.", "info");
            $this->redirect("guests");
        }
        $eventId = isset($_REQUEST['id']) ? trim($_REQUEST['id']) : "";
        if ($eventId == "") {
            $this->redirect("events");
        }

        //proceed with the DB delete
        $DB->execute("DELETE FROM reference WHERE id='$eventId';");
        $DB->execute("DELETE FROM guests WHERE event_id='$eventId';");
        appServices::auditLog($user['name'], 'Event has been deleted successfully', 'event', 'Delete');
        appServices::addFlashMessage("Event has been deleted successfully.", "success");
        $this->redirect("events");
    }

    function details() {
        $id = isset($_GET['id']) ? trim($_GET['id']) : "";
        if ($id == "") {
            appServices::addFlashMessage("Invalid requests", "error");
            $this->redirect("profile");
        }

        $eventSvc = new eventsServices();
        $this->post = $eventSvc->load(" id=$id");
        $this->view();
    }

    function form() {
        global $APPVARS, $DB;
        $user = $APPVARS->user;
        if ($userRole == 'customer' ) {
            appServices::addFlashMessage("You dont have the right to access this section.", "info");
            $this->redirect("guests");
        }
        $id = isset($_GET['id']) ? trim($_GET['id']) : "";
        $this->method = "insert";

        if ($id != "") {
            $this->method = "edit";
            $eventSvc = new eventsServices();
            $this->post = $eventSvc->load("id=$id");
            $this->post['eventdates'] = $this->post['start_date'] . ' - ' . $this->post['end_date'];
        }
        $this->view();
    }

    function save() {
        global $APPVARS, $DB;  global $GLOBAL_CONFIG, $DB, $APPVARS;
        $user = $APPVARS->user;
       $userRole = $user['user_level'];
        if ($userRole == 'customer' ) {
            appServices::addFlashMessage("You dont have the right to access events action.", "info");
            $this->redirect("guests");
        }

        if (appServices::isPostMethod() == false) {
            appServices::addFlashMessage("You are not allowed to access this page.", "error");
            $this->redirect("profile");
        }

        $this->post = $_POST;
        $this->error = "";

        //validation of required fields 
        if (@$this->post['name'] == "") { //@$this->post['appid']=="" || 
            $this->error = "Event's AppID & Name are both required.";
        }

        if ($this->error != "") {
            $this->form();
        }

        $method = @$this->post['id'] != "" ? "update" : "insert";

      

        //proceed with saving the event details
        $eventSvc = new eventsServices();
        $fields = $eventSvc->getTableFields($this->post, $method);
//        $fields['ref_id'] = trim($this->post['appid']);
        $fields['status'] = 'published';
        $fields['type'] = 'event';
               //$userRole ="operator";

        if ($this->post['eventdates'] != "") {
            $eventdates = explode(" - ", trim($this->post['eventdates']));
            $fields['start_date'] = trim($eventdates[0]);
            $fields['end_date'] = isset($eventdates[1]) && $eventdates[1] != "" ? trim($eventdates[1]) : $fields['start_date'];
        }

        if ($method == "insert") {
            $fields['code'] = randomValueBase64($GLOBAL_CONFIG->codeLength);
            $eventId = $DB->insert("INSERT INTO $eventSvc->tableName SET " . parseArrayToFields($fields));
            $isUpdated = $eventId !== false ? true : false;
            appServices::auditLog($user['name'], 'You have successfully created new event', 'event', 'Insert');
            appServices::addFlashMessage("You have successfully created new event.", "success");
        } else {

            unset($fields['code']);
            unset($fields['created_by']);
            unset($fields['created_date']);

            $eventId = trim($this->post['id']);
            if ($userRole == 'operator') {
                //events/edit/?id=6
                appServices::addFlashMessage("You don't have permission to edit the information. Please contact Administrator.", "info");
                $this->redirect("events/edit?id=$eventId");
                $this->form();
            }
            $isUpdated = $DB->execute("UPDATE $eventSvc->tableName SET " . parseArrayToFields($fields) . " WHERE id=$eventId");
            appServices::auditLog($user['name'], 'You have successfully updated an event', 'event', 'Update');
            appServices::addFlashMessage("You have successfully updated an event.", "success");
        }

        if ($isUpdated == false) {
            appServices::auditLog($user['name'], 'Problem encounter while saving your changes', 'event', $eventId);
            appServices::addFlashMessage("Problem encounter while saving your changes. Please try again.", "error");
            $this->form();
        }


        $this->redirect("events/details?id=$eventId");
    }

    function getEventLists() {
        global $APPVARS, $DB;

        $results = $DB->find("SELECT R.*, 
            (SELECT COUNT(*) FROM guests G WHERE G.event_id=R.id) AS guests_total FROM reference AS R 
            ORDER BY R.modified_date DESC, R.created_date DESC, R.name ASC");

        if (count($results) > 0) {
            $formattedRows = array();
            $dateSvc = new dateServices();
            foreach ($results as $row) {
                $row = (array) $row;
                $row['start_date_f'] = $dateSvc->format($row['start_date'], 'd M Y');
                $row['end_date_f'] = $dateSvc->format($row['end_date'], 'd M Y');
                $row['created_date_f'] = $dateSvc->format($row['created_date'], 'd M Y');
                $row['guests_total'] = number_format($row['guests_total']);
                $row['edit_link'] = '<a href="' . $APPVARS->siteUrl . 'events/edit/?id=' . $row['id'] . '"><i class="fa fa-pencil"></i></a>';
                $row['delete_link'] = '<a href="javascript:confirmDeleteEvent(\'' . $row['name'] . '\',\'' . $row['guests_total'] . '\',' . $row['id'] . ');"><i class="fa fa-trash"></i></a>';
                array_push($formattedRows, $row);
            }
            $results = $formattedRows;
        }
        $this->addOutput('lists', $results);

        $this->printOutput();
    }

}

?>