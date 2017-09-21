<?php
include '../config.php';
    
//$params = (array) json_decode(file_get_contents('php://input'), TRUE);
$params = $_GET;

$ratings_type = $_GET['ratings_type'];
$district_id = $params['district_id'];
$division_id = $params['division_id'];
    
$s_extra_query = "";
if ( $ratings_type > 0 )
{
    if ( $ratings_type == 1 )
    {
        $s_extra_query = " AND er.ratings BETWEEN 4.01 AND 5.00";
    }
    else if ( $ratings_type == 2 )
    {
        $s_extra_query = " AND er.ratings BETWEEN 3.01 AND 4.00";
    }
    if ( $ratings_type == 3 )
    {
        $s_extra_query = " AND er.ratings BETWEEN 2.01 AND 3.00";
    }
    if ( $ratings_type == 4 )
    {
        $s_extra_query = " AND er.ratings BETWEEN 1.01 AND 2.00";
    }
    if ( $ratings_type == 5 )
    {
        $s_extra_query = " AND er.ratings BETWEEN 0 AND 1.00";
    }
}

$draw = $params['draw'];
$start = $params['start'];
$length = $params['length'];
$a_order = $params['order'];
$order = $a_order[0];
$order_id = "e.name";
$order_dir = "ASC";
if ( $order['column'] == "0" )
{
    $order_id = "er.ratings";
    if ( strtoupper($order['dir']) == "ASC" )
    {
        $order_dir = "DESC";
    }
    else
    {
        $order_dir = "ASC";
    }
}
else if ( $order['column'] == "1" )
{
    $order_id = "er.ratings";
    if ( strtoupper($order['dir']) == "ASC" )
    {
        $order_dir = "DESC";
    }
    else
    {
        $order_dir = "ASC";
    }
}

$conn = new mysqli($db['host'],$db['username'], $db['password'], $db['dbname']);

