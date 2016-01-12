<?php ob_start(); ?>
<?php foreach ($response as $value) { ?>
    <div class="box box-info pull-left">
        <div class="box-body">

            <div class="col-sm-2 list-time">
                <div class="date-wrapper">
                    <span>
                        <?php echo date('D, M, j', strtotime($value->startTime)); ?>
                    </span>
                </div>
                <div class="time-wrapper">
                    <span>
                        <?php echo date('H: i', strtotime($value->startTime)); ?>
                        <span class="timeformat-wrapper">
                            <?php echo date('A', strtotime($value->startTime)); ?>
                        </span>
                    </span>
                </div>
            </div>
            
            <div class="col-sm-6 list-body" data-id-meeting="<?php echo $value->id; ?>">
                <div class="list-subject">
                    <span><?php echo $value->subject; ?></span>
                </div>
                <div class="list-duration">
                    <span>
                        <?php echo date('H: i A', strtotime($value->startTime)); ?> - <?php echo date('H: i A', strtotime($value->endTime)); ?>
                    </span>
                </div>
                <div class="list-organizer">
                    <span>Organizers: <?php echo $value->firstName . ' ' . $value->lastName; ?></span>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="list-options">
                    <span class="sessions">
                        <a data-id="<?php echo $value->id; ?>-meeting" title="Add Registrant" width="550" height="330" href="javascript:void(0);" class="share grey-link add-registrant" type="button">
                            <i class="glyphicon glyphicon-share" style="font-size: 18px;"></i>
                        </a>
                    </span>
                </div>
            </div>

        </div>
    </div>

<?php } ?>

<?php
$str_content = ob_get_contents();
ob_end_clean();
ob_end_flush();

echo $str_content;
exit;
?>