<!--<div ng-controller="home">
    <form name="frmRegister" id="frmRegister" method="post" ng-submit="formValidate($event)" action="<?php echo $APPVARS->siteUrl; ?>register">
        Company Name: <input type="text" name="name" ng-model="companyName" placeholder="Enter company name" required /><br>
        Email : <input type="email" name="email" ng-model="email" ng-blur="checkEmail()" placeholder="Enter company email" required /><br>
        <span class="help-inline" ng-show="submitted && frmRegister.email.$error.required">Required</span>
        <span class="help-inline" ng-show="submitted && frmRegister.email.$error.email">Invalid email</span>
            
        Industry : <select name="industry_id" ng-model="industryId">
            <option ng-repeat="item in industries" value="{{item.id}}">{{item.name}}</option>
        </select><br />
        
        Contact Person First Name : <input type="text" name="fname" ng-model="fname" placeholder="Enter contact person's first name" /><br>
        Contact Person Last Name : <input type="text" name="lname" ng-model="lname" placeholder="Enter contact person's last name" /><br>
        
        Password : <input type="password" name="password" placeholder="Enter password" required /><br><br>

        <button type="submit" name="submit" value="register" ng-click="submitted=true">Register</button>
    </form>
</div>-->
<!-- start: REGISTRATION -->
<div class="login" ng-controller="home">
    <div class="row">
        <div class="main-login col-xs-10 col-xs-offset-1 col-sm-8 col-sm-offset-2 col-md-4 col-md-offset-4">
            <div class="logo margin-top-30">
                <img src="assets/images/logo.png" alt="SB"/>
            </div>
            <!-- start: REGISTER BOX -->
            <div class="box-register">
                <form class="form-register" name="frmRegister" id="frmRegister" method="post" ng-submit="formValidate($event)" action="<?php echo $APPVARS->siteUrl; ?>register">
                    <fieldset>
                        <legend>
                            Sign Up
                        </legend>
                        <p>
                            Enter your details below:
                        </p>
                        <div class="form-group">
                            <span class="input-icon">
                            <input type="text" name="name" ng-model="companyName" placeholder="Company name" class="form-control" required /><br>
                            <i class="fa fa-user"></i> </span>
                            <!--<input type="text" class="form-control" name="full_name" placeholder="Full Name">-->
                        </div>
                        <div class="form-group">
                            <!--<input type="text" class="form-control" name="address" placeholder="Address">-->
                            <span class="input-icon">
                            <input type="email" name="email" ng-model="email" ng-blur="checkEmail()" placeholder="Company email" class="form-control" required /><br>
                            <span class="help-inline" ng-show="submitted && frmRegister.email.$error.required">Required</span>
                            <span class="help-inline" ng-show="submitted && frmRegister.email.$error.email">Invalid email</span>
                            <i class="fa fa-envelope"></i> </span>
                        </div>
                        <div class="form-group">
                            <span class="input-icon">
                            <input type="text" name="fname" ng-model="fname" placeholder="Contact Person First Name" class="form-control"/><br>
                            <i class="fa fa-user"></i> </span>
<!--                            <label class="block">
                                Gender
                            </label>
                            <div class="clip-radio radio-primary">
                                <input type="radio" id="rg-female" name="gender" value="female">
                                <label for="rg-female">
                                    Female
                                </label>
                                <input type="radio" id="rg-male" name="gender" value="male">
                                <label for="rg-male">
                                    Male
                                </label>
                            </div>-->
                        </div>
                        <div class="form-group">
                            <span class="input-icon">
                            <input type="text" name="lname" ng-model="lname" placeholder="Contact Person Last Name" class="form-control"/><br>
                            <i class="fa fa-user"></i> </span>
                        </div>

                        <div class="form-group">
                            <span class="input-icon">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <i class="fa fa-lock"></i> </span>
                        </div>
                        <div class="form-group">
                            <!--<input type="text" class="form-control" name="city" placeholder="City">-->
                            <b>Industry : </b> <select name="industry_id" ng-model="industryId">
                                <option ng-repeat="item in industries" value="{{item.id}}">{{item.name}}</option>
                            </select><br />
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox clip-check check-primary">
                                <input type="checkbox" id="agree" value="agree" name="agree" ng-model="agreeTC">
                                <label for="agree">
                                    I agree to T&C's
                                </label>
                            </div>
                        </div>
                        <div class="form-actions">
                            <p>
                                Already have an account?
                                <a href="<?php echo $APPVARS->siteUrl; ?>login">
                                    Login
                                </a>
                            </p>
                            <button type="submit" class="btn btn-primary pull-right" ng-click="submitted=true">
                                Register <i class="fa fa-arrow-circle-right"></i>
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
            <!-- end: REGISTER BOX -->
        </div>
    </div>
</div>

<!--<script type="text/javascript">

    Main.init();
    Login.init();

</script>-->
<!-- end: JavaScript Event Handlers for this page -->
<!-- end: CLIP-TWO JAVASCRIPTS -->

<!-- end: BODY -->





