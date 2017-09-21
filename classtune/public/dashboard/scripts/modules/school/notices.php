<?php
    include '../config.php';
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $school_id = $request->school_id;
    $school_domain = $request->school_domain;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], "champs21_school");
    $result = $conn->query("SELECT n.title, n.content, n.created_at, CONCAT(u.first_name, ' ', u.last_name) as name
                            FROM news n INNER JOIN users u ON u.id = n.author_id
                            WHERE n.school_id = " . $school_id . " ORDER BY n.id DESC LIMIT 3");
    
    $outp = array();
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
       $rs['created_at'] = date("d/m/Y", strtotime($rs['created_at']));
       if (strlen($rs['content']) > 100 )
       {
            $rs['content'] = substr(strip_tags($rs['content']), 0, 100) . "...";
       }
       else
       {
           $rs['content'] = strip_tags($rs['content']);
       }
       $outp[] = $rs;
    }
    //echo "Edooze"; 
    echo json_encode($outp);

