<?php
    include '../config.php';
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $university_id = $request->university_id;
    $university_name = $request->university_name;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    $result = $conn->query("SELECT ep.id as position_id, ep.name as employee_position_name,
                            count( e.id ) as emp_count FROM `employee_positions` ep
                            INNER JOIN employees e ON e.position_id = ep.id
                            WHERE e.university_id = " . $university_id . "
                            GROUP BY ep.id order by ep.order");
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
        $aDonutData['label'] = $rs['employee_position_name'];
        $aDonutData['value'] = (int) $rs['emp_count'];
        $aDonutData['color'] = randomColor(20,150);//"#" . dechex(mt_rand(0x000000, 0xFFFFFF));
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
    
    function randomColor ($minVal = 0, $maxVal = 255)
    {

        // Make sure the parameters will result in valid colours
        $minVal = $minVal < 0 || $minVal > 255 ? 0 : $minVal;
        $maxVal = $maxVal < 0 || $maxVal > 255 ? 255 : $maxVal;

        // Generate 3 values
        $r = mt_rand($minVal, $maxVal);
        $g = mt_rand($minVal, $maxVal);
        $b = mt_rand($minVal, $maxVal);

        // Return a hex colour ID string
        return sprintf('#%02X%02X%02X', $r, $g, $b);

    }