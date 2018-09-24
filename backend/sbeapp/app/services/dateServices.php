<?php
class dateServices{

    function __construct() {
        //$this->app = new app();
        $this->defaultFormat = 'Y-m-d H:i:s' ;
    }

    function dateAdd( $int=0, $format='', $date='' ){
        if( !is_numeric($int) ){ return date($this->defaultFormat);   }
        $ndate = $int>0 ? date($this->defaultFormat, strtotime("+$int days")) : date($this->defaultFormat) ;
        if( $format=="toISOString" ){
            $d = new DateTime( $ndate );
            return $d->format(DateTime::ISO8601);
        }else if( $format=="" ){
            return $ndate;
        }
        return $this->format( $ndate, $format, false );
    }
    
    function getInterval( $dateFrom, $calc=true, $dateNow='' ){
        
        $dateFrom = new DateTime($dateFrom);
        $dateTo = new DateTime($dateNow==''?'now':$dateNow);
        $interval = $dateFrom->diff($dateTo); //new DateTime('5 days ago') - 5 days ago date
        
        if( $calc==false ){
            return $interval;
        }
        
//        $interval->y = 0 ;
//        $interval->m = 0 ;
//        $interval->d = 0 ;
                
        if( $interval->y>=1 || ($interval->y==0 && $interval->m>1) ){ //if interval is >1yr, more than 2 months etc, display the actual date
            return "" ;
        }
        
        if( ($interval->m==1 && $interval->d==0) || ($interval->m==0 && ( $interval->d>=28 && $interval->d<=31 )) ){
            return "a month ago";
        }else if( ($interval->m==1 && $interval->d>=1) || ($interval->m==0 && $interval->d>=32) ){
            return "" ;
        }
        
        if( $interval->d>=7 ){ //if >= 7 days, display in # of weeks
            $weeksInt = round($interval->d/7) ;
            return $weeksInt==1 ? 'a week ago' : $weeksInt .' weeks ago' ;
        }else if( $interval->d>=2 && $interval->d<=6 ){    //less than 7 days or 1 week, display number of days.
            return $interval->d .' days ago' ;
        }else if( $interval->d==1 ){    //if 1 day interval, use yesterday
            return ' yesterday' ;
        }else{ //today
            return ' today' ;
        }
        
        return $interval;
    }
    
    function formatDate($date, $format='dS M y'){//eg : o/p format 11th Sep 14
//        $date = new DateTime($date);
//        print_r($date->format($format));
//        return $date->format($format); 
        echo getMongoDate($date) ;
        return $date ;
    }
    
    function format( $date, $format="D d M Y @ H:i"){
//        if( gettype($date)=="object" ){
//            $timestamp = date($this->defaultFormat, $date->sec);
//        }else{
//            $timestamp = date_create($date);
//        }
        
        $returnDate = date($format, strtotime($date));
        return $returnDate ;
    }
    public static function checkInterval($expiration,$returnType='day'){
        
        if( gettype($expiration)=="object" ){
            $expiration = date('Y-m-d H:i:s', $expiration->sec) ;
        }
        
        $dateFr = new DateTime($expiration);
        $dateNow = new DateTime('now');

        $interval = $dateNow -> diff($dateFr );
      
        if($returnType ==='hours'){
            $v = $interval->h ;
            $d = $interval->d;
            $hrs = ($d * 24) + $v;
            return $hrs;
        }
        if($returnType ==='day'){
            $v = $interval->d ;
            return $v;
        }
         
    }

}

?>