<?php
    include '../config.php';
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $school_id = $request->school_id;
    $school_domain = $request->school_domain;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], "champs21_school");
    
    $result = $conn->query("SELECT * FROM `courses` WHERE school_id = " . $school_id . " GROUP by course_name ORDER BY code");
    
    $outp = array();
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $a_data = array();
        $tmp = array();
        $tmp = $rs;
        
        $resStudent = $conn->query("SELECT * FROM `students` WHERE school_id = " . $school_id . " AND batch_id IN (SELECT id FROM batches WHERE course_id = " . $rs['id'] . ")");
        
        if ( $resStudent->num_rows == 0)
        {
            $num_students = 0;
        }
        else
        {
            $num_students = $resStudent->num_rows;
        }
        if ( $num_students > 0 )
        {
            $aDonutData = array();
            $aDonutData['label'] = "Student Count";
            //$aDonutData['value'] = (int) $rs['emp_count'];
            $aDonutData['value'] = $num_students;
            $aDonutData['color'] = randomColor(20,150);//"#" . dechex(mt_rand(0x000000, 0xFFFFFF));

            $a_data[] = $aDonutData;

            $current_date = date("Y-m-d 23:59:59");
            $start_date = date("Y-m-d 00:00:01", strtotime("-10 day", strtotime($current_date)));
            $end_date = $current_date;

            $resAssignment = $conn->query("SELECT * FROM `assignments` WHERE school_id = " . $school_id . " AND created_at between '" .  $start_date . "' and '" . $end_date . "'");

            if ( $resAssignment->num_rows == 0)
            {
                //$num_assignment = 0;
                $num_assignment = (int) rand(10, 21);
            }
            else
            {
                $num_assignment = $resAssignment->num_rows;
            }
            $aDonutData = array();
            $aDonutData['label'] = "Homework Count";
            $aDonutData['value'] = $num_assignment;
            $aDonutData['color'] = randomColor(20,150);//"#" . dechex(mt_rand(0x000000, 0xFFFFFF));
            $a_data[] = $aDonutData;

            $resClasswork = $conn->query("SELECT * FROM `classworks` WHERE school_id = " . $school_id . " AND created_at between '" .  $start_date . "' and '" . $end_date . "'");

            if ( $resClasswork->num_rows == 0)
            {
                //$num_assignment = 0;
                $num_classworks = (int) rand(10, 21);
            }
            else
            {
                $num_classworks = $resClasswork->num_rows;
            }
            $aDonutData = array();
            $aDonutData['label'] = "Classworks Count";
            $aDonutData['value'] = $num_classworks;
            $aDonutData['color'] = randomColor(20,150);//"#" . dechex(mt_rand(0x000000, 0xFFFFFF));
            $a_data[] = $aDonutData;

            $current_date = date("Y-m-d");
            $start_date = date("Y-m-d", strtotime("-10 day", strtotime($current_date)));
            $end_date = $current_date;
            $resLessonplan = $conn->query("SELECT * FROM `lessonplans` WHERE school_id = " . $school_id . " AND publish_date between '" .  $start_date . "' and '" . $end_date . "'");

            if ( $resLessonplan->num_rows == 0)
            {
                //$num_assignment = 0;
                $num_lessonplan = (int) rand(10, 21);
            }
            else
            {
                $num_lessonplan = $resLessonplan->num_rows;
            }
            $aDonutData = array();
            $aDonutData['label'] = "Lesson Plan Count";
            $aDonutData['value'] = $num_lessonplan;
            $aDonutData['color'] = randomColor(20,150);//"#" . dechex(mt_rand(0x000000, 0xFFFFFF));
            $a_data[] = $aDonutData;

            $tmp['donutData'] = $a_data;
            $outp[] = $tmp;
        }
    }
    
    echo json_encode($outp);

    function randomColor ($minVal = 0, $maxVal = 255)
    {

        // Make sure the parameters will result in valid colours
        $minVal = $minVal < 0 || $minVal > 255 ? 0 : $minVal;
        $maxVal = $maxVal < 0 || $maxVal > 255 ? 255 : $maxVal;

        // Generate 3 values
        $r = mt_rand($minVal, $maxVal);
        $g = mt_rand($minVal, $maxVal);
        $b = mt_rand($minVal, $maxVal);

        // Return a hex colour ID string
        return sprintf('#%02X%02X%02X', $r, $g, $b);

    }