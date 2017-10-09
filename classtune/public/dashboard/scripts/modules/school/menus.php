<?php
    include '../config.php';
    
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $school_id = $request->school_id;
    $server = $request->server;
    $dashboard_link = $request->dashboard_link;
    $school_domain = $request->school_domain;
    $username = $request->username;
    $access_token = $request->token;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], "champs21_school");
    
    $result = $conn->query("SELECT *  FROM dashboards_links WHERE is_common = 1 AND school_id = 0");

    $menus = array();
    
    $menus[0] = array(
        "id"    => 0,
        "menu_name"  => "Home",
        "fa_icon"  => "home",
        "link"  => $dashboard_link . "/?dom=" . $school_domain . "&username=" . $username . "&access_token=" . $access_token
    );
    
    $menus[1] = array(
        "id"    => 0,
        "menu_name"  => "Old Dashboard",
        "fa_icon"  => "dashboard",
        "link"  => $server . "/"
    );
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $rs['link'] = $server . "/" . $rs['link_key'];
        $menus[] = $rs;
    }
    
    $result = $conn->query("SELECT *  FROM dashboards_links WHERE is_common = 0 AND school_id = " . $school_id);

    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $menus[] = $rs;
    }
    
    echo json_encode($menus);