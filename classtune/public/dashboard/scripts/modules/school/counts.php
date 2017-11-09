<?php
    include '../config.php';
    
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $school_id = $request->school_id;
    $is_thai_school = $request->is_thai_school;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], "champs21_school");
    $conn->set_charset("utf8");
    
    $conn1 = new mysqli($db_m['host'],$db_m['username'], $db_m['password'], $db_m['dbname']);
    $conn1->set_charset("utf8");
    
    if ( $is_thai_school == 0 )
    {
        $result = $conn->query("SELECT count(*) as count FROM students WHERE school_id = " . $school_id);
    }
    else
    {
        $result = $conn1->query("SELECT count(*) as count FROM students WHERE school_id = " . $school_id);
    }

    if ( $result->num_rows == 0)
    {
        $student_record_total = 0;
    }
    else
    {
        $rs = $result->fetch_array(MYSQLI_ASSOC);
        $student_record_total = $rs['count'];
    }
    
    if ( $is_thai_school == 0 )
    {
        $result = $conn->query("SELECT count(*) as count FROM employees WHERE school_id = " . $school_id);
    }
    else
    {
        $result = $conn1->query("SELECT count(*) as count FROM employees WHERE school_id = " . $school_id);
    }

    if ( $result->num_rows == 0)
    {
        $employee_record_total = 0;
    }
    else
    {
        $rs = $result->fetch_array(MYSQLI_ASSOC);
        $employee_record_total = $rs['count'];
    }
    
    $result = $conn->query("SELECT * FROM attendance_registers WHERE attendance_date = '" . date("Y-m-d") . "' AND school_id = " . $school_id);

    if ( $result->num_rows == 0)
    {
        $student_attendance = false;
    }
    else
    {
        $student_attendance = true;
    }
    
    if ( $student_attendance )
    {
        $result = $conn->query("SELECT count(*) as count FROM attendances WHERE month_date = '" . date("Y-m-d") . "' AND school_id = " . $school_id);

        if ( $result->num_rows == 0)
        {
            $absent_student_total = 0;
        }
        else
        {
            $rs = $result->fetch_array(MYSQLI_ASSOC);
            $absent_student_total = $rs['count'];
        }
    }
    
    $result = $conn->query("SELECT count(*) as count FROM employee_attendances WHERE attendance_date = '" . date("Y-m-d") . "' AND school_id = " . $school_id);

    if ( $result->num_rows == 0)
    {
        $absent_employee_total = 0;
    }
    else
    {
        $rs = $result->fetch_array(MYSQLI_ASSOC);
        $absent_employee_total = $rs['count'];
    }
    
    $start_date = date("Y-m-d 00:00:01");
    $end_date = date("Y-m-d 23:59:59");
    $result = $conn->query("SELECT count(*) as count FROM assignments WHERE created_at BETWEEN '" . $start_date  . "' AND '" . $end_date . "' AND school_id = " . $school_id);

    if ( $result->num_rows == 0)
    {
        $homework_total = 0;
    }
    else
    {
        $rs = $result->fetch_array(MYSQLI_ASSOC);
        $homework_total = $rs['count'];
    }
    
    $outp['student'] = number_format($student_record_total);
    $outp['employee'] = number_format($employee_record_total);
    
    if ( $student_attendance )
    {
        $outp['student_attendance'] = $student_attendance;
        $outp['student_present'] = $student_record_total - $absent_student_total;
        $outp['employee_present'] = $employee_record_total - $absent_employee_total;
    }
    else
    {
        $outp['student_attendance'] = $student_attendance;
        $outp['student_present'] = "no class today";
        $outp['employee_present'] = "no class today";
    }
    $outp['homework'] = $homework_total;
    
    echo json_encode($outp);