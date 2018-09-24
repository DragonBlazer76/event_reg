<div class="wrapper">

    <header class="main-header">

        <!-- Logo -->
        <a href="<?php echo $APPVARS->siteUrl; ?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>E</b>R</span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>Event </b> Registration</span>
        </a>

        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
            </a>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?= $APPVARS->assetPath; ?>images/avatar5.png" class="user-image" alt="User Image"/>
                            <span class="hidden-xs"><?php echo $APPVARS->user['name']; ?></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="<?= $APPVARS->assetPath; ?>images/avatar5.png" class="img-circle" alt="User Image" />
                                <p>
                                    <?php echo $APPVARS->user['name']; ?>
                                    <small>Last login: <?php echo $APPVARS->user['last_login']; ?></small>
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="<?php echo $APPVARS->siteUrl; ?>home/profile" class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="<?php echo $APPVARS->siteUrl; ?>logout" class="btn btn-default btn-flat">Logout</a>
                                </div>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>

        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="<?= $APPVARS->assetPath; ?>images/avatar5.png" class="img-circle" alt="User Image" />
                </div>
                <div class="pull-left info">
                    <p><?php echo $APPVARS->user['name']; ?></p>
                    <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
                </div>
            </div>
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                <li class="treeview <?= $APPVARS->controllerName == 'guests' ? 'active' : ''; ?>">
                    <a href="<?= $APPVARS->siteUrl; ?>guests">
                        <i class="fa fa-users"></i>
                        <span>Guests </span>
                        <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a href="<?= $APPVARS->siteUrl; ?>guests"><i class="fa fa-table"></i> Lists</a></li>
                        <li><a href="<?= $APPVARS->siteUrl; ?>guests/new"><i class="fa fa-user-plus"></i> Add Guest</a></li>
                        <?php if ($APPVARS->user['user_level'] !== "customer") { ?>
                            <li><a href="<?= $APPVARS->siteUrl; ?>guests/import"><i class="fa fa-download"></i> Import CSV</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php if ($APPVARS->user['user_level'] !== "customer") { ?>
                    <li class="treeview <?= $APPVARS->controllerName == 'events' ? 'active' : ''; ?>">
                        <a href="<?= $APPVARS->siteUrl; ?>events">
                            <i class="fa fa-calendar"></i> <span>Events</span>
                            <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a href="<?= $APPVARS->siteUrl; ?>events"><i class="fa fa-table"></i> Lists</a></li>
                            <li><a href="<?= $APPVARS->siteUrl; ?>events/new"><i class="fa fa-plus-circle"></i> Add Event</a></li>
                        </ul>
                    </li>
                <?php } ?>
                <li class="treeview <?= $APPVARS->controllerName == 'home' ? 'active' : ''; ?>">
                    <a href="<?= $APPVARS->siteUrl; ?>home/profile">
                        <i class="fa fa-user-md"></i> <span>My Profile</span>
                    </a>
                </li>
                <?php if ($APPVARS->user['user_level'] !== "customer" && $APPVARS->user['user_level'] !== "operator" ) { ?>
                    <li class="treeview <?= $APPVARS->actionName == 'auditlog' ? 'active' : ''; ?>">
                        <a href="<?= $APPVARS->siteUrl; ?>web/auditlog">
                            <i class="fa fa-cogs"></i> <span>Audit Log</span>
                        </a>
                    </li>
                <?php } ?>
                <li class="treeview">
                    <a href="<?= $APPVARS->siteUrl; ?>logout">
                        <i class="fa fa-close"></i> <span>Logout</span>
                    </a>
                </li>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <?php echo plgPageTitle(); ?>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                <li class="active">Dashboard</li>
            </ol>
        </section>
        <section class="content">
            <?php
            plgFlashMessage(appServices::getFlashMessage());
            include_once( $APPVARS->viewPath );
            ?>
        </section>
    </div><!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Create the tabs -->
        <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
            <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
            <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
        </ul>
    </aside><!-- /.control-sidebar -->

    <div class='control-sidebar-bg'></div>

</div><!-- ./wrapper -->