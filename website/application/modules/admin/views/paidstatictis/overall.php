
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
                            
                            <table   class="table table-bordered" style="width: 100%;">
                                <thead>
                                    <tr class="even"><th>User Type</th><th>User</th><th>Session</th><th>Session Time (Min)</th></tr>
                                </thead>
                                <?php
                                    $total = 0;
                                    $totalsession = 0;
                                    $totaltime = 0;
                                    foreach ($stat as $value):
                                ?>
                                
                                <tr class="even"><td><?php echo $user_type[$value->user_type_paid]; ?></td><td> <a href="javascript:void(0)" class="user_full_stat" id="<?php echo $value->user_type_paid; ?>_full_stat"><?php echo $value->countUsers ?></td>
                                    <td> <?php echo $value->snumber ?></td><td><?php echo round($value->stime/60); ?></td></tr>
                                <?php
                                    $total = $total + $value->countUsers;
                                    $totalsession = $totalsession+$value->snumber;
                                    $totaltime = $totaltime+$value->stime;
                                    unset($user_type[$value->user_type_paid]);
                                ?>
                                <?php endforeach; ?>
                                <?php foreach ($user_type as $value): ?>
                                <tr class="even"><td>0</td><td>0</td>
                                    <td>0</td><td>0</td></tr>
                                <?php endforeach; ?>
                                <tr class="even"><td>All</td><td> <?php echo $total ?></td>
                                    <td> <?php echo $totalsession ?></td><td><?php echo $totaltime ?></td></tr>
                                
                            </table>

                            
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
            
             $(document).on("click", ".user_full_stat", function(){
   
                    var idFilter = this.id;
                    var $filterIdArray =  idFilter.split("_");
                    $.fancybox({
                        'width'		        : "40%",
                        'height'                : "60%",
                        'autoScale'             : true,
                        'href'			: $("#base_url").val() + "admin/paidstatictis/full_stat/"+$("#select_school").val()+"/"+$filterIdArray[0]+"/"+startDate_stat.format("YYYY-MM-DD")+"/"+endDate_stat.format("YYYY-MM-DD"),
                        'title'                 : false,
                        'transitionIn'		: 'none',
                        'transitionOut'		: 'none',
                        'type'		        : 'iframe'

                    });

            });

        </script>    
