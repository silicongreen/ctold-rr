<?php
    include '../config.php';
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    $conn->set_charset("utf8");
    
    $result = $conn->query("SELECT * FROM classes");
    
    $outp = array();
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $outp[] = $rs;
    }
    echo json_encode($outp);	