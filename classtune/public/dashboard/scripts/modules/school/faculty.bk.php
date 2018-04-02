<?php
    include '../config.php';
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $university_id = $request->university_id;
    $university_name = $request->university_name;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    $conn->set_charset("utf8");
    
    $result = $conn->query("SELECT f.id, f.name AS faculty_name, d.id AS dept_id, d.name AS dept_name, ep.name as employee_position,
                            count( e.id ) as emp_count FROM `faculty` f
                            INNER JOIN departments d ON f.id = d.faculty_id
                            INNER JOIN employees e ON e.dept_id = d.id
                            INNER JOIN employee_positions ep ON e.position_id = ep.id
                            WHERE e.university_id = " . $university_id . "
                            GROUP BY ep.id order by f.id");
    $facultyData = array();
    
    $color = array(
        '#710955','#C08D62','#651B9A', '#42887E', '#B34346', '#E4FFF0',
        '#FEDE0D','#ED92A3', '#C64281', '#8D9CA4', '#16A085', '#55392C',
        '#55392C', '#778180'
        );
    $k = 0;
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $aDonutData = array();
        $aDonutData['label'] = $rs['dept_name'];
        $aDonutData['value'] = (int) $rs['emp_count'];
        $aDonutData['color'] = "#" . dechex(rand(0x000000, 0xFFFFFF));
//        $aDonutData['color'] = $color[$k];
//        $k++;
//        if ( $k > count($color) )
//        {
//            $k = 0;
//        }
        $rs['DonutData'] = $aDonutData;
        $facultyData[$university_name][] = $rs;
    }
    $donutData = array();
    $i = 0;
    foreach ($facultyData as $k => $v)
    {
        $tmp = array();
        foreach ($v as $dt)
        {
            $tmp[] = $dt['DonutData'];
        }
        $donutData[$i] = $tmp;
        $i++;
    }
    $outp['faculties'] = $facultyData;
    $outp['donuts'] = $donutData;
    echo json_encode($outp);	