<?php
if( @$this->error!="" ){
    echo '<p>'.@$this->error.'</p>';
}
?>
    <div class="row">
        <!-- left column -->
        <div class="col-md-6">
            <!-- general form elements -->
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Edit Profile</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <form role="form" action="<?php echo $APPVARS->siteUrl; ?>home/profile" name="frmLogin" id="frmLogin" method="post" >
                    <div class="box-body">
                        <div class="form-group">
                           <label for="exampleInputName">Name</label>
                            <input type="text"  name ="name" class="form-control" placeholder="Username" value="<?php echo @$this->name ?>" >
                        </div>
                        <div class="form-group">
                            <label for="exampleInputEmail1">Email address</label>
                            <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email" value="<?php echo @$this->email ?>">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                        </div>

                    </div><!-- /.box-body -->

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div><!-- /.box -->

        </div><!--/.col (left) -->
        <!-- right column -->
 
    </div>   <!-- /.row -->
