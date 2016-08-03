
<div id="pjax">
    <div id="wrapper" data-adminica-nav-top="1" data-adminica-side-top="1">
        <?php
        $widget = new Widget;

        $widget->run('sidebar');
        ?>

        <input type="hidden" id="modelwidth" value="80%" >

        <input type="hidden" id="modelheight" value="73%" >

        <input type="hidden" id="model2width2" value="30%" >

        <input type="hidden" id="modelheight2" value="55%" >

        <input type="hidden" id="sorttype" value="desc" >

        <div id="main_container" class="main_container container_16 clearfix">


            <div class="flat_area grid_16">
                <h2>Paid School Statistics</h2>

            </div>
            <?php echo form_open('', array('class' => 'validate_form')); ?>

            <div class="box grid_16" >
                <?php
                $filter_array = array();
                $filter_array[0] = array("School", "form_dropdown", $schools);
                $filter_array[1] = array("User Name", "input");
                $filter_array[2] = array("User Type", "form_dropdown", $user_type);
                $filter_array[3] = array("Action", "input");
                $filter_array[4] = array("Ip", "input");
                $filter_array[5] = array("User Agent", "input");
                $filter_array[6] = array("users_from", "form_dropdown", $users_from);
                $filter_array[7] = array("Date", "input_daterange2");

                create_filter($filter_array);
                ?>


            </div>

            <div class="box grid_16 single_datatable">

                <div id="dt1" class="no_margin"><?php echo $this->table->generate(); ?></div>
            </div>
            <?php echo form_close(); ?> 
        </div>

        <script>
            if ($('.dateranger2').length > 0)
            {

                $('.dateranger2').daterangepicker(
                        {
                            startDate: moment(),
                            endDate: moment(),
                            minDate: '01/01/2014',
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
                    var idFilter = $('.dateranger2').attr("id");
                    var $filterIdArray = idFilter.split("_");
                    $('.dateranger2 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    oTable.fnFilter(start.format('YYYY-MM-DD 00:00:00') + ' - ' + end.format('YYYY-MM-DD 59:00:00'), $filterIdArray[1], 'between');


                }
                );

                $('.dateranger2 span').html(moment().format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));


                var idFilter = $('.dateranger2').attr("id");
                var $filterIdArray = idFilter.split("_");


                oTable.fnFilter(moment().format('YYYY-MM-DD 00:00:00') + ' - ' + moment().format('YYYY-MM-DD 59:00:00'), $filterIdArray[1], 'between');
            }
        </script>    






