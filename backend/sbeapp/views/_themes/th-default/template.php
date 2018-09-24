<?php 
if( $APPVARS->isAuthenticated ){
    include_once( VIEWS . "_themes/" . $APPVARS->theme . "/layout/two_columns.php" );
}else{
    include_once( $APPVARS->viewPath ); 
}

?>