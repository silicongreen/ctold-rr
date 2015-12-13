<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$config['PaymentModules'] = array("2Checkout" => 1, "stripe" => 0, "SSLCommerce" => 0);

$config['PaymentParams']  = array(
        "2Checkout"     => array(
            'product_name'  => 'Classtune School Subscription', 
            'private_key'   => '8CB1BE8D-1C8C-4784-BCDE-BDC7A4B82463',
            'public_key'    => 'FF3CBFCB-D0FE-4E03-A1EE-2FC6ED240962',
            'sellerID'      => '901300257',
            'username'      => '', //Username and password require for Admin Call
            'password'      => '', //Username and password require for Admin Call
            'SSLVerify'     => false,
            'SandBox'       => true,
            'format'        => 'json'
        ), 
        "Stripe"        => array(), 
        "SSLCommerce"   => array()
);

$config['PaymentRules']  = array(
        'recurrence_unit'   => 1,
        'recurrence_type'   => 'Week' //Month, Year
);


$config['PaymentPackages']  = array(
        "1001" => array(
            'package_name'        => 'First Package - 1000 Students',
            'price'               => 1000,
            'student'             => 1000,
            'test_student'        => 5,
            'type_purchase'       => 'Year', //Month, Year
            'test_type_purchase'  => 'Week',
            'grace_student_no'    => 5, 
            'unlimited_allowed'   => false 
        ),
        "1002" => array(
            'package_name'        => 'Second Package - 1500 Students',
            'price'               => 1500,
            'student'             => 1500,
            'test_student'        => 6,
            'type_purchase'       => 'Year', //Month, Year
            'test_type_purchase'  => 'Week',
            'grace_student_no'    => 5, 
            'unlimited_allowed'   => false 
        ),
        "1003" => array(
            'package_name'        => 'Third Package - 2000 Students',
            'price'               => 2000,
            'student'             => 2000,
            'test_student'        => 7,
            'type_purchase'       => 'Year', //Month, Year
            'test_type_purchase'  => 'Week',
            'grace_student_no'    => 5, 
            'unlimited_allowed'   => false 
        ),
        "1004" => array(
            'package_name'        => 'Fourth Package - 2500 Students',
            'price'               => 2500,
            'student'             => 2500,
            'test_student'        => 8,
            'type_purchase'       => 'Year', //Month, Year
            'test_type_purchase'  => 'Week',
            'grace_student_no'    => 5, 
            'unlimited_allowed'   => true 
        )
);
