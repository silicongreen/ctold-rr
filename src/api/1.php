<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$host = "localhost";
$user = "champs21_champs";
$password = "1_84T~vADp2$";
$dbname = "champs21_school";

$link = mysqli_connect($host, $user, $password, $dbname);

$strsql = "select sm.body, sl.mobile from sms_messages sm INNER join sms_logs sl where sm.id = sl.sms_message_id where sl.id > 4382";

$res = mysqli_query($link, $strsql);

while ($row = mysql_fetch_row($query)) {
    $msg = urldecode($row[0]);
    $no = $row[1];
    echo $msg . "  " . $no . "\n\n";
//     $user = "classtune";  $pass = "ssl@123";  $sid = "classtune"; 
//     $url="http://sms.sslwireless.com/pushapi/dynamic/server.php"; 
//     $param="user=$user&pass=$pass&sms[0][0]= $no &sms[0][1]=".urlencode($msg)."&sid=$sid"; 
//     $crl = curl_init(); 
//     curl_setopt($crl,CURLOPT_SSL_VERIFYPEER,FALSE); 
//     curl_setopt($crl,CURLOPT_SSL_VERIFYHOST,2); 
//     curl_setopt($crl,CURLOPT_URL,$url);  
//     curl_setopt($crl,CURLOPT_HEADER,0); 
//     curl_setopt($crl,CURLOPT_RETURNTRANSFER,1); 
//     curl_setopt($crl,CURLOPT_POST,1); 
//     curl_setopt($crl,CURLOPT_POSTFIELDS,$param);     
//     $response = curl_exec($crl); 
//     curl_close($crl); 
//     echo $response;  
}