<?php
/**
 *
 * This is the template that use for login ajax
 *
 * @package WordPress
 * @subpackage classtune
 * @since classtune 1.0
 */
/*
Template Name: login_ajax
*/
$username = $_POST['username'];
$password = $_POST['password'];
if($username && $password)
{
    $url = check_login_paid($username, $password);
    if($url)
    {
        echo str_replace("http://bncd","https://bncd",$url);
    }
    else
    {
        echo "0";
    }    
}
else
{
    echo "0";
}    



