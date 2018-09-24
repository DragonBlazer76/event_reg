
<div class="login" ng-controller="home">
<div class="row">
    <div class="main-login col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
        <div class="logo margin-top-30">
            <img src="assets/images/logo.png" alt="SB"/>
        </div>
        <!-- start: LOGIN BOX -->
        <div class="box-login">
            <form class="form-login" action="<?php echo $APPVARS->siteUrl; ?>reset" name="frmReset" id="frmReset" method="post">
                <fieldset>
                    <legend>
                        Reset Password
                    </legend>
                    <p>
                        Please enter your password and confirmation password
                    </p>
                    <div class="form-group">
                        <span class="input-icon">
                         <input type="password" class="form-control password" name="password" placeholder="Password" required>
                            <i class="fa fa-lock"></i>
                    </div>
                    <div class="form-group">
                        <span class="input-icon">
                        <input type="password" class="form-control password" name="confirmation" placeholder="Confirm Password" required>
                            <i class="fa fa-lock"></i>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary pull-right" >
                            Reset <i class="fa fa-arrow-circle-right"></i>
                        </button>
                    </div>
                </fieldset>
            </form>
            <!-- start: COPYRIGHT -->
            <div class="copyright">
                &copy; <span class="current-year"></span><span class="text-bold text-uppercase"> SB</span>. <span>All rights reserved</span>
            </div>
            <!-- end: COPYRIGHT -->
        </div>
        <!-- end: LOGIN BOX -->
    </div>
</div>
</div>