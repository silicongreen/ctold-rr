<?php
    include '../config.php';
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $university_id = $request->university_id;
    $department_id = $request->department_id;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    $conn->set_charset("utf8");
    
    $result = $conn->query("SELECT grade, COUNT( id ) as count
                            FROM `student_results` WHERE  `dept_id` = " . $department_id . "
                            AND university_id = " . $university_id . " 
                            GROUP BY grade");
    
    $res = $conn->query("SELECT grade, COUNT( id ) as count
                            FROM `student_results` WHERE  `dept_id` = " . $department_id . "
                            AND university_id = " . $university_id . " 
                            GROUP BY dept_id");
    $detailsData = array();
    $rs1 = $res->fetch_array(MYSQLI_ASSOC);
    $count_student = $rs1['count'];
    
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $rs['percent'] = ($rs['count'] / $count_student) * 100;
        $detailsData['details'][] = $rs;
    }
    
    $result = $conn->query("SELECT s.id, s.name, s.email, s.phone_no, s.profile_photo, s.short_desc, s.description, sr.number, sr.grade
                            FROM  `student_results` sr INNER JOIN students s ON s.id = sr.student_id
                            WHERE sr.university_id = " . $university_id . "
                            AND sr.dept_id = " . $department_id . " 
                            ORDER BY number DESC LIMIT 5 ");
    
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $detailsData['students'][] = $rs;
    }
    
    echo json_encode($detailsData);	