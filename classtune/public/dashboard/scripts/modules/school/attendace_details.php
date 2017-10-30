<?php
    include '../config.php';
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $school_id = $request->school_id;
    $school_domain = $request->school_domain;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], "champs21_school");
    $result = $conn->query("SELECT count( s.id ) as student_count, c.course_name, c.id
                            FROM `students` s
                            INNER JOIN batches b ON s.batch_id = b.id
                            INNER JOIN courses c ON c.id = b.course_id
                            WHERE s.school_id = " . $school_id . "
                            GROUP BY c.course_name ORDER BY c.code");
    $resultData = array();
    
    $k = 1;
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $ar_tmp = array();
        $ar_tmp = array($k, $rs['student_count'] +  + 30);
        $resultData['student_counts'][] = $ar_tmp;
        
        $ar_tmp = array();
        $ar_tmp = array($rs['id'], $rs['course_name']);
        $resultData['courses'][] = $ar_tmp;
        $k++;
    }
    
    $current_date = date("Y-m-d");
    $start_date = date("Y-m-d", strtotime("-10 day", strtotime($current_date)));
    $end_date = $current_date;
    
    $result = $conn->query("SELECT ar.attendance_date, ar.batch_id, AVG(ar.total) as total, 
                            AVG(ar.present) as present, AVG(ar.absent) as absent, 
                            AVG(ar.late) as late, c.course_name FROM `attendance_registers` ar 
                            INNER JOIN batches b ON ar.batch_id = b.id
                            INNER JOIN courses c ON c.id = b.course_id
                            WHERE ar.school_id = " . $school_id . " and 
                            ar.attendance_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'
                            GROUP BY c.course_name  ORDER BY c.code");
    
    $k = 1;
    $l = 10;
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $ar_tmp = array();
        $ar_tmp = array($k, (int) $rs['present']);
        $resultData['student_attendace'][] = $ar_tmp;
        
        $ar_tmp = array();
        $ar_tmp = array($l, (int) $rs['present']);
        $resultData['student_present'][] = $ar_tmp;
        
        $ar_tmp = array();
        $ar_tmp = array($l, (int) $rs['absent']);
        $resultData['student_absent'][] = $ar_tmp;
        
        $ar_tmp = array();
        $ar_tmp = array($l, (int) $rs['late']);
        $resultData['student_late'][] = $ar_tmp;
        
        $ar_tmp = array();
        $ar_tmp = array($l, $rs['course_name']);
        $resultData['courses_attendance'][] = $ar_tmp;
        
        $k++;
        $l += 4;
    }
    
    
    $start_date = date("Y-m-01");
    $end_date = $current_date;
    
    $result = $conn->query("SELECT * FROM `students` WHERE school_id = " . $school_id);
    if ( $result->num_rows == 0)
    {
        $num_students = 0;
    }
    else
    {
        $num_students = $result->num_rows;
    }
    $resultData['attendance_statistics'] = array();
    $resultData['attendance_statistics']['total_students'] = $num_students;
    
    $result = $conn->query("SELECT * FROM `attendance_registers` 
                            WHERE school_id = " . $school_id . " and 
                            attendance_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'");
        
    $class_month = true;
    if ( $result->num_rows == 0)
    {
        $class_month = false;
        $resultData['attendance_statistics']['avg_attendace'] = ' 0 ';
    }
    else
    {
        $result = $conn->query("SELECT AVG(ar.present) as present, AVG(ar.absent) as absent FROM `attendance_registers` ar 
                            WHERE ar.school_id = " . $school_id . " and 
                            ar.attendance_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'
                            GROUP BY ar.school_id");
    
        $rs = $result->fetch_array(MYSQLI_ASSOC);
        $resultData['attendance_statistics']['avg_attendace'] = $num_students - (int) $rs['absent'];
    }
        
    
    $now = time(); // or your date as well
    $datediff = $now - $start_date;

    $num_days = floor($datediff / (60 * 60 * 24));

    $number_of_friday = 0;
    
    $resultData['students_top'] = array();
    
    if ( $class_month )
    {
        $result = $conn->query("SELECT s.admission_no, s.class_roll_no, s.first_name, s.last_name,  
                                c.course_name, c.section_name, b.name, s.photo_file_name, s.phone2, s.id FROM `students` s
                                INNER JOIN batches b on b.id = s.batch_id
                                INNER JOIN courses c on b.course_id = c.id
                                WHERE s.school_id = " . $school_id . " and 
                                s.id NOT IN (SELECT student_id from attendances WHERE
                                school_id = " . $school_id . " AND month_date
                                BETWEEN '" . $start_date . "' AND '" . $end_date . "') LIMIT 10");

        $ar_students = array();
        $i = 1;
        $ar_student_data = array();
        $str_school_id = $school_id;
        while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
        {
            $server_name = $_SERVER['SERVER_NAME'];
            $server_name = "http://" . str_replace("dashboard", $school_domain, $server_name);
            $ar_tmp = array();
            $ar_tmp['name'] = $rs['first_name'] . " " . $rs['last_name'];
            $ar_tmp['admission_no'] = $rs['admission_no'];
            $ar_tmp['id'] = $rs['id'];
            $ar_tmp['class_roll_no'] = $rs['class_roll_no'];
            $ar_tmp['class_name'] = $rs['course_name'];
            $ar_tmp['section_name'] = $rs['section_name'];
            $ar_tmp['phone'] = $rs['phone2'];

            if ( !is_null($rs['photo_file_name']) || !empty($rs['photo_file_name']) )
            {
                if (strlen($school_id) == 1 )
                {
                    $str_school_id = "00" . $school_id;
                }
                else if (strlen($school_id) == 2 )
                {
                    $str_school_id = "0" . $school_id;
                }
                if (strpos($rs['photo_file_name'], '.') === FALSE )
                {
                    $rs['photo_file_name'] = $rs['photo_file_name'] . "jpg";
                }
                $ar_tmp['photo_file_name'] = $server_name . "/uploads/000/000/" . $str_school_id . "/students/photos/" . $rs['id'] . "/original/" . $rs['photo_file_name'];

            }
            else
            {
                $ar_tmp['photo_file_name'] = $server_name . "/images/master_student/profile/default_student.png?1465381441";
            }

            $ar_student_data[] = $ar_tmp;
            if ( $i % 2 == 0 )
            {
                $ar_students[] = $ar_student_data;
                $ar_student_data = array();
            }
            $i++;
        }
        $resultData['students_top'] = $ar_students;
    }
    
    $resultData['employees_top'] = array();
    if ( $class_month )
    {
        $result = $conn->query("SELECT e.employee_number, e.first_name, e.last_name, ep.name,
                                e.photo_file_name, e.mobile_phone, e.id FROM `employees` e
                                INNER JOIN employee_positions ep on ep.id = e.employee_position_id
                                WHERE e.school_id = " . $school_id . " and ep.name != 'System Admin' and
                                e.id NOT IN (SELECT employee_id from employee_attendances WHERE
                                school_id = " . $school_id . " AND attendance_date = '" . $end_date . "') LIMIT 10");

        $ar_employees = array();
        $i = 1;
        $ar_employee_data = array();
        $str_school_id = $school_id;
        while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
        {
            $server_name = $_SERVER['SERVER_NAME'];
            $server_name = "http://" . str_replace("dashboard", $school_domain, $server_name);
            $ar_tmp = array();
            $ar_tmp['name'] = $rs['first_name'] . " " . $rs['last_name'];
            $ar_tmp['employee_number'] = $rs['employee_number'];
            $ar_tmp['id'] = $rs['id'];
            $ar_tmp['employee_position'] = $rs['name'];
            $ar_tmp['mobile_phone'] = $rs['mobile_phone'];

            if ( !is_null($rs['photo_file_name']) || !empty($rs['photo_file_name']) )
            {
                if (strlen($school_id) == 1 )
                {
                    $str_school_id = "00" . $school_id;
                }
                else if (strlen($school_id) == 2 )
                {
                    $str_school_id = "0" . $school_id;
                }
                if (strpos($rs['photo_file_name'], '.') === FALSE )
                {
                    $rs['photo_file_name'] = $rs['photo_file_name'] . "jpg";
                }
                $ar_tmp['photo_file_name'] = $server_name . "/uploads/000/000/" . $str_school_id . "/employees/photos/" . $rs['id'] . "/original/" . $rs['photo_file_name'];

            }
            else
            {
                $ar_tmp['photo_file_name'] = $server_name . "/images/master_student/profile/default_student.png?1465381441";
            }

            $ar_employee_data[] = $ar_tmp;
            if ( $i % 2 == 0 )
            {
                $ar_employees[] = $ar_employee_data;
                $ar_employee_data = array();
            }
            $i++;
        }
        $resultData['employees_top'] = $ar_employees;
    }
    
    $result = $conn->query("SELECT s.admission_no, s.class_roll_no, s.first_name, s.last_name,  
                            c.course_name, c.section_name, b.name, s.photo_file_name, s.phone2, s.id FROM `students` s
                            INNER JOIN batches b on b.id = s.batch_id
                            INNER JOIN courses c on b.course_id = c.id
                            WHERE s.school_id = " . $school_id . " and 
                            s.id IN (SELECT student_id from attendances WHERE
                            school_id = " . $school_id . " AND month_date = '" . $end_date . "') LIMIT 20");
    
    $ar_students = array();
    $i = 1;
    $ar_student_data = array();
    $str_school_id = $school_id;
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $server_name = $_SERVER['SERVER_NAME'];
        $server_name = "http://" . str_replace("dashboard", $school_domain, $server_name);
        $ar_tmp = array();
        $ar_tmp['name'] = $rs['first_name'] . " " . $rs['last_name'];
        $ar_tmp['admission_no'] = $rs['admission_no'];
        $ar_tmp['id'] = $rs['id'];
        $ar_tmp['class_roll_no'] = $rs['class_roll_no'];
        $ar_tmp['class_name'] = $rs['course_name'];
        $ar_tmp['section_name'] = $rs['section_name'];
        $ar_tmp['phone'] = $rs['phone2'];
        
        if ( !is_null($rs['photo_file_name']) || !empty($rs['photo_file_name']) )
        {
            if (strlen($school_id) == 1 )
            {
                $str_school_id = "00" . $school_id;
            }
            else if (strlen($school_id) == 2 )
            {
                $str_school_id = "0" . $school_id;
            }
            if (strpos($rs['photo_file_name'], '.') === FALSE )
            {
                $rs['photo_file_name'] = $rs['photo_file_name'] . "jpg";
            }
            $ar_tmp['photo_file_name'] = $server_name . "/uploads/000/000/" . $str_school_id . "/students/photos/" . $rs['id'] . "/original/" . $rs['photo_file_name'];
            
        }
        else
        {
            $ar_tmp['photo_file_name'] = $server_name . "/images/master_student/profile/default_student.png?1465381441";
        }
        
        $ar_student_data[] = $ar_tmp;
        if ( $i % 2 == 0 )
        {
            $ar_students[] = $ar_student_data;
            $ar_student_data = array();
        }
        $i++;
    }
    if ( ! empty($ar_students) )
    {
        $resultData['students_absent_today'] = $ar_students;
    }
    else
    {
        $resultData['students_absent_today'] = array();
        
        $result = $conn->query("SELECT * FROM `attendance_registers` 
                            WHERE school_id = " . $school_id . " and 
                            attendance_date = '" . $end_date . "'");
        
        $resultData['class_today'] = true;
        if ( $result->num_rows == 0)
        {
            $resultData['class_today'] = false;
        }
        
    }
    
    $result = $conn->query("SELECT e.employee_number, e.first_name, e.last_name, ep.name,
                            e.photo_file_name, e.id, e.mobile_phone FROM `employees` e
                            INNER JOIN employee_positions ep on ep.id = e.employee_position_id
                            WHERE e.school_id = " . $school_id . " and ep.name != 'System Admin' and
                            e.id IN (SELECT employee_id from employee_attendances WHERE
                            school_id = " . $school_id . " AND attendance_date = '" . $end_date . "') LIMIT 20");
    
   
    $ar_employees = array();
    $i = 1;
    $ar_employee_data = array();
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $server_name = $_SERVER['SERVER_NAME'];
        $server_name = "http://" . str_replace("dashboard", $school_domain, $server_name);
        $ar_tmp = array();
        $ar_tmp['name'] = $rs['first_name'] . " " . $rs['last_name'];
        $ar_tmp['employee_number'] = $rs['employee_number'];
        $ar_tmp['id'] = $rs['id'];
        $ar_tmp['employee_position'] = $rs['name'];
        $ar_tmp['mobile_phone'] = $rs['mobile_phone'];
        
        if ( !is_null($rs['photo_file_name']) || !empty($rs['photo_file_name']) )
        {
            if (strlen($school_id) == 1 )
            {
                $str_school_id = "00" . $school_id;
            }
            else if (strlen($school_id) == 2 )
            {
                $str_school_id = "0" . $school_id;
            }
            if (strpos($rs['photo_file_name'], '.') === FALSE )
            {
                $rs['photo_file_name'] = $rs['photo_file_name'] . "jpg";
            }
            $ar_tmp['photo_file_name'] = $server_name . "/uploads/000/000/" . $str_school_id . "/employees/photos/" . $rs['id'] . "/original/" . $rs['photo_file_name'];
            
        }
        else
        {
            $ar_tmp['photo_file_name'] = $server_name . "/images/master_student/profile/default_student.png?1465381441";
        }
        
        $ar_employee_data[] = $ar_tmp;
        if ( $i % 2 == 0 )
        {
            $ar_employees[] = $ar_employee_data;
            $ar_employee_data = array();
        }
        $i++;
    }
   
    if ( empty($ar_employees) )
    {
        $resultData['employees_absent_today'] = array();
        $resultData['class_employee'] = true;
        if ( ! $class_month )
        {
            $resultData['class_employee'] = false;
        }
    }
    else
    {
        $resultData['employees_absent_today'] = $ar_employees;
    }
    
    $result = $conn->query("SELECT s.admission_no, s.class_roll_no, s.first_name, s.last_name,  
                            c.course_name, c.section_name, b.name, s.photo_file_name, s.id, 
                            s.phone2, count(s.id) as count_absent FROM attendances a
                            INNER JOIN `students` s ON s.id = a.student_id
                            INNER JOIN batches b on b.id = s.batch_id
                            INNER JOIN courses c on b.course_id = c.id
                            WHERE s.school_id = " . $school_id . " and 
                            month_date BETWEEN '" . $start_date . "' AND '" . $end_date . "'
                            GROUP BY s.id having count(s.id) <= 10 ORDER BY count( s.id ) DESC LIMIT 10");
   
    $ar_students = array();
    $i = 1;
    $ar_student_data = array();
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $server_name = $_SERVER['SERVER_NAME'];
        $server_name = "http://" . str_replace("dashboard", $school_domain, $server_name);
        $ar_tmp = array();
        $ar_tmp['name'] = $rs['first_name'] . " " . $rs['last_name'];
        $ar_tmp['admission_no'] = $rs['admission_no'];
        $ar_tmp['id'] = $rs['id'];
        $ar_tmp['class_roll_no'] = $rs['class_roll_no'];
        $ar_tmp['class_name'] = $rs['course_name'];
        $ar_tmp['section_name'] = $rs['section_name'];
        $ar_tmp['count_absent'] = $rs['count_absent'];
        $ar_tmp['phone'] = $rs['phone2'];
        
        if ( !is_null($rs['photo_file_name']) || !empty($rs['photo_file_name']) )
        {
            if (strlen($school_id) == 1 )
            {
                $str_school_id = "00" . $school_id;
            }
            else if (strlen($school_id) == 2 )
            {
                $str_school_id = "0" . $school_id;
            }
            if (strpos($rs['photo_file_name'], '.') === FALSE )
            {
                $rs['photo_file_name'] = $rs['photo_file_name'] . "jpg";
            }
            $ar_tmp['photo_file_name'] = $server_name . "/uploads/000/000/" . $str_school_id . "/students/photos/" . $rs['id'] . "/original/" . $rs['photo_file_name'];
            
        }
        else
        {
            $ar_tmp['photo_file_name'] = $server_name . "/images/master_student/profile/default_student.png?1465381441";
        }
        
        $ar_student_data[] = $ar_tmp;
        if ( $i % 2 == 0 )
        {
            $ar_students[] = $ar_student_data;
            $ar_student_data = array();
        }
        $i++;
    }
    if ( ! empty($ar_students) )
    {
        $resultData['students_absent_month'] = $ar_students;
    }
    else
    {
        $resultData['students_absent_month'] = array();
        
        $resultData['class_month'] = true;
        if ( !$class_month )
        {
            $resultData['class_month'] = false;
        }
        
    }
    
    $result = $conn->query("SELECT e.employee_number, e.first_name, e.last_name, ep.name,
                            e.photo_file_name, e.id, e.mobile_phone, count(e.id) as count_absent FROM `employee_attendances` ea
                            INNER JOIN employees e on e.id = ea.employee_id
                            INNER JOIN employee_positions ep on ep.id = e.employee_position_id
                            WHERE e.school_id = " . $school_id . " and ep.name != 'System Admin' and
                            ea.attendance_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' 
                            GROUP BY e.id having count(e.id) <= 10 ORDER BY count( e.id ) DESC LIMIT 10");
    
   
    $ar_employees = array();
    $i = 1;
    $ar_employee_data = array();
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $server_name = $_SERVER['SERVER_NAME'];
        $server_name = "http://" . str_replace("dashboard", $school_domain, $server_name);
        $ar_tmp = array();
        $ar_tmp['name'] = $rs['first_name'] . " " . $rs['last_name'];
        $ar_tmp['employee_number'] = $rs['employee_number'];
        $ar_tmp['id'] = $rs['id'];
        $ar_tmp['employee_position'] = $rs['name'];
        $ar_tmp['count_absent'] = $rs['count_absent'];
        $ar_tmp['mobile_phone'] = $rs['mobile_phone'];
        
        if ( !is_null($rs['photo_file_name']) || !empty($rs['photo_file_name']) )
        {
            if (strlen($school_id) == 1 )
            {
                $str_school_id = "00" . $school_id;
            }
            else if (strlen($school_id) == 2 )
            {
                $str_school_id = "0" . $school_id;
            }
            if (strpos($rs['photo_file_name'], '.') === FALSE )
            {
                $rs['photo_file_name'] = $rs['photo_file_name'] . "jpg";
            }
            $ar_tmp['photo_file_name'] = $server_name . "/uploads/000/000/" . $str_school_id . "/employees/photos/" . $rs['id'] . "/original/" . $rs['photo_file_name'];
            
        }
        else
        {
            $ar_tmp['photo_file_name'] = $server_name . "/images/master_student/profile/default_student.png?1465381441";
        }
        
        $ar_employee_data[] = $ar_tmp;
        if ( $i % 2 == 0 )
        {
            $ar_employees[] = $ar_employee_data;
            $ar_employee_data = array();
        }
        $i++;
    }
    if ( !empty($ar_employees) )
    {
        $resultData['employees_absent_month'] = $ar_employees;
    }
    else
    {
        $resultData['employees_absent_month'] = array();
        $resultData['class_employee_month'] = true;
        if ( ! $class_month )
        {
            $resultData['class_employee_month'] = false;
        }
    }
    
    $result = $conn->query("SELECT * FROM `employees` WHERE school_id = " . $school_id);
    if ( $result->num_rows == 0)
    {
        $num_employee = 0;
    }
    else
    {
        $num_employee = $result->num_rows;
    }
    $resultData['attendance_statistics_emp'] = array();
    $resultData['attendance_statistics_emp']['total_employee'] = $num_employee;
    
    $result = $conn->query("SELECT id, attendance_date, count(employee_id) as count FROM `employee_attendances` 
                            WHERE school_id = " . $school_id . " and attendance_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' 
                            GROUP BY `attendance_date`");
    
    
    if ( $result->num_rows == 0)
    {
        $num_absent = 0;
    }
    else
    {
        $num_absent_row = $result->num_rows;
        $num_employee = 0;
        while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
        {
            $num_employee += $rs['count'];
        }
        $num_absent = floor( $num_employee / $num_absent_row );
    }
    $resultData['attendance_statistics_emp']['avg_attendace'] = ' 0 ';
    if ($class_month)
    {
        $resultData['attendance_statistics_emp']['avg_attendace'] = $num_employee - $num_absent;
    }
    
    
    $start = new DateTime('first day of this month');
    $end = new DateTime('now');
    $days = $start->diff($end, true)->days;

    $fridays = intval($days / 7) + ($start->format('N') + $days % 5 >= 5);
    
    $num_class = $days - $fridays;
    $resultData['attendance_statistics']['num_class'] = ' 0 ';
    $resultData['attendance_statistics_emp']['num_class'] = ' 0 ';
    if ($class_month)
    {
        $resultData['attendance_statistics']['num_class'] = $num_class;
        $resultData['attendance_statistics_emp']['num_class'] = $num_class;
    }
    
    echo json_encode($resultData);	