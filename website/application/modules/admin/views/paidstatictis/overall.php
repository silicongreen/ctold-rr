
<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;

        $widget->run('sidebar');
        ?>
        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <div class="box grid_16" >
                    <div class="col_50">
                        <fieldset class="label_top top">
                            <label for="text_field_inline">Date</label>
                            <div style="float:left;">
                                <div class="dateranger_stat" id="dateranger_stat"  style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                                    <i class="glyphicon glyphicon-calendar icon-calendar icon-large"></i>
                                    <span id="range_data"></span> <b class="caret"></b>
                                </div>

                            </div>  
                        </fieldset>
                    </div>
                    <div class="col_25">
                        <fieldset class="label_top top">
                            <label for="text_field_inline">School</label>
                            <div>
                                <?php $classId = "id='select_school'" ?>
                                <?php echo form_dropdown("school_select", $schools, '', $classId) ?>
                            </div>   
                        </fieldset>
                    </div>

                </div>
                <?php if (isset($stat)): ?>
                    <div class="box grid_16" id="change_data_ajax">
                        <div class="block">
                            <h2 class="section">Paid Statistics</h2>

                            <?php
                            $total = 0;
                            foreach ($stat as $value):
                                ?>
                                <fieldset class="top">
                                    <label style="font-size: 20px; font-weight: bold;" ><?php echo $user_type[$value->user_type_paid]; ?></label>
                                    <div style="margin-top:10px; font-size: 20px; font-weight: bold;">
                                        <?php echo $value->countUsers ?>
                                    </div>
                                </fieldset>
                                <?php
                                $total = $total + $user_type[$value->user_type_paid];
                                unset($user_type[$value->user_type_paid]);
                                ?>
                            <?php endforeach; ?>
                            <?php foreach ($user_type as $value): ?>
                                <fieldset class="top">
                                    <label  style="font-size: 20px; font-weight: bold;" for="required_field"><?php echo $value; ?></label>
                                    <div style="margin-top:10px; font-size: 20px; font-weight: bold;">
                                        0
                                    </div>
                                </fieldset>
                            <?php endforeach; ?>
                            <fieldset class="top">
                                <label  style="font-size: 20px; font-weight: bold;" for="required_field">Total</label>
                                <div style="margin-top:10px; font-size: 20px; font-weight: bold;">
                                    <?php echo $total ?>
                                </div>
                            </fieldset>
                        </div>
                    </div>    
                <?php endif; ?>



            </div>

        </div>

        <script type="text/javascript">
            var startDate_stat = moment();
            var endDate_stat = moment();
            $(document).ready(function () {
                $(document).on("change", "#select_school", function () {
                    $.post($("#base_url").val() + "admin/paidstatictis/new_stat", {school: $("#select_school").val(), start_date: startDate_stat.format("YYYY-MM-DD"), end_date: endDate_stat.format("YYYY-MM-DD")})
                            .done(function (data) {
                                $("#change_data_ajax").html(data);
                            });


                });
                $('.dateranger_stat').daterangepicker(
                        {
                            startDate: moment(),
                            endDate: moment(),
                            minDate: '08/01/2015',
                            maxDate: '12/31/2050',
                            dateLimit: {
                                days: 60
                            },
                            showDropdowns: true,
                            showWeekNumbers: true,
                            timePicker: false,
                            timePickerIncrement: 1,
                            timePicker12Hour: true,
                            ranges: {
                                'Today': [moment(), moment()],
                                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                                'Last 2 Days': [moment().subtract('days', 1), moment()],
                                'Last 3 Days': [moment().subtract('days', 2), moment()],
                                'Last 4 Days': [moment().subtract('days', 3), moment()],
                                'Last 5 Days': [moment().subtract('days', 4), moment()],
                                'Last 6 Days': [moment().subtract('days', 5), moment()],
                                'Last 7 Days': [moment().subtract('days', 6), moment()],
                                'Last 2 weeks': [moment().subtract('days', 13), moment()],
                                'Last 30 Days': [moment().subtract('days', 29), moment()],
                            },
                            opens: 'right',
                            buttonClasses: ['btn btn-default'],
                            applyClass: 'btn-small btn-primary',
                            cancelClass: 'btn-small',
                            format: 'MM/DD/YYYY',
                            separator: ' to ',
                            locale: {
                                applyLabel: 'Submit',
                                fromLabel: 'From',
                                toLabel: 'To',
                                customRangeLabel: 'Custom Range',
                                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                firstDay: 1
                            }
                        },
                function (start, end) {
                    $('#dateranger_stat span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
                    startDate_stat = start;
                    endDate_stat = end;
                    $.post($("#base_url").val() + "admin/paidstatictis/new_stat", {school: $("#select_school").val(), start_date: startDate_stat.format("YYYY-MM-DD"), end_date: endDate_stat.format("YYYY-MM-DD")})
                            .done(function (data) {
                                $("#change_data_ajax").html(data);
                            });

                }
                );

                $('.dateranger_stat span').html(moment().format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
            });

        </script>    
