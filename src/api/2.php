<?php
ini_set('max_execution_time',0);
$servername = "192.168.0.117";
$username = "champs21";
$password = "079366";
$dbname_source = "company";

// Create connection
global $conn_source;
$conn_source = new mysqli($servername, $username, $password, $dbname_source);
// Check connection
if ($conn_source->connect_error)
{
    die("Connection failed: " . $conn_source->connect_error);
}

include('simplehtmldom_1_5/simple_html_dom.php');
for($i = 1;$i<31;$i++)
{
$html = file_get_html('http://www.crn.com/slide-shows/managed-services/300079672/2016-msp-500-elite-150.htm/pgno/0/'.$i);

echo $company =  $html->find('div.slideCopy p',0)->plaintext; 
echo $website =  $html->find('div.slideCopy p',3)->plaintext;
//echo $link->href;
echo $service =  str_replace("Services: ","",$html->find('div.slideCopy p',5)->plaintext);
$sql = "insert into company_data (company,website,service) values ('".$company."','".$website."','".$service."')";
$conn_source->query($sql);
}