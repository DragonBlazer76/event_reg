<?php

class webController extends app {

    function __construct() {
        parent::__construct();
    }

    function index() {
        global $APPVARS, $DB;
        $now = date('Y-m-d H:i:s');
        $result = $DB->findOne("SELECT * FROM guests ");
        //var_dump($result);
        $this->view();
    }

//index
    function getapp() {
        $file = '/public_html/sbeapp/app/controllers/eWerkzEventApp.apk'; //not public folder
        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.android.package-archive');
            header('Content-Disposition: attachment; filename=' . basename($file));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
        }
    }

    function getListSummary() {
        global $APPVARS, $DB;

        $eventId = isset($_POST['eventId']) ? trim($_POST['eventId']) : 0;

        $results = $DB->findOne("SELECT a.total, b.registered, c.unregistered, d.logout FROM 
            (SELECT 1 AS id, COUNT(*) AS total FROM `guests` WHERE event_id='$eventId') a INNER JOIN
            (SELECT 1 AS id, COUNT(*) AS registered FROM `guests` WHERE event_id='$eventId' AND `status`='registered') b ON a.id=b.id INNER JOIN
            (SELECT 1 AS id, COUNT(*) AS unregistered FROM `guests` WHERE event_id='$eventId' AND `status`='unregistered') c ON a.id=c.id INNER JOIN
            (SELECT 1 AS id, COUNT(*) AS logout FROM `guests` WHERE event_id='$eventId' AND `status`='logout') d ON a.id=d.id;");

        $this->addOutput('results', $results);
        $this->printOutput();
    }

//end getListSummary

    function getLists() {
        global $APPVARS, $DB;
        $qWhere = '1';
        if (@$_POST['eventId'] != "") {
            $qWhere = ' event_id=' . trim($_POST['eventId']);
        }
        $results = $DB->find("SELECT * FROM guests WHERE $qWhere");
        if (count($results) > 0) {
            $formattedRows = array();
            $dateSvc = new dateServices();
            foreach ($results as $row) {
                $row = (array) $row;
                $row['created_date_f'] = $dateSvc->format($row['created_date'], 'd M Y H:i');
                if (substr($row['registered_date'], 0, 4) == '0000') {
                    $row['registered_date_f'] = $row['created_date_f'];
                } else {
                    $row['registered_date_f'] = $dateSvc->format($row['registered_date'], 'd M Y H:i');
                }
                $row['edit_link'] = '<a href="' . $APPVARS->siteUrl . 'guests/edit/?id=' . $row['id'] . '"><i class="fa fa-pencil"></i></a>';
                $row['delete_link'] = '<a href="javascript:confirmDeleteGuest(\'' . $row['fname'] . ' ' . $row['lname'] . '\',' . $row['id'] . ');"><i class="fa fa-trash"></i></a>';
                array_push($formattedRows, $row);
            }
            $results = $formattedRows;
        }
        $this->addOutput('lists', $results);
        $this->printOutput();
    }

    function setguestlogout() {
        global $APPVARS, $DB;
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        $eventid = $input['event_id'];
        $code = $input['code'];
        $dateNow = date('Y-m-d H:i:s');

        if (strlen($code) > 4) {
            $query = $DB->update("UPDATE  guests SET status = 'logout', registered_date='$dateNow' WHERE nric = '" . $code . "' and event_id = '" . $eventid . "';");
        } else {
            $query = $DB->update("UPDATE  guests SET status = 'logout', registered_date='$dateNow' WHERE code = '" . $code . "' and event_id = '" . $eventid . "';");
        }
        if ($query == true) {
            $result = array("response" => "true");
            echo json_encode($result);
            appServices::auditLog('Mobile', 'SuccessFully Updated Status Logged Out', 'webservice', 'setgueststatus');
            exit;
        } else {
            $result = array("response" => "Problem encounter while update your changes. Please try again.");
            appServices::auditLog('Mobile', 'Problem encounter while update your changes.', 'webservice', 'setgueststatus');
            echo json_encode($result);
            exit;
        }
    }

    function setgueststatus() {
        global $APPVARS, $DB;
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        //$guestId = $input['guest_id'];
        $eventid = $input['event_id'];
        $code = $input['code'];
        $validation = webServices::validation($input);
        $validation['status'] = true;
        $appid = $input['app_id'];
        $user = $APPVARS->user;
        $dateNow = date('Y-m-d H:i:s');

        if ($validation['status'] == true) {
            if (strlen($code) > 6) {
                //for NRIC scan
                $resGuest = $DB->find("SELECT CONCAT(fname, ' ', mname, ' ', lname) as gname, tableno FROM guests where nric = '" . $code . "' and event_id = '" . $eventid . "';");
                if (count($resGuest) == 0) {
                    $result = array("response" => "NA");
                    appServices::auditLog('Mobile', 'NO Event Found in this code NRIC:'.$code, 'webservice', 'setgueststatus');
                    echo json_encode($result);

                    exit;
                }
                $resReg = $DB->find("SELECT CONCAT(fname, ' ', mname, ' ', lname) as gname, tableno FROM guests where nric = '" . $code . "' and status = 'registered' and event_id = '" . $eventid . "';");
                if (count($resReg) > 0) {
                    $result = array("response" => "REG|".json_encode($resReg));
                    appServices::auditLog('Mobile', 'NO Event Found in this code and status registered', 'webservice', 'setgueststatus');
                    echo json_encode($result);
                    exit;
                }
                $query = $DB->update("UPDATE guests SET status = 'registered', registered_date='$dateNow', app_id = '" . $appid . "' WHERE nric = '" . $code . "' and event_id = '" . $eventid . "';");
            } else {
                //for normal barcode scan
                $resGuest = $DB->find("SELECT CONCAT(fname, ' ', mname, ' ', lname) as gname, tableno FROM guests where code = '" . $code . "' and event_id = '" . $eventid . "';");
                if (count($resGuest) == 0) {
                    $result = array("response" => "NA");
                    appServices::auditLog('Mobile', 'NO Event Found in this code=>barcode scan method', 'setgueststatus', $code);
                    appServices::auditLog('Mobile', 'SELECT gname, tableno FROM guests where code = '. $code .'and event_id ='.$eventid, 'setgueststatus', $code);
		    echo json_encode($result);
                    exit;
                }
                $resReg = $DB->find("SELECT CONCAT(fname, ' ', mname, ' ', lname) as gname, tableno FROM guests where code = '" . $code . "' and status = 'registered' and event_id = '" . $eventid . "';");
                if (count($resReg) > 0) {
                    $result = array("response" => "REG|".json_encode($resReg));
                    appServices::auditLog('Mobile', 'NO Event Found in this code and status registered=>barcode scan method', 'setgueststatus', $code);
                    echo json_encode($result);
                    exit;
                }
                $query = $DB->update("UPDATE guests SET status = 'registered', registered_date='$dateNow', app_id = '" . $appid . "' WHERE code = '" . $code . "' and event_id = '" . $eventid . "';");
            }
        }
        if ($query == true) {
            $result = array("response" => "true|" . json_encode($resGuest));
            appServices::auditLog('Mobile', 'SuccessFully Updated Status Registered', 'webservice', 'setgueststatus');
            echo json_encode($result);
            exit;
        } else {
            $result = array("response" => "Problem encounter while update your changes. Please try again.");
            appServices::auditLog('Mobile', 'Problem encounter while update your changes', 'setgueststatus', $code);
            echo json_encode($result);
            exit;
        }
    }

    function geteventdetails() {
        global $APPVARS, $DB;
        $inputJSON = file_get_contents('php://input');
        $input = json_decode($inputJSON, TRUE);
        $guestId = $input['guest_id'];
        $code = $input['code'];
        $user = $APPVARS->user;

        //$code = "ERnMWRxRDxjW1k1";
        //$validation = webServices::validation($input);
        $validation['status'] = true;
        if ($validation['status'] == true) {
            // $result = $DB->findOne("SELECT g.event_id,r.name FROM guests g inner join reference r on g.event_id = r.id where g.code= '" . $code . "'");
            $result = $DB->find("SELECT id, name from reference;");
        }
        if (count($result) > 0) {
            appServices::auditLog('Mobile', 'SuccessFully exported Event Details', 'webservice', 'geteventdetails');
            echo json_encode($result);
            exit;
        } else {
            appServices::auditLog('Mobile', "Event not found in this $code", 'webservice', 'geteventdetails');
            echo 'Event not found in this' . $code;
            exit;
        }
    }

    function pdfreport() {
        global $APPVARS, $DB;
        $eventId = @$_REQUEST['event_id'];
        require_once LIBRARIES . "pdf/html2pdf.class.php";
        global $APPVARS, $DB;
        $dateSvc = new dateServices();
        $eventSvc = new eventsServices();

        ini_set('max_execution_time', 300);

//        ini_set('memory_limit', '-1');
//        ini_set('memory_limit', '256M'); 
        //generate random filename
        $fileName = randomValueBase64(9, 'admin@domain.com');

        //get the event details
        $event = $eventSvc->load(" id=$eventId");
        $event['start_date_f'] = $dateSvc->format($event['start_date'], 'd M Y');
        $event['end_date_f'] = $dateSvc->format($event['end_date'], 'd M Y');
        $_POST['eventId'] = $eventId;

        //load all the guests
        $allGuests = $this->getLists(true, 2000);

        ob_start();
        $content = '<page style="font-size: 13px" ><table>
                <tr>
                    <td>Event Name</td>
                    <td>' . @$event['name'] . '</td>
                </tr>
                <tr>
                    <td>Description</td>
                    <td>' . @$event['description'] . '</td>
                </tr>
                <tr>
                    <td>Start / End Date</td>
                    <td>' . @$event['start_date_f'] . ' / ' . @$event['end_date_f'] . '</td>
                </tr>
            </table><br /><br />
            
            <table border="0" cellpadding="5" cellspacing="0" width="100%" style="width: 100%; border: solid 1px #ccc;">
                <thead>
                <tr>
                    <th style="width:7%; border: solid 1px #ccc;">Code</th>
                    <th style="width:15%; border: solid 1px #ccc;">First Name</th>
                    <th style="width:15%; border: solid 1px #ccc;">Last Name</th>
                    <th style="width:15%; border: solid 1px #ccc;">NRIC</th>
                    <th style="width:10%; border: solid 1px #ccc;">Station</th>
                    <th style="width:7%; border: solid 1px #ccc;">Status</th>
                    <th style="width:7%; border: solid 1px #ccc;">Registered</th>
                    <th style="width:7%; border: solid 1px #ccc;">Verified</th>
                </tr>
                </thead>
                <tbody>{{LIST_GUESTS}}</tbody>
            </table>
            </page>';

        $guestsList = array();
        foreach ($allGuests as $g) {
            $g = (array) $g;
            array_push($guestsList, '<tr>
                    <td style="border: solid 1px #ccc;">' . @$g['code'] . '</td>
                    <td style="border: solid 1px #ccc;">' . @$g['fname'] . '</td>
                    <td style="border: solid 1px #ccc;">' . @$g['lname'] . '</td>
                    <td style="border: solid 1px #ccc;">' . @$g['nric'] . '</td>
                    <td style="border: solid 1px #ccc;">' . @$g['station'] . '</td>
                    <td style="border: solid 1px #ccc;">' . @$g['status'] . '</td>
                    <td style="border: solid 1px #ccc;">' . substr(@$g['registered_date'], 0, 10) . '</td>
                    <td style="border: solid 1px #ccc;">' . substr(@$g['created_date'], 0, 10) . '</td>
                </tr>');
        }

        $content = str_replace('{{LIST_GUESTS}}', implode('', $guestsList), $content);

        try {
            $html2pdf = new HTML2PDF('P', 'A4', 'fr', true, 'UTF-8', array(10, 10, 10, 10));
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->writeHTML($content);
            $html2pdf->Output("$fileName.pdf");
        } catch (HTML2PDF_exception $e) {
            echo $e;
            exit;
        }
    }

//pdfreport

    function generatereport() {
        global $APPVARS, $DB;
        $status = @$_REQUEST['method'];
        $eventId =@$_REQUEST['event_id']; 
        $sRows = array();
        $labelheader = array('S/No', 'Guest Name', 'Company Name', 'Designation', 'Code','Status','EventId', 'Event Name', 'Event Description', 'Event Date', 'TableNo','Station');
        $guestSvc = new guestsServices();
        //$query  = $status == 'all' ? 1 : $status;
        $type = @$_REQUEST['type'];
        $this->report = $guestSvc->loadGuest($status,$eventId);
      
        $user = $APPVARS->user;
        if (count($this->report) > 0) {
            $fileName = randomValueBase64(9, 'admin@domain.com');
             //@header('Content-Length: ' . sizeof($this->report));
             //header(sprintf('Content-Length: %d', sizeof($this->report)));
            @header('Content-Disposition: attachment; filename=' . $fileName . '.' . $type);
            if ($type == 'pdf') {
                @header('Content-Type: application/pdf');
            } else {
                @header('Content-Type: text/csv');
                //# Don't use application/force-download - it's not a real MIME type, and the Content-Disposition header is sufficient
            }
//             var_dump($this->report);exit;
            //@header('Content-Length: ' . count($this->report));
//            header("Pragma: no-cache");
//            header("Expires: 0");
            $printLns = array();
            array_push($printLns, implode(",", $labelheader));
             
            foreach ($this->report as $row) {
                array_push($printLns, implode(",", $row));
            }
          
            appServices::auditLog($user['name'], 'SuccessFully generated report ', 'report', 'csv');
            echo implode("\r\n", $printLns);
        } else {
            appServices::addFlashMessage("No Data found.", "error");
            $this->redirect("guests");
        }
        //printArray($list);
        exit;
    }

//generatereport

    function auditlog() {
        global $APPVARS, $DB;
        $sRows = array();
        $user = $APPVARS->user;
        $userRole = $user['user_level'];
        if ($userRole == 'admin') {
            $result = $DB->find("SELECT * from auditlog order by date_created desc;");
            foreach ($result as $row) {
                array_push($sRows, (array) $row);
            }
            $this->result = $sRows;

            $this->view();
        } else {
            appServices::addFlashMessage("You don't have permission to view the auditlog. Please contact Administrator.", "error");
            $this->redirect("events");
        }
    }

}
?>

