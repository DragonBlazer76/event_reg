<?php if (@$this->result == 0 && @$this->reult == 0) { ?>
    <div class="callout callout-warning">
        <h4>No AuditLog found.</h4>

    </div>
<?php } else if (@$this->result > 0) { ?>


    <div class="box"  >
        <div class="box-body">

            <table id="tblEventsList" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="6%">ID</th>
                        <th width="6%">UserName</th>
                        <th>Message</th>
                        <th width="14%">Category</th>
                        <th width="10%">Subcategory</th>
                        <th width="10%">Date</th>
                    </tr>
                    <?php foreach (@$this->result as $value): ?>
                        <tr>
                            <td><?php echo $value['id']; ?></td>
                            <td><?php echo @$value['username']; ?></td>
                            <td><?php echo $value['message']; ?></td>
                            <td><?php echo $value['category']; ?></td>
                            <td><?php echo $value['subcategory']; ?></td>
                            <td><?php echo $value['date_created']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                   
                </thead>
            </table>
        </div>
    </div>


<?php } ?>



