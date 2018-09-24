<div class="row">
    <div class="col-md-6">
        <div class="box 1-primary">
            <div class="box-header">
                <h3 class="box-title">Import Guests to Event</h3>
            </div><!-- /.box-header -->
            <!-- form start -->
            <form role="form" method="post" action="<?php echo $APPVARS->siteUrl; ?>guests/import" enctype="multipart/form-data">
                <div class="box-body">
                    <?php if ($this->error != "") { ?>
                        <div class="callout callout-warning">
                            <h4>Error Encountered</h4>
                            <p><?php echo $this->error; ?></p>
                        </div>
                    <?php } ?>
                    <div class="form-group">
                        <label for="event">Event </label>
                        <select name="event_id" class="form-control">
                            <?php foreach ($this->events as $event) { ?>
                                <option value="<?php echo $event['id']; ?>" <?php echo @$this->post['event_id']==$event['id']?'selected="selected"':'';?> ><?php echo $event['name']; ?></option>
                            <?php } ?>                            
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="name">CSV Files</label>
                        <input name="file" type="file" multiple/>
                    </div>

                </div>
                <div class="box-footer">
                    <button type='submit' name="save" class="btn btn-flat btn-primary"><i class="fa fa-save"></i> &nbsp; Import</button>
                </div>
            </form>
        </div>
    </div>
</div>