<?php
    $db['host'] = 'localhost';
//    $db['username'] = 'champs21_champ';
//    $db['password'] = '1_84T~vADp2$';
//    $db['dbname'] = 'champs21_school';
    $db['username'] = 'root';
    $db['password'] = '079366';
    $db['dbname'] = 'champs21_school';
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    $conn->set_charset("utf8");
    
    $result = $conn->query("SELECT s.admission_no, s.first_name, s.middle_name, s.last_name, c.section_name FROM `students` s 
                            INNER join batches b on s.batch_id = b.id 
                            INNER join courses c ON b.course_id = c.id WHERE c.`is_deleted` = 0 AND s.`school_id` = 340 and c.course_name IN ('KG I','KG II','Playgroup', 'Nursery')");
    
    $outp = array();
    $i = 0;
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $admission_no = $rs['admission_no'];
        if ( ! empty($rs['middle_name']) )
        {
            if ( ! empty($rs['last_name']) )
            {
                $name = $rs['first_name'] . " " . $rs['middle_name'] . " " . $rs['last_name'];
            }
            else
            {
                $name = $rs['first_name'];
            }
        }
        else
        {
            if ( ! empty($rs['last_name']) )
            {
                $name = $rs['first_name'] . " " . $rs['last_name'];
            }
            else
            {
                $name = $rs['first_name'];
            }
        }
        $section_name = $rs['section_name'];
        $fields = array(
            "studentInfo" => array(
                'user'      => 'admin',
                'token'     => '12345',
                'sid'       => $admission_no,
                'sname'     => $name,
                'section'   => $section_name
            )
        );
        echo json_encode($fields) . "\n\n";
//        $fields_string = '';
//        foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
//        $fields_string = substr($fields_string, 0, -1);
//        echo $fields_string . "\n\n";
        $url = "http://202.84.35.179:1009/api/student";
        
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS, json_encode($fields));
        
        $header = array(
            'Content-type: application/json'
        );
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $response = json_decode(curl_exec($ch));
        echo serialize($response) . "  " . curl_error($ch) . "\n\n";
        //close connection
        curl_close($ch);
        $i++;
        //if ( $i > 5 )
        //{
        //    exit;
        //}
        
    }
    echo json_encode($outp);	
