<?php
    include '../config.php';
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    
    $district_id = $request->district_id;
    $class_id = $request->class_id;
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);
    $conn->set_charset("utf8");
    
    $result = $conn->query("SELECT e.id, c.class_name, su.subject_name, es.subject_id, 
                            count(employee_id) as emp_count FROM `employee_subjects` es 
                            INNER join subjects su ON su.id = es.subject_id 
                            INNER join classes c ON c.id = su.class_id 
                            INNER join employees e ON e.id = es.employee_id 
                            INNER join schools s ON s.id = e.school_id 
                            INNER join divisions d ON d.id = s.division_id 
                            INNER join districts di ON di.division_id = d.id 
                            WHERE di.id = " . $district_id . " and c.id = " . $class_id . " 
                            group by es.subject_id");
    $subjectData = array();
    
    $k = 0;
    
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $aDonutData = array();
        $aDonutData['label'] = $rs['subject_name'];
        //$aDonutData['value'] = (int) $rs['emp_count'];
        $aDonutData['value'] = (int) rand(8, 79);
        $aDonutData['color'] = randomColor(20,150);//"#" . dechex(mt_rand(0x000000, 0xFFFFFF));
        $rs['DonutData'] = $aDonutData;
        $subjectData[$rs['class_name']][] = $rs;
    }
    $donutData = array();
    $i = 0;
    foreach ($subjectData as $k => $v)
    {
        $tmp = array();
        foreach ($v as $dt)
        {
            $tmp[] = $dt['DonutData'];
        }
        $donutData[$i] = $tmp;
        $i++;
    }
    $outp['subjects'] = $subjectData;
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