<?php
    include '../config.php';
    
    $params = $_GET;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    $conn->set_charset("utf8");
    
    $result = $conn->query("SELECT * FROM (SELECT u.name, u.id, a.date, a.present, a.absent   FROM `universities` u INNER JOIN employee_attendance a ON u.id = a.university_id order by a.date DESC) AS t group by id order by name");
    
    $outp = array();
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $tmp = array();
        $tmp['title'] = $rs['name'];
        $tmp['status'] = (int) ($rs['present']/($rs['present']+$rs['absent']) * 100);
        $tmp['chart']['color'] = "#" . dechex(rand(0x000000, 0xFFFFFF));
        $subresult = $conn->query("SELECT present FROM employee_attendance WHERE university_id = ". $rs['id'] . " ORDER BY date DESC LIMIT 10");
        //$i=9;
        $tmp['chart']['data'] = array();
        while( $sr = $subresult->fetch_array(MYSQLI_ASSOC))
        {
            $tmp['chart']['data'][] = $sr['present'];
            //$i--;
        }
        array_reverse($tmp['chart']['data']);
        $outp[] = $tmp;
    }
    //echo "Edooze"; 
    echo json_encode($outp);
