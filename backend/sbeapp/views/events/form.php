<div class="row">
    <div class="col-md-6">
        <div class="box 1-primary">
            <div class="box-header">
                <h3 class="box-title"><?php echo $this->method == "insert" ? 'New Event' : 'Edit Event'; ?></h3>
            </div><!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" action="<?php echo $APPVARS->siteUrl; ?>events/save" >
                <div class="box-body">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter event's name" value="<?php echo trim(@$this->post['name']); ?>" required />
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Enter event's description"><?php echo trim(@$this->post['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Event Date (Start-End)</label>
                        <input type="text" name="eventdates" value="<?php echo trim(@$this->post['eventdates']); ?>" class="form-control pull-right dateRangePicker" />
                    </div>
                    <?php if (@$this->post['created_by'] > 0) { ?>
                        <div class="form-group"><br /><br />
                            <label>Created By / Date</label>
                            <p><?php echo $this->post['created_by_name']; ?> / <?php echo $this->post['created_date_f']; ?></p>
                        </div>
                    <?php } ?>
                    <?php if (@$this->post['modified_by'] > 0) { ?>
                        <div class="form-group"><br /><br />
                            <label>Last Modified By / Date</label>
                            <p><?php echo $this->post['modified_by_name']; ?> / <?php echo $this->post['modified_date_f']; ?></p>
                        </div>
                    <?php } ?>

                </div>
                <div class="box-footer">
                    <button type='submit' name="save" class="btn btn-flat btn-primary"><i class="fa fa-save"></i> &nbsp; Save</button>
                </div>
                <input type="hidden" name="id" value="<?php echo trim(@$this->post['id']); ?>" />
            </form>
        </div>
    </div>
</div>