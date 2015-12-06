<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$config['PaymentModules'] = array("2Checkout" => 1, "stripe" => 0, "SSLCommerce" => 0);

$config['PaymentParams']  = array(
        "2Checkout"     => array(
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
        'unit_price'        => '0.99',
        'recurrence_unit'   => 1,
        'recurrence_type'   => 'Week' //Month, Year
);
