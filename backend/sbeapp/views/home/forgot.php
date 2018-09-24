
<div class="login-box" ng-controller="home">
    <div class="login-logo">
        <b>Forgot Password</b>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        <!-- form start -->
            <form role="form" action="<?php echo $APPVARS->siteUrl; ?>home/forgot" name="frmLogin" id="frmLogin" method="post" >
                <div class="box-body">
                    
                    <?php plgFlashMessage(appServices::getFlashMessage()); ?>
                    
                    <?php if ($this->error != "") { ?>
                        <div class="callout callout-warning">
                            <h4>Error Encountered</h4>
                            <p><?php echo $this->error; ?></p>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                        <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email" value="<?php echo @$this->email ?>">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">New Password</label>
                        <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                    </div>

                </div><!-- /.box-body -->

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="<?php echo $APPVARS->siteUrl;?>login" style="margin-left:30px">Login</a>
                </div>
            </form>
    </div>
</div>    
