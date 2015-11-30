<form class="form-horizontal" id="<?php echo $form_name; ?>">
    
    <?php if (isset($categories) && ( !empty($categories))) { ?>
        <div style="display: none;" id="str_categories_wrapper">
            <?php echo form_dropdown('str_categories', $categories, '', 'id="str_categories" class="form-control"'); ?>
        </div>
    <?php } ?>

    <div class="form-group">
        <h4 class="col-lg-12 no-padding">Setup Employee Position</h4>
    </div>

    <div class="clearfix"></div>

    <?php if (!empty($data)) { ?>
        <div class="col-lg-5 panel panel-default">
            
            <?php foreach ($data as $row) { ?>
                <div class="form-group">
                    <label class="col-lg-12"><input name="checkbox" class="my_checkbox checkbox-inline right-margin-10" type="checkbox" value="<?php echo $row['value']; ?>"><?php echo $row['value']; ?></label></label>
                </div>
            <?php } ?>
            
        </div>
    <?php } ?>

    <div class="col-lg-5 panel panel-default" id="extra_vaules">
        <table class="table table-hover table-condensed table-bordered table-responsive">
            <thead>
                <tr>
                    <th>Position Name</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>




</form>
