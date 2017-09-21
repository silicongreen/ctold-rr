<?php
    include '../config.php';
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    
    $result = $conn->query("SELECT count(*) as count FROM students");

    if ( $result->num_rows == 0)
    {
        $student_record_total = 0;
    }
    else
    {
        $rs = $result->fetch_array(MYSQLI_ASSOC);
        $student_record_total = $rs['count'];
    }
    
    $result = $conn->query("SELECT count(*) as count FROM employees");

    if ( $result->num_rows == 0)
    {
        $employee_record_total = 0;
    }
    else
    {
        $rs = $result->fetch_array(MYSQLI_ASSOC);
        $employee_record_total = $rs['count'];
    }
    
    $result = $conn->query("SELECT count(*) as count FROM schools");

    if ( $result->num_rows == 0)
    {
        $university_record_total = 0;
    }
    else
    {
        $rs = $result->fetch_array(MYSQLI_ASSOC);
        $school_record_total = $rs['count'];
    }
    
    $result = $conn->query("SELECT count(*) as count FROM divisions");

    if ( $result->num_rows == 0)
    {
        $division_record_total = 0;
    }
    else
    {
        $rs = $result->fetch_array(MYSQLI_ASSOC);
        $division_record_total = $rs['count'];
    }
    
    $result = $conn->query("SELECT count(*) as count FROM districts");

    if ( $result->num_rows == 0)
    {
        $district_record_total = 0;
    }
    else
    {
        $rs = $result->fetch_array(MYSQLI_ASSOC);
        $district_record_total = $rs['count'];
    }
    
    $outp['student'] = number_format($student_record_total);
    $outp['employee'] = number_format($employee_record_total);
    $outp['school'] = number_format($school_record_total);
    $outp['division'] = $division_record_total;
    $outp['district'] = $district_record_total;
    
    echo json_encode($outp);