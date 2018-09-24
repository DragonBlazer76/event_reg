<?php
global $UICOMPONENTS, $GLOBAL_CONFIG ;

$UICOMPONENTS = new stdClass();

//css/js components to load per usage
//DatePicker components
$UICOMPONENTS->datePicker = array(
    'css' => array(),
    'js' => array()
);

$UICOMPONENTS->formValidator = array(
    'js' => array(
        '/common/js/jquery.validate.min.js',
        '/common/js/jquery.add-methods.min.js'
    )
);

$UICOMPONENTS->dateRangePicker = array(
    'css' => array(
        '/common/js/plugins/daterangepicker/daterangepicker-bs3.css'
    ),
    'js' => array(
        '/common/js/jquery.moment.min.js',
        '/common/js/plugins/daterangepicker/daterangepicker.js'
    )
);

$UICOMPONENTS->dataTables = array(
    'css' => array(
        '/common/js/plugins/datatables/dataTables.bootstrap.css'
    ),
    'js' => array(
        '/common/js/plugins/datatables/jquery.dataTables.min.js',
        '/common/js/plugins/datatables/dataTables.bootstrap.min.js'
    )
);

?>
