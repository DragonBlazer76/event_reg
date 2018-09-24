<?php

class dbServices {

    function __construct() {
    }

    function getTableFields( $values=[], $method="" ){
        global $DB;
     
        $fields = array_fill_keys($DB->GetColumnNames( $this->tableName ), '');
        foreach( $values as $field=>$value ){
            if( isset($fields[$field]) ){
                $fields[$field] = $value;
            }
        }
        
        $uId = userServices::getMemberId();
        $dateNow = date('Y-m-d H:i:s');
        
        if( $method=="insert" && isset($fields['created_date']) ){
            $fields['created_date'] = $dateNow;
        }
        if( $method=="insert" && isset($fields['created_by']) ){
            $fields['created_by'] = $uId;
        }
        if( $method=="update" ){
            unset($fields['created_by']);
            unset($fields['created_date']);
            if( isset($fields['modified_date']) )
                $fields['modified_date'] = $dateNow;
        }
        if( $method=="update" && isset($fields['modified_by']) ){            
            $fields['modified_by'] = $uId;
        }

        if( isset($fields['id']) )
            unset($fields['id']);
        
        return $fields;
    }
    
}

?>