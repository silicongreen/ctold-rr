<form class="form-horizontal" id="<?php echo $form_name; ?>">

    <?php // if (!empty($data)) { ?>

    <div class="form-group">
        <h4 class="col-lg-12 no-padding">Setup Shift</h4>
    </div>

    <div class="clearfix"></div>

    <div class="col-lg-5 panel panel-default">

        <div class="form-group">
            <label class="col-lg-6">Have shift? <input name="checkbox" class="shift_checkbox checkbox-inline right-margin-10" type="checkbox" value="1"></label>
        </div>

        <div class="form-group additional_shift">
            <!--            <div class="col-lg-11">
                            <input class="form-control additional_shift_txt" type="text" value="" placeholder="Enter Shift Name" />
                            <p>Press "Enter" when done</p>
                        </div>-->

            <div class="form-group">
                <label class="col-lg-12"><input name="checkbox" class="my_checkbox checkbox-inline right-margin-10" type="checkbox" value="Morning">Morning</label>
            </div>

            <div class="form-group">
                <label class="col-lg-12"><input name="checkbox" class="my_checkbox checkbox-inline right-margin-10" type="checkbox" value="Day">Day</label>
            </div>

        </div>

    </div>

    <div class="col-lg-5 panel panel-default" id="extra_vaules">
        <table class="table table-hover table-condensed table-bordered table-responsive">
            <thead>
                <tr>
                    <th>Shift Name</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>


    <?php // } ?>

    <div class="clearfix"></div>

    <?php
    $data['form_name'] = 'course';
    $this->load->view('school_setup/_course', $data);
    ?>

</form>
