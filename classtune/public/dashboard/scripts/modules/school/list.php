<?php
    include '../config.php';
    
    $params = $_GET;
    
    $table_info = $params['table_info'];
    $school_id = $params['school_id'];
    $school_domain = $params['school_domain'];
    $server = $params['server'];
    
    $draw = $params['draw'];
    $start = $params['start'];
    $length = $params['length'];
    $a_order = $params['order'];
    $order = $a_order[0];
    $order_id = "e.name";
    $order_dir = "ASC";
    if ( $order['column'] == "0" )
    {
        $order_id = "e.name";
        $order_dir = $order['dir'];
    }
    else if ( $order['column'] == "1" )
    {
        $order_id = "e.name";
        $order_dir = $order['dir'];
    }
    
    $conn = new mysqli($db['host'],$db['username'], $db['password'], "champs21_school");
    $conn->set_charset("utf8");
    
    $current_date = date("Y-m-d 23:59:59");
    $start_date = date("Y-m-d 00:00:01", strtotime("-10 day", strtotime($current_date)));
    $end_date = $current_date;
    
    $result = $conn->query("SELECT count(*) as count FROM " . $table_info . " t 
                            INNER JOIN employees e ON t.employee_id = e.id 
                            INNER JOIN employee_positions ep on ep.id = e.employee_position_id
                            INNER JOIN subjects s ON s.id = t.subject_id 
                            INNER JOIN batches b ON b.id = s.batch_id 
                            INNER JOIN courses c ON c.id = b.course_id 
                            WHERE t.school_id = " . $school_id . " LIMIT 10");
   
    if ( $result->num_rows == 0)
    {
        $record_total = 0;
    }
    else
    {
        $record_total = 10;
    }
    
    $s_extra_fields = "";
    if ( $table_info == "assignments" )
    {
        $s_extra_fields = ", t.duedate";
    }
    
    $result = $conn->query("SELECT e.employee_number, e.first_name, e.last_name, ep.name,
                            b.name as batch_name, c.course_name, c.section_name, t.id, t.title, t.content, 
                            s.name as subject_name, s.icon_number " . $s_extra_fields . " FROM " . $table_info . " t 
                            INNER JOIN employees e ON t.employee_id = e.id 
                            INNER JOIN employee_positions ep on ep.id = e.employee_position_id
                            INNER JOIN subjects s ON s.id = t.subject_id 
                            INNER JOIN batches b ON b.id = s.batch_id 
                            INNER JOIN courses c ON c.id = b.course_id 
                            WHERE t.school_id = " . $school_id . " ORDER by t.created_at DESC LIMIT 10");
    //AND t.created_at
    //BETWEEN '" . $start_date . "' AND '" . $end_date . "'
    $outp = array();
    $i = 1;
    while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
    {
        $server_name = $_SERVER['SERVER_NAME'];
        $server_name = "http://" . str_replace("dashboard", $school_domain, $server_name);
        $ar_tmp = array();
        
        if ( !is_null($rs['icon_number']) || !empty($rs['icon_number']) )
        {
            $icon_name = $server_name . "/images/icons/subjects/"  . $rs['icon_number'];
            
        }
        else
        {
            $icon_name = $server_name . "/images/icons/classwork.png";
        }
        
        $name = '<div class="row">
                    <!-- col -->
                    <div class="col-lg-12">
                        <div class="media mb-20">
                            <div class="pull-left thumb">
                                <img class="media-object" src="' . $icon_name . '" alt="">
                            </div>
                            <div class="media-body" style="padding-left: 20px;">
                                <h4 class="media-heading mb-0"><strong>' . $rs['subject_name'] . '</strong></h4>
                                <small class="text-lightred">' . $rs['batch_name'] . ', ' . $rs['course_name'] . '</small><br />
                                <small class="text-lightred">Section: ' . $rs['section_name'] . '</small><br /><br /><br />
                            </div>
                        </div>
                    </div>
                </div>';
        $ar_tmp['id'] = $i;
        $ar_tmp['name'] = $name;
        
        $content = $rs['content'];
        if ( strlen($content) > 100 )
        {
            $content = substr($rs['content'], 0, 100) . "...";
        }
        $s_homework_info  = '<div class="col-lg-12"> <dl class="text-sm">';
        if ( $table_info == "assignments" )
        {
            $s_homework_info .= '<dt><h4><a href="' . $server . '/assignments/' . $rs['id'] . '">' . $rs['title'] . '</a></h4></dt><dt>Due Date: ' . date("Y-m-d", strtotime($rs['duedate'])) . '</dt><dd>' . $content . '</dd>';
        }
        else
        {
            $s_homework_info .= '<dt><h4><a href="' . $server . '/assignments/' . $rs['id'] . '">' . $rs['title'] . '</a></h4></dt><dd>' . $content . '</dd>';
        }
        $s_homework_info .= '</dl></div>';
        
        $ar_tmp['info'] = $s_homework_info;
        $i++;
        $outp[] = $ar_tmp;
    }

    $a_data = array(
        'draw'              => $draw,
        'recordsTotal'      => $record_total,
        'recordsFiltered'   => $record_total,
        'data'              => $outp
    );
    echo json_encode($a_data);