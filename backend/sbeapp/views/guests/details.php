<div class="row">
    <div class="col-md-6">
        <?php if( @$this->error!="" ){ ?>
        <div class="alert alert-error" data-autoclose="true">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo @$this->error; ?>
        </div>
        <?php } ?>
        
        <div class="box 1-primary">
            <div class="box-header">
                <h3 class="box-title">View Guest Details</h3>
            </div><!-- /.box-header -->

            <div class="box-body">
                <div class="form-group">
                    <label for="name">Code</label>
                    <p><?php echo $this->post['code']; ?></p>
                </div>
                <div class="form-group">
                    <label for="name">NRIC</label>
                    <p><?php echo $this->post['nric']; ?></p>
                </div>
                <div class="form-group">
                    <label for="name">Name (First / Middle / Last)</label>
                    <p><?php echo $this->post['fname'] . ' ' . $this->post['mname'] . ' ' . $this->post['lname']; ?></p>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <p><?php echo $this->post['email']; ?></p>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <p><?php echo $this->post['address']; ?></p>
                </div>
                <div class="form-group">
                    <label>Contact</label>
                    <p><?php echo $this->post['contact']; ?></p>
                </div>
                <div class="form-group">
                    <label>Table No</label>
                    <p><?php echo $this->post['tableno']; ?></p>
                </div>

                <?php if ($this->post['app_id'] != "") { ?>
                    <div class="form-group">
                        <label>App ID</label>
                        <p><?php echo $this->post['app_id']; ?></p>
                    </div>
                <?php } ?>

                <?php if ($this->post['station'] != "") { ?>
                    <div class="form-group">
                        <label>Station</label>
                        <p><?php echo $this->post['station']; ?></p>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label>Created By / Date</label>
                    <p><?php echo $this->post['created_by_name']; ?> / <?php echo $this->post['created_date_f']; ?></p>
                </div>

                <?php if ($this->post['modified_by'] != 0) { ?>
                    <div class="form-group">
                        <label>Last Modified By / Date</label>
                        <p><?php echo $this->post['modified_by_name']; ?> / <?php echo $this->post['modified_date_f']; ?></p>
                    </div>
                <?php } ?>

            </div>
            <div class="box-footer">
                <a href="<?php echo $APPVARS->siteUrl; ?>guests/edit?id=<?php echo $this->post['id']; ?>" class="btn btn-flat btn-primary"><i class="fa fa-pencil"></i> &nbsp; Edit</a>
            </div>
        </div>
    </div>
</div>
