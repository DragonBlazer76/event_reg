<div class="row" ng-controller="guests">
    <div class="col-md-6">
        <?php if( @$this->error!="" ){ ?>
        <div class="alert alert-error" data-autoclose="true">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo @$this->error; ?>
        </div>
        <?php } ?>
        
        <div class="box 1-primary">
            <div class="box-header">
                <h5 class="box-title">
                    <strong><?php echo @$this->event['name']; ?></strong> 
                    <?php echo @$this->method == "insert" ? 'New ' : 'Edit '; ?> Guest 
                </h5>
            </div><!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" action="<?php echo $APPVARS->siteUrl; ?>guests/save" id="frmGuest">
                <div class="box-body">
                    <div class="form-group">
                        <label for="name">Event Name</label>
                        <select name="event_id" class="form-control">
                            <?php foreach ($this->events as $event) { ?>
                                <option value="<?php echo $event['id']; ?>" <?php echo $this->eventId==$event['id']?'selected="selected"':'';?>><?php echo $event['name']; ?></option>
                            <?php } ?>                            
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Code</label>
                        <input type="text" name="code" class="form-control" placeholder="Enter guest's Code" value="<?php echo trim(@$this->post['code']); ?>" required />
                    </div>
                    <div class="form-group">
                        <label for="name">NRIC</label>
                        <input type="text" name="nric" class="form-control" placeholder="Enter guest's NRIC" value="<?php echo trim(@$this->post['nric']); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="name">Guest Name</label>
                        <input type="text" name="fname" class="form-control" placeholder="Enter guest's first name" value="<?php echo trim(@$this->post['fname']); ?>" required />
                    </div>
                    <div class="form-group">
                        <label for="name">Designation</label>
                        <input type="text" name="lname" class="form-control" placeholder="Enter guest's last name" value="<?php echo trim(@$this->post['lname']); ?>" />
                    </div>
                    <div class="form-group">
                        <label for="name">Company Name</label>
                        <input type="text" name="mname" class="form-control" placeholder="Enter guest's middle name" value="<?php echo trim(@$this->post['mname']); ?>" />
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="text" name="email" class="form-control" placeholder="Enter email address" value="<?php echo trim(@$this->post['email']); ?>" />
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <input type="text" name="address" class="form-control" placeholder="Enter address" value="<?php echo trim(@$this->post['address']); ?>" />
                    </div>
                    <div class="form-group">
                        <label>Contact</label>
                        <input type="text" name="contact" class="form-control" placeholder="Enter contact" value="<?php echo trim(@$this->post['contact']); ?>" />
                    </div>
                    <div class="form-group">
                        <label>Table No</label>
                        <input type="text" name="tableno" class="form-control" placeholder="Enter Table number" value="<?php echo trim(@$this->post['tableno']); ?>" />
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control" placeholder="Select status" required ng-model="guestStatus" ng-change="checkGuestStatus()">
                            <option value="unregistered" <?php echo @$this->post['status'] == '' || @$this->post['status'] == 'unregistered' ? 'selected="selected"' : ''; ?>>Unregistered</option>
                            <option value="registered" <?php echo @$this->post['status'] == 'registered' ? 'selected="selected"' : ''; ?>>Registered</option>
                            <option value="logout" <?php echo @$this->post['status'] == 'logout' ? 'selected="selected"' : ''; ?>>Logout</option>
                        </select>                            
                    </div>

                    <div class="form-group">
                        <label>App ID</label>
                        <input type="text" name="app_id" class="form-control" placeholder="Enter App ID" value="<?php echo trim(@$this->post['app_id']); ?>" />
                    </div>
                    <div class="form-group">
                        <label>Station</label>
                        <input type="text" name="station" class="form-control" placeholder="Enter station" value="<?php echo trim(@$this->post['station']); ?>" />
                    </div>

                    <?php if (@$this->post['created_by'] != "") { ?>
                        <div class="form-group"><br />
                            <label>Created By / Date</label>
                            <p><?php echo $this->post['created_by_name']; ?> / <?php echo $this->post['created_date_f']; ?></p>
                        </div>
                    <?php } ?>
                    <?php if (@$this->post['modified_by'] != "") { ?>
                        <div class="form-group">
                            <label>Last Modified By / Date</label>
                            <p><?php echo $this->post['modified_by_name']; ?> / <?php echo $this->post['modified_date_f']; ?></p>
                        </div>
                    <?php } ?>

                </div>
                <div class="box-footer">
                    <button type='submit' name="save" class="btn btn-flat btn-primary"><i class="fa fa-save"></i> &nbsp; Save</button>
                </div>
                <input type="hidden" name="id" value="<?php echo trim(@$this->post['id']); ?>" />
                <!--<input type="hidden" name="event_id" value="<?php // echo trim(@$this->post['event_id']); ?>" />-->
            </form>
        </div>
    </div>
</div>
