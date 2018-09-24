<div class="login-box" ng-controller="home">
    <div class="login-logo">
        <a href="<?php echo $APPVARS->siteUrl; ?>"><b>Login</b></a>
    </div><!-- /.login-logo -->
    <div class="login-box-body">

        <?php plgFlashMessage(appServices::getFlashMessage()); ?>
        <?php if (@$this->error != "") { ?>
            <div class="alert alert-error" data-autoclose="true">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <?php echo @$this->error; ?>
            </div>
        <?php } ?>

        <p class="login-box-msg">Sign in to start your session</p>
        <form action="<?php echo $APPVARS->siteUrl; ?>home/login" name="frmLogin" id="frmLogin" method="post" ng-submit="loginFormValidate($event)">
            <div class="form-group has-feedback">
                <input type="email" name="email" ng-model="email" ng-blur="checkEmail(true)" placeholder="Enter your email" class="form-control" autocomplete="off" required /><br>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                <span class="help-inline" ng-show="submitted && frmLogin.email.$error.required">Email Required</span>
                <span class="help-inline" ng-show="submitted && frmLogin.email.$error.email">Invalid email</span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="Password"/>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
                <div class="col-xs-8">    

                    <a class="forgot" href="<?php echo $APPVARS->siteUrl; ?>forgot" style="margin-top:5px;">
                        I forgot my password
                    </a>

                    <!-- div class="checkbox icheck">
                        <label>
                            <input type="checkbox"> Remember Me
                        </label>
                    </div -->                        
                </div><!-- /.col -->
                <div class="col-xs-4">
                    <button type="submit" name="task" value="login" class="btn btn-primary btn-block btn-flat">Login</button>
                </div><!-- /.col -->
            </div>
        </form>

    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->