
<?php

function loadFiles($dir, $prefix = '', $path = '') {
    $arrFiles = array();
    $files = scandir($dir);
    $prefixLen = strlen($prefix);
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (($file != ".") && ($file != "..") && $ext == 'php') {
            require_once $dir . $file;
        } else if (($file != ".") && ($file != "..") && $ext == 'js') {
            if ($prefix != "" && strncmp($file, $prefix, $prefixLen) == 0) {
                //array_push($arrFiles, SITE_URL.$dir.$file);
                array_push($arrFiles, $path . $file);
            } else if ($prefix == "") {
                array_push($arrFiles, $path . $file);
            }
        } else if (($file != ".") && ($file != "..") && $ext == 'html') {
            array_push($arrFiles, $path . $file);
        } else if (($file != ".") && ($file != "..")) {
            array_push($arrFiles, pathinfo($file, PATHINFO_BASENAME));
        }
    }
    return $arrFiles;
}

//classes, functions and the application initialization
function __autoload($class) {
    $pathLoader = array(CLASSES, DRIVERS, CONTROLLERS, SERVICES);
    foreach ($pathLoader as $path) {
        $className = $path . $class;
        if (file_exists($className . '.php') && !class_exists($class)) {
            require_once $className . '.php';
            return;
        }
    }
}

loadFiles(CONFIG);
loadFiles(HELPERS);

require_once APP . 'policy.php';
global $app;
$app = new app();
$app->init();
?>