if ( $district_id == 0 )
{
    $result = $conn->query("SELECT e.id FROM employees e 
                            INNER JOIN `employee_ratings` er ON e.id = er.employee_id  
                            INNER JOIN schools s ON s.id = e.school_id 
                            INNER join divisions d ON d.id = s.division_id 
                            WHERE d.id = " . $division_id . $s_extra_query . " GROUP BY e.id");

}
else
{
    $result = $conn->query("SELECT e.id FROM employees e 
                            INNER JOIN `employee_ratings` er ON e.id = er.employee_id  
                            INNER JOIN schools s ON s.id = e.school_id 
                            INNER join districts d ON d.id = s.district_id 
                            WHERE d.id = " . $district_id . $s_extra_query . " GROUP BY e.id");
}

if ( $result->num_rows == 0)
{
    $record_total = 0;
}
else
{
    $record_total = $result->num_rows;
}


if ( $district_id == 0 )
{
    $result = $conn->query("SELECT e.id, e.name as employee_name, e.email, e.phone_no, er.total_homework,
                            e.profile_photo, er.present_percent, er.pass_percent, er.total_point, er.ratings,
                            e.short_desc as short_bio, s.institute_name as school_name
                            FROM employees e INNER JOIN `employee_ratings` er ON e.id = er.employee_id
                            INNER JOIN schools s ON s.id = e.school_id
                            INNER join divisions d ON d.id = s.division_id 
                            WHERE d.id = " . $division_id . $s_extra_query . "
                            GROUP BY e.id 
                            ORDER BY " . $order_id . " " . $order_dir . " LIMIT " . $start . ", " . $length);
}
else
{
    $result = $conn->query("SELECT e.id, e.name as employee_name, e.email, e.phone_no, er.total_homework,
                            e.profile_photo, er.present_percent, er.pass_percent, er.total_point, er.ratings,
                            e.short_desc as short_bio, s.institute_name as school_name
                            FROM employees e INNER JOIN `employee_ratings` er ON e.id = er.employee_id
                            INNER JOIN schools s ON s.id = e.school_id
                            INNER join districts d ON d.id = s.district_id 
                            WHERE d.id = " . $district_id . $s_extra_query . "
                            GROUP BY e.id 
                            ORDER BY " . $order_id . " " . $order_dir . " LIMIT " . $start . ", " . $length);
//    print "SELECT e.id, e.name as employee_name, e.email, e.phone_no, er.total_homework,
//                            e.profile_photo, er.present_percent, er.pass_percent, er.total_point, er.ratings,
//                            e.short_desc as short_bio, s.institute_name as school_name
//                            FROM employees e INNER JOIN `employee_ratings` er ON e.id = er.employee_id
//                            INNER JOIN schools s ON s.id = e.school_id
//                            INNER join districts d ON d.id = s.district_id 
//                            WHERE d.id = " . $district_id . $s_extra_query . "
//                            GROUP BY e.id 
//                            ORDER BY " . $order_id . " " . $order_dir . " LIMIT " . $start . ", " . $length;
}

$outp = array();

//$emp_ratings = (int) $ratings;

while( $rs = $result->fetch_array(MYSQLI_ASSOC)) 
{
    $ar_tmp = array();
    $name = '<div class="row">
                <!-- col -->
                <div class="col-lg-12" style="padding: 10px;">
                    <div class="media mb-20">
                        <div class="pull-left thumb">
                            <img class="media-object img-circle" src="' . $rs['profile_photo'] . '" alt="">
                        </div>
                        <div class="media-body" style="padding-left: 20px;">
                            <h4 class="media-heading mb-0"><strong>' . $rs['employee_name'] . '</strong></h4>
                            <small class="text-lightred"><i class="fa fa-envelope-o"></i>&nbsp;&nbsp; ' . $rs['email'] . '</small><br />
                            <small class="text-lightred"><i class="fa fa-phone"></i>&nbsp;&nbsp; ' . $rs['phone_no'] . '</small><br /><br /><br />
                        </div>
                        <div class="row">
                            <h5 style="padding-left: 30px; font-weight: bold;">Total Marks Obtained: ' . $rs['total_point'] . ' (<small class="text-lightred">Ratings: ' .  $rs['ratings'] . '</small>)</h5>
                            <hr />
                            <p style="padding-left: 30px;"><b>Mark Breakdown: </b></p>
                            <!-- col -->
                            <div class="col-xs-4 text-center b-r b-solid">
                                <small class="text-lightred"><i class="fa fa-check-square-o "></i>&nbsp;&nbsp;&nbsp; ' . $rs['present_percent']  . '% <br />(Percentage of Attendace this month)</small>
                            </div>
                            <!-- /col -->
                            <!-- col -->
                            <div class="col-xs-4 text-center b-r b-solid">
                                <small class="text-greensea"><i class="fa fa-star-o"></i>&nbsp;&nbsp;&nbsp; ' . $rs['pass_percent']  . '% <br />(Percentage of Students obtained 70+ Marks in his subject)</small>
                            </div>
                            <!-- /col -->
                            <!-- col -->
                            <div class="col-xs-4 text-center">
                                <small class="text-blue"><i class="fa fa-paperclip"></i>&nbsp;&nbsp;&nbsp; ' . $rs['total_homework']  . '<br />(Total Homework given in this month)</small>
                            </div>
                            <!-- /col -->
                        </div>
                        <div class="row" style="padding-top: 20px; padding-left: 10px;">
                            <div class="col-xs-12 text-left">
                                <b class="text-blue">' . $rs['school_name']  . '</b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
    $ar_tmp['id'] = $rs['id'];
    $ar_tmp['name'] = $name;
    $ratings = (int) $rs['ratings'];
    $ratings_remain = 5 - $ratings;
    $s_ratings = '<div class="col-lg-12"> <dl class="text-sm">';
    $s_ratings .= '<dt><h4>Short Bio: </h4></dt><dd>' . substr($rs['short_bio'], 0, 100) . '...</dd><dt>Rating: </dt><dd class="text-lightred">';
    for( $i = 1; $i <= $ratings; $i++ )
    {
        $s_ratings .= '<i class="fa fa-star" style="display: inline;"></i>';
    }
    if (  $rs['ratings'] > $ratings && $rs['ratings'] < ($ratings + 1) )
    {
        $s_ratings .= '<i class="fa fa-star-half-full" style="display: inline;"></i>';
        $ratings_remain -= 1;
    }
    for( $i = 1; $i <= $ratings_remain; $i++ )
    {
        $s_ratings .= '<i class="fa fa-star-o" style="display: inline;"></i>';
    }
    $s_ratings .= '</dd></dl></div>';
    
    $ar_tmp['ratings'] = $s_ratings;
    $outp[] = $ar_tmp;
}
$a_data = array(
    'draw'              => $draw,
    'recordsTotal'      => $record_total,
    'recordsFiltered'   => $record_total,
    'data'              => $outp
);
echo json_encode($a_data);