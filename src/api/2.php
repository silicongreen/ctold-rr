<?php
ini_set('max_execution_time',0);
$servername = "128.199.171.177";
$username = "classtune";
$password = "u[QXL=OF%D,F";
$dbname_source = "classtune";

// Create connection
global $conn_source;
$conn_source = new mysqli($servername, $username, $password, $dbname_source);
// Check connection
if ($conn_source->connect_error)
{
    die("Connection failed: " . $conn_source->connect_error);
}

// Perform query
if ($result = mysqli_query($conn_source, "SELECT * FROM `exam_connects` WHERE `name` LIKE '2nd Term Exam' AND `school_id` = 352 and published_date LIKE '2022%' ORDER BY `id` DESC ")) {
    echo "Returned rows are: " . mysqli_num_rows($result);

    while ($row = mysqli_fetch_row($result)) {
        $connect_id = $row[0];
        $batch_id = $row[2];
        if ($res = mysqli_query($conn_source, "SELECT * FROM `students` WHERE `batch_id` = " . $batch_id . " AND `is_deleted` = 0 AND `school_id` = 352 ORDER BY `new_id` ASC  ")) {
            echo "Returned rows are: " . mysqli_num_rows($res);
        
            while ($row = mysqli_fetch_row($result)) {
                $student_id = $row[0];
                echo $student_id . "<br>";
             }
             exit;
            // Free result set
            mysqli_free_result($res);
        }   
     }

    // Free result set
    mysqli_free_result($result);
  }
  
  mysqli_close($con);
echo 'jeje';
exit;