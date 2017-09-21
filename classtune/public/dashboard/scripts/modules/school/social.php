<?php
    include '../config.php';
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    $result = $conn->query("SELECT * FROM social_feed");
    $outp = array();
    
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $rs['time'] = rand(10, 55);
        $outp[] = $rs;
    }
    echo json_encode($outp);	
    
