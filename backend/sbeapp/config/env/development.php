<?php
global $environmentVars ;
$environmentVars = new stdClass();

$environmentVars->mysql = array(
        "host" => 'ewerkzmicro.ctohzjqt6mal.ap-southeast-1.rds.amazonaws.com',
        "port" => '',
        "username" => 'ewerkzevent',
        "password" => 'Event_1234',
        "database" => 'sbeventdb',
	"charset" => 'utf-8'
);
//$environmentVars->mysql = array(
//        "host" => 'localhost',
//        "port" => '',
//        "username" => 'root',
//        "password" => '',
//        "database" => 'sbeventdb'
//);

?>
