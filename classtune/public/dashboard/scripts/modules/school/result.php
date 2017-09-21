<?php
    include '../config.php';
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $division_id = $request->division_id;
    $district_id = $request->district_id;
    $class_id  = $request->class_id;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    
    if ( $district_id == 0 )
    {
        $result = $conn->query("SELECT s.id, spd.class_id, s.subject_name, spd.below_50, 
                                spd.below_70, spd.above_70, spd.num_students, 
                                spd.num_districts, spd.num_schools
                                FROM `student_pass_division` spd
                                INNER JOIN subjects s ON spd.subject_id = s.id
                                WHERE spd.division_id = " . $division_id . "
                                AND spd.class_id = " . $class_id);
    }
    else if ( $district_id > 0 )
    {
        $result = $conn->query("SELECT s.id, spd.class_id, s.subject_name, spd.below_50, spd.below_70, 
                                spd.above_70, spd.num_students, spd.num_schools
                                FROM `student_pass_district` spd
                                INNER JOIN subjects s ON spd.subject_id = s.id
                                WHERE spd.district_id = " . $district_id . "
                                AND spd.class_id = " . $class_id);
    }
    $resultData = array();
    $k = 10;
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $ar_tmp = array();
        $below_50 = $rs['below_50'];
        $ar_tmp = array($k, $rs['below_50']);
        $resultData['student_below_50'][] = $ar_tmp;
        
        $ar_tmp = array();
        $below_70 = $rs['below_70'];
        if ( $below_50 + $below_70 > 100 )
        {
            $below_70 = 100 - $below_50;
        }
        $ar_tmp = array($k, $below_70);
        $resultData['student_below_70'][] = $ar_tmp;
        
        $ar_tmp = array();
        $above_70 = $rs['above_70'];
        
        if ( $below_50 + $below_70 + $above_70 > 100 )
        {
            $above_70 = 100 - ( $below_50 + $below_70);
        }
        else if ($below_50 + $below_70 + $above_70 < 100)
        {
            $remaining = 100 - ( $below_50 + $below_70 );
            $above_70 = $remaining;
        }
        
        $ar_tmp = array($k, $above_70);
        $resultData['student_above_70'][] = $ar_tmp;
        
        $ar_tmp = array();
        $ar_tmp = array($k, $rs['subject_name']);
        $resultData['subject_tricks'][] = $ar_tmp;
        
        $num_districts = 0;
        if ( isset($rs['num_districts']) )
        {
            $num_districts = $rs['num_districts'];
        }
        $resultData['summary'][] = array($rs['num_students'], $rs['num_schools'], $num_districts);
        $k += 3;
    }
    
    
    echo json_encode($resultData);	