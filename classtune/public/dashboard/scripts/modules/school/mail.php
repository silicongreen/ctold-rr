<?php
    include '../config.php';
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    $result = $conn->query("SELECT email FROM employees WHERE email != '' group by email LIMIT 50");
    
    $outp = array();
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $outp[] = $rs['email'];
    }
    echo json_encode($outp);	