<?php
    include '../config.php';
    
    $params = $_GET;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    $result = $conn->query("SELECT u.name, u.id, l.branch_id, l.lattitude, l.longitude   FROM `universities` u INNER JOIN `location` l ON u.id = l.university_id order by u.name");
    
    $outp = array();
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $outp[] = $rs;
    }
    //echo "Edooze"; 
    echo json_encode($outp);
