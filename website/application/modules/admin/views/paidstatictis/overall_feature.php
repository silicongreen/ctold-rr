
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
                
                    <div class="box grid_16" id="change_data_ajax">
                        <div class="block">
                            <h2 class="section"><span class="loading-msg">Loading Data This will take some time...</span></h2>
                            <div class="CSSTableGenerator" >
                                <table   style="width: 100%;">

                                    <tr class="even">
                                        <td>User Type</td>
                                        <td>Homework</td>
                                        <td>Attendance/report</td>
                                        <td>Exam</td>
                                        <td>Class Routines</td>
                                        <td>Events</td>
                                        <td>Notice</td>
                                        <td>Leave</td>
                                        <td>Quiz</td>
                                        <td>lesson_plan</td>
                                        <td>Syllabus</td>
                                        <td>Meetings</td>
                                    </tr>
                                    <?php foreach ($user_type as $key=>$value): ?>
                                    <?php $index = $key; ?>
                                    <tr class="even">
                                        <td><?php echo $value; ?></td>
                                        <?php echo create_html_td($stat_homework,$index); ?>
                                        <?php echo create_html_td($stat_attendence,$index,"at"); ?>
                                        <?php echo create_html_td($stat_exams,$index,'ex'); ?>
                                        <?php echo create_html_td($stat_class_routines,$index,'cr'); ?>
                                        <?php echo create_html_td($stat_events,$index,'ev'); ?>
                                        <?php echo create_html_td($stat_notice,$index,'no'); ?>
                                        <?php echo create_html_td($stat_leave,$index,'le'); ?> 
                                        <?php echo create_html_td($stat_quize,$index,'qu'); ?> 
                                        <?php echo create_html_td($stat_lesson_plan,$index,'lp'); ?>
                                        <?php echo create_html_td($stat_syllabus,$index,'sy'); ?>
                                        <?php echo create_html_td($stat_mettings,$index,'me'); ?>
                                    </tr>  
                                    <?php endforeach; ?>

                                    

                                </table>
                            </div>     


                        </div>
                    </div>    
               



            </div>

        </div>

        <script type="text/javascript">

            var startDate_stat = moment();
            var endDate_stat = moment();
            $(document).ready(function () {
                $(document).on("change", "#select_school", function () {
                    $(".loading-msg").show();
                    $.post($("#base_url").val() + "admin/paidstatictis/new_stat_feature", {school: $("#select_school").val(), start_date: startDate_stat.format("YYYY-MM-DD"), end_date: endDate_stat.format("YYYY-MM-DD")})
                            .done(function (data) {
                                $("#change_data_ajax").html(data);
                                $(".loading-msg").hide();
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
                    $.post($("#base_url").val() + "admin/paidstatictis/new_stat_feature", {school: $("#select_school").val(), start_date: startDate_stat.format("YYYY-MM-DD"), end_date: endDate_stat.format("YYYY-MM-DD")})
                            .done(function (data) {
                                $("#change_data_ajax").html(data);
                            });

                }
                );

                $('.dateranger_stat span').html(moment().format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
            });
            
            $(document).on("click", ".user_full_stat", function () {

                var idFilter = this.id;
                var $filterIdArray = idFilter.split("_");
                $.fancybox({
                    'width': "40%",
                    'height': "60%",
                    'autoScale': true,
                    'href': $("#base_url").val() + "admin/paidstatictis/full_stat_feature/" + $("#select_school").val() + "/" + $filterIdArray[0] + "/" + startDate_stat.format("YYYY-MM-DD") + "/" + endDate_stat.format("YYYY-MM-DD")+"/"+$filterIdArray[1],
                    'title': false,
                    'transitionIn': 'none',
                    'transitionOut': 'none',
                    'type': 'iframe'

                });

            });



            

        </script>   
        <style>
            .loading-msg
            {
                display:none;
            }
            .CSSTableGenerator {
                margin:0px;padding:0px;
                width:100%;
                box-shadow: 10px 10px 5px #888888;
                border:1px solid #000000;

                -moz-border-radius-bottomleft:0px;
                -webkit-border-bottom-left-radius:0px;
                border-bottom-left-radius:0px;

                -moz-border-radius-bottomright:0px;
                -webkit-border-bottom-right-radius:0px;
                border-bottom-right-radius:0px;

                -moz-border-radius-topright:0px;
                -webkit-border-top-right-radius:0px;
                border-top-right-radius:0px;

                -moz-border-radius-topleft:0px;
                -webkit-border-top-left-radius:0px;
                border-top-left-radius:0px;
            }.CSSTableGenerator table{
                border-collapse: collapse;
                border-spacing: 0;
                width:100%;
                height:100%;
                margin:0px;padding:0px;
            }.CSSTableGenerator tr:last-child td:last-child {
                -moz-border-radius-bottomright:0px;
                -webkit-border-bottom-right-radius:0px;
                border-bottom-right-radius:0px;
            }
            .CSSTableGenerator table tr:first-child td:first-child {
                -moz-border-radius-topleft:0px;
                -webkit-border-top-left-radius:0px;
                border-top-left-radius:0px;
            }
            .CSSTableGenerator table tr:first-child td:last-child {
                -moz-border-radius-topright:0px;
                -webkit-border-top-right-radius:0px;
                border-top-right-radius:0px;
            }.CSSTableGenerator tr:last-child td:first-child{
                -moz-border-radius-bottomleft:0px;
                -webkit-border-bottom-left-radius:0px;
                border-bottom-left-radius:0px;
            }.CSSTableGenerator tr:hover td{

            }
            .CSSTableGenerator tr:nth-child(odd){ background-color:#e5e5e5; }
            .CSSTableGenerator tr:nth-child(even)    { background-color:#ffffff; }.CSSTableGenerator td{
                vertical-align:middle;


                border:1px solid #000000;
                border-width:0px 1px 1px 0px;
                text-align:center;
                padding:9px;
                font-size:14px;
                font-family:Arial;
                font-weight:bold;
                color:#000000;
            }.CSSTableGenerator tr:last-child td{
                border-width:0px 1px 0px 0px;
            }.CSSTableGenerator tr td:last-child{
                border-width:0px 0px 1px 0px;
            }.CSSTableGenerator tr:last-child td:last-child{
                border-width:0px 0px 0px 0px;
            }
            .CSSTableGenerator tr:first-child td{
                background:-o-linear-gradient(bottom, #cccccc 5%, #b2b2b2 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #cccccc), color-stop(1, #b2b2b2) );
                background:-moz-linear-gradient( center top, #cccccc 5%, #b2b2b2 100% );
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#cccccc", endColorstr="#b2b2b2");	background: -o-linear-gradient(top,#cccccc,b2b2b2);

                background-color:#cccccc;
                border:0px solid #000000;
                text-align:center;
                border-width:0px 0px 1px 1px;
                font-size:17px;
                font-family:Arial;
                font-weight:bold;
                color:#000000;
            }
            .CSSTableGenerator tr:first-child:hover td{
                background:-o-linear-gradient(bottom, #cccccc 5%, #b2b2b2 100%);	background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #cccccc), color-stop(1, #b2b2b2) );
                background:-moz-linear-gradient( center top, #cccccc 5%, #b2b2b2 100% );
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr="#cccccc", endColorstr="#b2b2b2");	background: -o-linear-gradient(top,#cccccc,b2b2b2);

                background-color:#cccccc;
            }
            .CSSTableGenerator tr:first-child td:first-child{
                border-width:0px 0px 1px 0px;
            }
            .CSSTableGenerator tr:first-child td:last-child{
                border-width:0px 0px 1px 1px;
            }
        </style>
