<div class="row">
    <div class="col-md-6">
        <div class="box 1-primary">
            <div class="box-header">
                <h3 class="box-title">View Event Details</h3>
            </div><!-- /.box-header -->

            <div class="box-body">
                <div class="form-group">
                    <label for="name">Name</label>
                    <p><?php echo $this->post['name']; ?></p>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <p><?php echo $this->post['description']; ?></p>
                </div>
                <div class="form-group">
                    <label>Event Date (Start-End)</label>
                    <p><?php echo $this->post['start_date_f']; ?> - <?php echo $this->post['end_date_f']; ?></p>
                </div>
                <div class="form-group">
                    <label>Created By / Date</label>
                    <p><?php echo $this->post['created_by_name']; ?> / <?php echo $this->post['created_date_f']; ?></p>
                </div>
                <?php if ($this->post['modified_by']!="") { ?>
                    <div class="form-group">
                        <label>Last Modified By / Date</label>
                        <p><?php echo $this->post['modified_by_name']; ?> / <?php echo $this->post['modified_date_f']; ?></p>
                    </div>
                <?php } ?>
            </div>
            <div class="box-footer">
                <a href="<?php echo $APPVARS->siteUrl; ?>events/edit?id=<?php echo $this->post['id']; ?>" class="btn btn-flat btn-primary"><i class="fa fa-pencil"></i> &nbsp; Edit</a>
            </div>
        </div>
    </div>
</div>