<?php

class guestsServices extends dbServices {

    function __construct() {
        $this->tableName = "guests";
    }

    function isDuplicateCode( $code, $eventId, $guestId=0 ){
        global $DB;
        $row = (array) $DB->findOne("SELECT * FROM guests WHERE code='$code' AND event_id='$eventId';");
        if( !$row ){
            return false;
        }
        //if exists check based on the guest Id
        if( $guestId==0 ){
            return false;
        }
        return $row['id']==$guestId ? false : true;
    }
    
    function generateBarcode( $eventId, $codeLength=0 ){
        global $GLOBAL_CONFIG, $DB;
        if( $eventId==0 ){
            return randomValueBase64($GLOBAL_CONFIG->codeLength);
        }
        $codeLength = $codeLength==0 ? $GLOBAL_CONFIG->barcodeLength : $codeLength;
        
        $res = $DB->count("SELECT COUNT(*) AS total FROM guests WHERE event_id=$eventId;", "total");
        if( isset($res['total']) )
            $totalGuests = $res['total']++;
        else
            $totalGuests = 1;
        return str_pad($totalGuests, $codeLength, '0', STR_PAD_LEFT);
    }
    
    function load($query, $sanitize = true) {
        global $DB;
        $row = (array) $DB->findOne("SELECT * FROM $this->tableName WHERE $query;");
        if ($sanitize) {
            $row = $this->sanitize($row);
        }
        return $row;
    }

    function loadAll($query = 1, $fields = '*', $sanitize = true) {
        global $DB;
        $rows = (array) $DB->find("SELECT $fields FROM $this->tableName WHERE $query;");
        if (count($rows) > 0 && $sanitize == true) {
            $sRows = array();
            foreach ($rows as $row) {
                array_push($sRows, $this->sanitize((array) $row));
            }
            return $sRows;
        }
        return $rows;
    }

    function loadGuest($query,$eId) {
        global $DB;
        //$result = $DB->findOne("SELECT g.event_id,r.name FROM guests g inner join reference r on g.event_id = r.id where g.code= '".$code."'");
        if($query=='all'){
            $rows = (array) $DB->find("SELECT g.fname,g.mname,g.lname,g.event_id,g.code,g.station,g.tableno,g.registered_date,r.name,r.description,g.status FROM $this->tableName  g inner join reference r on g.event_id = r.id  where g.event_id = $eId;");
        }else{
           $rows = (array) $DB->find("SELECT g.fname,g.mname,g.lname,g.event_id,g.code,g.station,g.tableno,g.registered_date,r.name,r.description,g.status FROM $this->tableName  g inner join reference r on g.event_id = r.id and g.status='".$query."' and g.event_id = $eId;");
        }

        if (count($rows) > 0 ) {
            $sRows = array();
            foreach ($rows as $i=>$row) {
                $row->sno = $i+1;
                array_push($sRows, $this->loadsanitize((array) $row));
            }
            
            return $sRows;
        }
       
    }

    function sanitize($row) {
        $dateSvc = new dateServices();

        if (isset($row['start_date']))
            $row['start_date_f'] = $dateSvc->format($row['start_date']);
        if (isset($row['end_date']))
            $row['end_date_f'] = $dateSvc->format($row['end_date']);
        if (isset($row['created_date']))
            $row['created_date_f'] = $dateSvc->format($row['created_date']);
        if (isset($row['modified_date']))
            $row['modified_date_f'] = $dateSvc->format($row['modified_date']);

        global $APPVARS;
        if (isset($row['created_by']) && $APPVARS->user['id'] == $row['created_by']) {
            $row['created_by_name'] = $APPVARS->user['name'];
        } else {
            $row['created_by_name'] = "";
        }

        if (isset($row['modified_by']) && $APPVARS->user['id'] == $row['modified_by']) {
            $row['modified_by_name'] = $APPVARS->user['name'];
        } else {
            $row['modified_by_name'] = "";
        }

        return $row;
    }

    function loadsanitize($row) {
        $dateSvc = new dateServices();
        $fields = array();
        $fields['sno']= isset($row['sno']) ? $row['sno'] : '';
        $strspecialchar =  array(",");
        if (isset($row['fname']) || isset($row['mname']) || isset($row['lname'])) {
            $fields['visitorName'] = str_replace($strspecialchar, " ", @$row['fname']);
        }
        if (isset($row['mname'])) {
            $fields['company'] = str_replace($strspecialchar, " ", $row['mname']);
        }
        if (isset($row['lname'])) {
            $fields['designation'] = str_replace($strspecialchar, " ", $row['lname']);
        }
        if (isset($row['code'])) {
            $fields['code'] = $row['code'];
        }
        if (isset($row['status'])) {
            $fields['status'] = $row['status'];
        }
        if (isset($row['event_id'])) {
            $fields['eventId'] = $row['event_id'];
        }
        if (isset($row['name'])) {
            $fields['eventName'] = $row['name'];
        }
        if (isset($row['description'])) {
            $fields['description'] = $row['description'];
        }
        if (isset($row['registered_date'])) {
            $fields['eventDate&time'] = $dateSvc->format($row['registered_date']);
        }
      
        $fields['tableno']= isset($row['tableno']) ? $row['tableno'] : 'NULL';
        $fields['station']= isset($row['station']) ? $row['station'] : 'NULL';
//        if (isset($row['tableno'])) {
//            $fields['tableno'] = $row['tableno'];
//        }
//        if (isset($row['station'])) {
//            $fields['station'] = $row['station'];
//        }
        return $fields;
    }

}

?>
