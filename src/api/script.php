<?php

$servername = "localhost";
$username = "edoozz_admin";
$password = "3o?n~^Jc?1OF";
$dbname_source = "edoozz_sub3";
$dbname_destination = "edoozz_state";

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

$sql = "insert into testexe (name) values ('testing')";
$result = $conn_source->query($sql);
exit;
