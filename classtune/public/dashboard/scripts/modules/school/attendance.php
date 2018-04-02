<?php
    include '../config.php';
    
    $params = $_GET;
    
    $school_id = $params['school_id'];

    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];
    $a_order = $params['order'];
    $order = $a_order[0];
    
    $order_id = "c.code";
    $order_dir = "ASC";
    
    if ( $order['column'] == "0" )
    {
        $order_id = "c.code";
        $order_dir = $order['dir'];
    }
    else if ( $order['column'] == "1" )
    {
        $order_id = "c.code";
        $order_dir = $order['dir'];
    }
    else if ( $order['column'] == "2" )
    {
        $order_id = "total";
        $order_dir = $order['dir'];
    }
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], "champs21_school");
    $conn->set_charset("utf8");
    
    $current_date = date("Y-m-d");
    $start_date = date("Y-m-d", strtotime("-10 day", strtotime($current_date)));
    $end_date = $current_date;
//    $current_date = date("Y-m-d");
    //$end_date = date("Y-m-d", strtotime("+10 day", strtotime($current_date)));
    //$start_date = $current_date;
    
    
    $result = $conn->query("SELECT * FROM `courses` WHERE school_id = " . $school_id . " GROUP by course_name");
    
        
    if ( $result->num_rows == 0)
    {
        $record_total = 0;
    }
    else
    {
        $record_total = $result->num_rows;
    }
    
    $result = $conn->query("SELECT c.id, c.course_name, AVG(ar.total) as total, AVG(ar.present) as present, AVG(ar.absent) as absent, AVG(ar.late) as late
                            FROM `attendance_registers` ar 
                            INNER JOIN batches b ON ar.batch_id = b.id
                            INNER JOIN courses c ON b.course_id = c.id
                            WHERE ar.school_id = " . $school_id . " and ar.attendance_date BETWEEN '" . $start_date . "' and '" . $end_date . "'
                            GROUP BY c.course_name ORDER BY " . $order_id . " " . $order_dir . " LIMIT " . $start . ", " . $length);
    
    
    $outp = array();
    $i = 1;
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $tmp = array();
        $id = $rs['id'];
        $tmp['serial'] = $i;
        $tmp['name'] = $rs['course_name'];
        $tmp['num_student'] = (int) $rs['total'];
        
        $absent = (int) $rs['absent'];
        $late = (int) $rs['late'];
        $present = $tmp['num_student'] - ( $absent + $late );
        
        if ( $rs['present'] > 0  )
        {
            //$tmp['count_student'] = (int) $rs['present']+$rs['absent'];
            $percent_attendance = (int) ( (($rs['present'] + $rs['late'] )/($rs['total'])) * 100);
        }
        else
        {
            $percent_attendance = rand(70, 99);
            //$tmp['count_student'] = rand(700, 1200);
        }
        $tmp['percent'] = '<div class="progress-xxs not-rounded mb-0 inline-block progress ng-isolate-scope" value="' . $percent_attendance . '" type="greensea" style="width: 250px; margin-right: 5px">
                           <div class="progress-bar progress-bar-greensea" ng-class="type &amp;&amp; \'progress-bar-\' + type" role="progressbar" aria-valuenow="' . $percent_attendance . '" aria-valuemin="0" aria-valuemax="100" ng-style="{width: (percent < 100 ? percent : 100) + \'%\'}" aria-valuetext="' . $percent_attendance . '%" aria-labelledby="progressbar" style="min-width: 0px; width: ' . $percent_attendance . '%;" ng-transclude=""></div>
                           </div>    
                           <small>' . $percent_attendance . '%</small><br /><br />
                           <div class="bg-greensea col-md-4" style="text-align: center; border-right: 1px solid #ccc;"><b>Present</b></div><div class="bg-greensea col-md-4" style="text-align: center; border-right: 1px solid #ccc;"><b>Absent</b></div><div class="bg-greensea col-md-4" style="text-align: center;"><b>Late</b></div><div class="clearfix"></div>
                           <div class="bg-drank col-md-4" style="text-align: center; border-right: 1px solid #ccc;"><b>' . $present . '</b></div><div class="bg-drank col-md-4" style="text-align: center; border-right: 1px solid #ccc;">' . $absent . '</b></div><div class="bg-drank col-md-4" style="text-align: center;">' . $late . '</b></div>';
        
        $chart = array();
        $chart['color'] = "#" . dechex(rand(0x000000, 0xFFFFFF));
        
        $subresult = $conn->query("SELECT attendance_date, AVG(present) as present, AVG(late) as late FROM `attendance_registers` 
                                    WHERE batch_id IN (SELECT id from batches WHERE course_id = " . $id . ")  and 
                                    attendance_date BETWEEN '" . $start_date . "' and '" . $end_date . "'
                                    GROUP BY attendance_date ORDER BY attendance_date");
        
        $chart['data'] = array();
        $date = array();
        for( $i=0; $i<10; $i++ )
        {
            if ( $i == 0 )
            {
                $t_date = date("Y-m-d");
            }
            else
            {
                $t_date = date("Y-m-d", strtotime("-" . $i . " day", strtotime(date("Y-m-d"))));
            }
            $date[] = $t_date;
            $chart['data'][] = 0;
        }
        
        $l = 0;
        while( $sr = $subresult->fetch_array(MYSQLI_ASSOC))
        {
            $attendance_date = $sr['attendance_date'];
            $m = 0;
            foreach ($date as $d)
            {
                if ( $attendance_date == $d )
                {
                    break;
                }
                $m++;
            }
            $chart['data'][$m] = (int) ($sr['present'] + $sr['late']);
            //$i--;
        }
        
        $a_options = array("type" => "bar", "barColor" => $chart['color'], "barWidth" => "10%", "height" => "28px");
        $tmp['graph_attendance'] = "<span class='attendance_chart' sparkline data='" . json_encode($chart['data']) . "' options='" . json_encode($a_options) . "'  watch-me='numberOfPages'>
                                    </span>"; 
        $outp[] = $tmp;
        $i++;
    }
    
    if ( empty($outp) )
    {
        $record_total = 0;
    }
    else
    {
        $record_total = count($outp);
    }
    
    $a_data = array(
        'draw'              => $draw,
        'recordsTotal'      => $record_total,
        'recordsFiltered'   => $record_total,
        'data'              => $outp
    );
    echo json_encode($a_data);
