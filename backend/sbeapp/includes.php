<?php
error_reporting(E_ALL);
//ini_set("log_errors", 1);

$dirname = dirname($_SERVER["SCRIPT_NAME"]) ;
date_default_timezone_set('Singapore');
define("SITE_URL", (strlen($dirname)>2?$dirname:'') . "/");
define("ROOT", dirname(__FILE__) . "/");
define("CONFIG", ROOT . "config/");
define("APP", ROOT . "app/");
define("CLASSES", APP . "classes/");
define("DRIVERS", APP . "drivers/");
define("CONTROLLERS", APP . "controllers/");
define("SERVICES", APP . "services/");
define("HELPERS", APP . "helpers/");
define("TASKS", ROOT . "tasks/");
define("LOGS", ROOT . "logs/");
define("VIEWS", ROOT . "views/");
define("LIBRARIES", ROOT . "libraries/");
define("LANGUAGES", ROOT . "languages/");
define("ASSETS", "assets/");

global $APPVARS, $APPLANG, $APPDATA, $DB ;
$APPVARS = new stdClass();
$APPLANG = new stdClass();
$APPDATA = new stdClass();
$DB = new stdClass();

require_once( ROOT . "config.php");

global $GLOBAL_CONFIG;
ini_set('display_errors', $GLOBAL_CONFIG->debug);

require_once( APP . "app.php" );
?>