<?php if (@$this->totalEvents == 0 && @$this->totalGuests == 0) { ?>
    <div class="callout callout-warning">
        <h4>No events found.</h4>
        <p>Please create new event first.</p>
    </div>
<?php } else if (@$this->totalEvents > 0 && @$this->totalGuests == 0) { ?>
    <h3><?php echo $this->totalEvents; ?> Events Found</h3>
    <p>You have <?php echo $this->totalEvents; ?> events. Click on the button below to proceed adding/importing guest into an event.</p>
    <p><a href="<?php echo $APPVARS->siteUrl; ?>guests/import" class="btn btn-flat btn-primary">Import Now</a></p>
<?php } else { ?>

    <div class="box" ng-controller="events" data-eventId="<?php echo @$this->eventId; ?>">
        <div class="box-body">

            <table id="tblEventsList" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="6%">Edit</th>
                        <th width="6%">Delete</th>
                        <th>Event Name</th>
                        <th width="14%">No. of Guests</th>
                        <th width="10%">Start Date</th>
                        <th width="10%">End Date</th>
                        <!--<th width="6%">Status </th>-->
                        <th width="10%">Created On</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modDeleteEvent">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Delete Event</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <b id="modDeleteEventName"></b> event with <b id="modDeleteEventGuestsTotal"></b> guests?</p>
                    <p>This action will also delete all the guests under this event. </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modDeleteEventConfDelete">Confirm Delete</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php } ?>

