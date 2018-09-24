<?php if (@$this->totalEvents == 0 && @$this->totalGuests == 0) { ?>
    <div class="callout callout-warning">
        <h4>No events & guests found.</h4>
        <p>Please create new event first.</p>
    </div>
<?php } else if (@$this->totalEvents > 0 && @$this->totalGuests == 0) { ?>
    <h3><?php echo $this->totalEvents; ?> Events Found</h3>
    <p>You have <?php echo $this->totalEvents; ?> events. Click on the button below to proceed adding/importing guest into an event.</p>
    <p><a href="<?php echo $APPVARS->siteUrl; ?>guests/import" class="btn btn-flat btn-primary">Import Now</a></p>
<?php } else { ?>

    <div class="box" ng-controller="guests" data-eventId="<?php echo @$this->eventId; ?>">
        <div class="box-body">

            <div class="form-group row" style="margin-bottom:20px;">
                <div class="col-md-3">
                    <select name="event_id" class="form-control" ng-model="eventId" ng-change="updateEventId()" <?php echo @$_GET['eid'] != "" ? 'ng-init="eventId=' . $_GET['eid'] . '"' : ''; ?>>
                        <option value="">Please select an event...</option>
                        <?php foreach ($this->events as $event) { ?>
                            <option value="<?php echo $event['id']; ?>" <?php echo @$_GET['eid'] == $event['id'] ? 'selected="selected"' : ''; ?> ><?php echo $event['name']; ?></option>
                        <?php } ?>                            
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="javascript:;" id="btnLoadEvent" class="btn btn-flat btn-default" ng-click="setEvent()">
                        <i class="fa fa-search"></i> &nbsp; Go
                    </a> &nbsp; 
                </div>

                <div class="col-md-7" id="divCTAButtons">
                    <a href="<?php $APPVARS->siteUrl; ?>guests/new?id=" class="btn btn-flat btn-primary" id="btnLinkNewGuest">
                        <i class="fa fa-plus"></i> &nbsp; Add Guest
                    </a> &nbsp; 
                    <?php if ($this->userRole !== 'customer') { ?>
                        <a href="<?php $APPVARS->siteUrl; ?>guests/import?id=" class="btn btn-flat btn-primary" id="btnLinkImportGuest">
                            <i class="fa fa-file-excel-o"></i> &nbsp; Import CSV
                        </a> &nbsp;
                    <?php }?>
                            <a href="javascript:;" class="btn btn-flat btn-danger fnExpBlock" data-targetId="blkGenReport">
                                <i class="fa fa-download"></i> &nbsp; Generate Report
                            </a>
                        </div>
                        <br />
                    </div>

                    <div class="row" id="blkGenReport" style="display:none;">

                        <div class="col-sm-6 col-xs-10 col-sm-offset-3 col-xs-offset-1">
                            <div class="box box-danger">
                                <div class="box-header">
                                    <h3 class="box-title">GENERATE GUESTS REPORT</h3>
                                </div>
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="form-group">
                                                <!--<label for="name">Filter</label>-->
                                                <select id="fileter-val" name="filter" class="form-control" required>
                                                    <option value="all">All guests</option>
                                                    <option value="unregistered">Unregistered</option>
                                                    <option value="registered">Registered</option>
                                                    <option value="logout">Logged Out</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <a ng-click="csvDownload()" id="csvDownload" href="#" name="download_as" value="csv" class="btn btn-flat btn-primary">
                                        <i class="fa fa-file-excel-o"></i> &nbsp; Download as csv
                                    </a>
                                    <!--                            <a ng-click="pdfDownload()" id="pdfDownload" href ="#" type='button' name="download_as" value="pdf" class="btn btn-flat btn-danger">
                                                                    <i class="fa  fa-file-pdf-o"></i> &nbsp; Download as pdf
                                                                </a>-->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ng-if="eventId == ''" class="row">
                        <div class="col-md-12 col-xs-12"><br /><br /><br />
                            <h3 class="text-center">Select event to load the guests...</h3><br /><br /><br />
                        </div>
                    </div>

                    <div ng-if="eventId > 0" id="mListGuests">

                        <div class="row preLoader" style="display:none">
                            <div class="col-md-12 col-xs-12">
                                <br /><br /><br />
                                <h3 class="text-center">
                                    <img src="<?php echo $APPVARS->assetUrl; ?>images/preloader.gif" /><br /><br />
                                    Loading, please wait...
                                </h3>
                                <br /><br /><br />
                            </div>
                        </div>

                        <div class="row" id="evtGuestSummary" style="margin-top:50px;margin-bottom:20px;">
                            <div class="col-md-3 col-sm-6 col-xs-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Guests</span>
                                        <span class="info-box-number" id="evtTotalGuests">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-blue"><i class="fa fa-user-md"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Registered Guests</span>
                                        <span class="info-box-number" id="evtTotalGuestsReg">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="fa fa-user-plus"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">UNRegistered Guests</span>
                                        <span class="info-box-number" id="evtTotalGuestsUNReg">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 col-xs-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-fuchsia"><i class="fa fa-close"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Logout Guests</span>
                                        <span class="info-box-number" id="evtTotalGuestsLogout">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        

                        <table id="tblGuestsList" class="table table-bordered table-striped results" style="display:none">
                            <thead>
                                <tr>
                                    <th>Edit</th>
                                    <th>Delete</th>
                                    <th>Code</th>
                                    <th>NRIC</th>
                                    <th>Guest Name</th>
                                    <th>Designation</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Table No</th>
                                    <th>Registered Date</th>
                                </tr>
                            </thead>
                        </table>

                    </div>

                </div>
            </div>

            <div class="modal fade" id="modDeleteGuest">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Delete Guest</h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure you want to delete this guest <b id="modDeleteGuestName"></b>?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id="modDeleteGuestConfDelete">Confirm Delete</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        <?php } ?>
