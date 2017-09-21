<?php
    include '../config.php';
    
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $division_id = $request->division_id;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    if ( $division_id > 0 )
    {
        $result = $conn->query("SELECT * FROM districts WHERE division_id = " . $division_id);
    }
    else
    {
        $result = $conn->query("SELECT d.id, d.name, di.name as division_name FROM districts d INNER JOIN divisions di ON d.division_id = di.id");
    }
    
    $outp = array();
    if ( $division_id > 0 )
    {
        $outp[] = array("id" => 0, "name" => "All Districts");
    }
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $outp[] = $rs;
    }
    echo json_encode($outp);	