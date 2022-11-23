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

echo 'jeje';
exit;