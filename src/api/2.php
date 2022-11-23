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
        print("connect_id: ".$row[0]."\n");
        print("Age: ".$row[1]."\n");
     }
    // Free result set
    mysqli_free_result($result);
  }
  
  mysqli_close($con);
echo 'jeje';
exit;