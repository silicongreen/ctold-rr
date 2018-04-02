<?php
    include '../config.php';
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $school_id = $request->school_id;
    $school_domain = $request->school_domain;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], "champs21_school");
    $conn->set_charset("utf8");
    
    $result = $conn->query("SELECT * FROM `courses` WHERE school_id = " . $school_id . " GROUP by course_name ORDER BY code"); //and is_deleted = 0 
   
    $outp = array();
    $j = 0;
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $i = 0;
        $has_student = false; 
        $a_data = array();
        $tmp = array();
        $tmp = $rs;
        
        $tmp['sections'] = array();
        $tmp['sections'][$i] = array(
            'id'            => 0,
            'section_name'  => "All"
        );
        
        $course_ids = "";
        $res1 = $conn->query("SELECT id FROM `courses` WHERE course_name = '" . $rs['course_name'] . "' and school_id = " . $school_id . "");// and is_deleted = 0
        while( $rs1 = $res1->fetch_array(MYSQLI_ASSOC)) 
        {
            $course_ids .= $rs1['id'] . ",";
        }
        $course_ids = rtrim($course_ids, ",");
        
        $resStudent = $conn->query("SELECT * FROM `students` WHERE school_id = " . $school_id . " AND batch_id IN (SELECT id FROM batches WHERE course_id IN (" . $course_ids . "))");
        
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
            $has_student = true;
            $aDonutData = array();
            $aDonutData['label'] = "Student Count";
            //$aDonutData['value'] = (int) $rs['emp_count'];
            $aDonutData['value'] = $num_students;
            $aDonutData['color'] = randomColor(20,150);//"#" . dechex(mt_rand(0x000000, 0xFFFFFF));

            $a_data[] = $aDonutData;

            $current_date = date("Y-m-d 23:59:59");
            $start_date = date("Y-m-d 00:00:01", strtotime("-10 day", strtotime($current_date)));
            $end_date = $current_date;

            $resAssignment = $conn->query("SELECT * FROM `assignments` a INNER JOIN subjects s ON a.subject_id = s.id
                                           WHERE a.school_id = " . $school_id . " AND a.created_at between '" .  $start_date . "' and '" . $end_date . "' 
                                           AND s.batch_id IN (SELECT id FROM batches WHERE course_id IN (" . $course_ids . "))");
            
            if ( $resAssignment->num_rows == 0)
            {
                $num_assignment = 0;
                //$num_assignment = (int) rand(10, 21);
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

            $resClasswork = $conn->query("SELECT * FROM `classworks` c INNER JOIN subjects s ON c.subject_id = s.id
                                           WHERE c.school_id = " . $school_id . " AND c.created_at between '" .  $start_date . "' and '" . $end_date . "' 
                                           AND s.batch_id IN (SELECT id FROM batches WHERE course_id IN (" . $course_ids . "))");

            if ( $resClasswork->num_rows == 0)
            {
                $num_classworks = 0;
                //$num_classworks = (int) rand(10, 21);
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
                $num_lessonplan = 0;
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

            $tmp['sections'][$i]['donutData'][] = $a_data;
            //$tmp['donutData'] = $a_data;
            $outp[$j] = $tmp;
        }
        
        $res1 = $conn->query("SELECT id, section_name FROM `courses` WHERE course_name = '" . $rs['course_name'] . "' and school_id = " . $school_id . "");// and is_deleted = 0
        $i++;
        while( $rs1 = $res1->fetch_array(MYSQLI_ASSOC)) 
        {
            $a_data = array();
            $has_section_student = false; 
            $course_id = $rs1['id'];
            
            $resStudent = $conn->query("SELECT * FROM `students` WHERE school_id = " . $school_id . " AND batch_id IN (SELECT id FROM batches WHERE course_id = " . $course_id . ")");
        
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
                $has_student = true;
                $has_section_student = true; 
                $aDonutData = array();
                $aDonutData['label'] = "Student Count";
                //$aDonutData['value'] = (int) $rs['emp_count'];
                $aDonutData['value'] = $num_students;
                $aDonutData['color'] = randomColor(20,150);//"#" . dechex(mt_rand(0x000000, 0xFFFFFF));

                $a_data[] = $aDonutData;

                $current_date = date("Y-m-d 23:59:59");
                $start_date = date("Y-m-d 00:00:01", strtotime("-10 day", strtotime($current_date)));
                $end_date = $current_date;

                $resAssignment = $conn->query("SELECT * FROM `assignments` a INNER JOIN subjects s ON a.subject_id = s.id
                                               WHERE a.school_id = " . $school_id . " AND a.created_at between '" .  $start_date . "' and '" . $end_date . "' 
                                               AND s.batch_id IN (SELECT id FROM batches WHERE course_id = " . $course_id . ")");

                if ( $resAssignment->num_rows == 0)
                {
                    $num_assignment = 0;
                    //$num_assignment = (int) rand(10, 21);
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

                $resClasswork = $conn->query("SELECT * FROM `classworks` c INNER JOIN subjects s ON c.subject_id = s.id
                                               WHERE c.school_id = " . $school_id . " AND c.created_at between '" .  $start_date . "' and '" . $end_date . "' 
                                               AND s.batch_id IN (SELECT id FROM batches WHERE course_id = " . $course_id . ")");

                if ( $resClasswork->num_rows == 0)
                {
                    $num_classworks = 0;
                    //$num_classworks = (int) rand(10, 21);
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
                    $num_lessonplan = 0;
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

                $tmp['sections'][$i] = $rs1;
                $tmp['sections'][$i]['donutData'][] = $a_data;
                $i++;
                
            }
            if ( $has_section_student )
            {
                $outp[$j] = $tmp;
            }
        }
        if ( $has_student )
        {
            $j++;
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