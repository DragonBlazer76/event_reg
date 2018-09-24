<!DOCTYPE html>
<html xmlns:fb="http://ogp.me/ns/fb#" <?php echo @$APPVARS->htmlId != '' ? 'id="' . @$APPVARS->htmlId . '"' : ''; ?> >
    <head prefix="og: http://ogp.me/ns# object: http://ogp.me/ns/object#">
        <title><?php echo $APPVARS->page['metaTitle']; ?></title>

        <!--<link rel="shortcut icon" href="images/sg-ico.ico">-->

        <!-- Viewport mobile tag for sensible mobile support -->
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        
	<!--<link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700' rel='stylesheet' type='text/css' />
        <link href='//fonts.googleapis.com/css?family=Open+Sans:400italic,800italic,400,800' rel='stylesheet' type='text/css' />-->

        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />

        <?php
        include_once('_global/_cssLoader.php');
        include_once('_global/_cssCustom.php');
        ?>    

        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    </head>

    <body id="<?php echo @$APPVARS->page["properties"]['bodyId']; ?>" class="<?php echo @$APPVARS->page["properties"]['bodyClass']; ?> skin-blue sidebar-mini" ng-app="appnSAPP">
        <div id="app">

            <?php
	    header('Content-Type: text/html; charset=utf-8');
            include_once( VIEWS . "_themes/" . $APPVARS->theme . "/template.php" );

            include_once('_global/_jsLoader.php');
            ?>
        </div> 
    </body>
</html>
