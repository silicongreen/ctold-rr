<?php
$servername = "localhost";
$username = "root";
$password = "079366";
$dbname_source = "dod";
$dbname_destination = "cmate";

// Create connection
$conn_source = new mysqli($servername, $username, $password, $dbname_source);
// Check connection
if ($conn_source->connect_error) {
    die("Connection failed: " . $conn_source->connect_error);
}

$conn_destination = new mysqli($servername, $username, $password, $dbname_destination);
// Check connection
if ($conn_destination->connect_error) {
    die("Connection failed: " . $conn_destination->connect_error);
}

$university_id = 24;
$insert_date = date("Y-m-d H:i:s");


////ADD CATEGORY

//$stsql_catgeory = "insert into employee_categories (name,prefix,status,updated_at,created_at,university_id)"
//        . " values ('General','Ge',1,'".$insert_date."','".$insert_date."',".$university_id.")";
//$conn_destination->query($stsql_catgeory);
//
//$catgeory_id = $conn_destination->insert_id;


/// NEED TO ADD IN EMPLOYEE POSITION TABLE

//ALTER TABLE  `employee_positions` ADD  `short` VARCHAR( 255 ) NULL ,
//ADD  `rank` VARCHAR( 255 ) NULL ,
//ADD  `prev_id` VARCHAR( 255 ) NULL ;


//// ADD POSITION
//$sql = "SELECT * FROM desig_info";
//$result = $conn_source->query($sql);
//$prog_ids = array();
//if ($result->num_rows > 0) {
//    while ($row=$result->fetch_assoc())
//    {
//        $sql = "insert into employee_positions (name,employee_category_id,status,updated_at,created_at,university_id,short,rank)"
//        . " values ('".$row['d_fullname']."',".$catgeory_id.",1,'".$insert_date."','".$insert_date."',".$university_id.",'".$row['d_short']."','".$row['rank']."')";
//        
//        $conn_destination->query($sql);
//        $p_id = $conn_destination->insert_id;
//        
//        $sql = "update desig_info set insert_id=".$p_id." where desig_id='".$row['desig_id']."'";
//        
//        $conn_source->query($sql);
//     
//        print_r($conn_destination->error);
//    }
//} 

/// ADD GRADES OR OFFICIAL STATUS

//$sql = "SELECT emp_sub FROM  lib_teacher GROUP BY emp_sub";
//$result = $conn_source->query($sql);
//$prog_ids = array();
//if ($result->num_rows > 0) {
//    while ($row=$result->fetch_assoc())
//    {
//        $sql = "insert into employee_grades (name,priority,status,updated_at,created_at,university_id)"
//        . " values ('".$row['emp_sub']."',1,1,'".$insert_date."','".$insert_date."',".$university_id.")";
//        
//        $conn_destination->query($sql);
//        print_r($conn_destination->error);
//    }
//} 

//$types = array('Type 1','Type 2','Type 3','Type 4');
//
//$i = 0;
//foreach($types as $value)
//{
//    $i++;
//    $sql = "insert into employee_types (name,priority,status,updated_at,created_at,university_id)"
//        . " values ('".$value."',".$i.",1,'".$insert_date."','".$insert_date."',".$university_id.")";
//        
//    $conn_destination->query($sql);
//}  


$sql = "SELECT * FROM  emp_info";
$result = $conn_source->query($sql);
$prog_ids = array();
if ($result->num_rows > 0) {
    while ($row=$result->fetch_assoc())
    {
        $name = explode(" ",$row['emp_name']);
        $first_name = "";
        $last_name = "";
        $total = count($name)-1;
        foreach($name as $key=>$value)
        {
            if($key == 0 or $key<$total)
            {
                $first_name.= $value;
            }
            else
            {
                $last_name = $value;
            }
        }  
        $gender = strtolower($row['sex']);
        
        
        
        $employee_position_id = "";
        
        
        $sql = "insert into employees (employee_category_id,employee_number,joining_date,first_name,last_name,gender,job_title)"
        . " values (".$catgeory_id.",'".$row['emp_id']."','".date("Y-m-d", strtotime($row['join_date']))."'"
                . ",'".$first_name."','".$last_name."','".$gender."','".$row['JobDesc']."','".$row['JobDesc']."')";
        
        $conn_destination->query($sql);
        print_r($conn_destination->error);
    }
} 

