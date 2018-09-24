<?php

class eventsServices extends dbServices{

    function __construct() {
        $this->tableName = "reference";      
    }

    function getFields( $type='' ){
        $fields = array('id','ref_id','type','code', 'name' , 'description','start_date','end_date', 'status' , 'created_by', 'created_date', 'modified_by', 'modified_date' ); 
        $dateNow = date('Y-m-d H:i:s');
        $uId = userServices::getMemberId();
        
        $fields['type'] = 'event';
        $fields['modified_by'] = $uId;
        $fields['modified_date'] = $dateNow;
        $fields['status'] = 'published';
        
        switch ($type) {
            case 'insert':
                $fields['created_by'] = $uId;
                $fields['created_date'] = $dateNow;
                unset($fields['id']);
                break;
            default:
                break;
        }
        return $fields;
    }
    
    function load( $query, $sanitize=true ){
        global $DB;    
        $row = (array) $DB->findOne("SELECT * FROM $this->tableName WHERE $query;");
        if( $sanitize ){
            $row = $this->sanitize( $row );
        }
        return $row;
    }
  
    function loadAll( $query=1, $fields='*', $sanitize=true ){
        global $DB;    
        $rows = (array) $DB->find("SELECT $fields FROM $this->tableName WHERE $query;");
        if( count($rows)>0 && $sanitize==true ){
            $sRows = array();
            foreach($rows as $row){
                array_push($sRows, $this->sanitize((array) $row));
            }
            return $sRows; 
        }
        return $rows;
    }
    
    function sanitize( $row ){
        $dateSvc = new dateServices();
        
        if( isset($row['start_date']) )
            $row['start_date_f'] = $dateSvc->format($row['start_date']);
        if( isset($row['end_date']) )
            $row['end_date_f'] = $dateSvc->format($row['end_date']);
        if( isset($row['created_date']) )
            $row['created_date_f'] = $dateSvc->format($row['created_date']);
        if( isset($row['modified_date']) )
            $row['modified_date_f'] = $dateSvc->format($row['modified_date']);
        
        global $APPVARS;
        if( isset($row['created_by']) && $APPVARS->user['id']==$row['created_by'] ){
            $row['created_by_name'] = $APPVARS->user['name'];
        }else{
            $row['created_by_name'] = "";
        }
           
        if( isset($row['modified_by']) && $APPVARS->user['id']==$row['modified_by'] ){
            $row['modified_by_name'] = $APPVARS->user['name'];
        }else{
            $row['modified_by_name'] = "";
        }
        
        return $row;
    }
    
}

?>
