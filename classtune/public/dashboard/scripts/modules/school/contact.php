<?php
    include '../config.php';
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    $result = $conn->query("SELECT u.name as universityName, uc.name, uc.email, uc.phone_no, uc.profile_photo FROM universities u INNER JOIN university_contact uc ON u.id = uc.university_id");
    
    $outp = array();
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $outp[$rs['universityName']][] = $rs;
    }
    echo json_encode($outp);	