<?php

require_once('./stripeConf.php');

$token = $_POST['stripeToken'];

echo '<pre>';
var_dump($_POST);
exit;

$customer = \Stripe\Customer::create(array(
            'email' => 'customer@example.com',
            'card' => $token
        ));

$charge = \Stripe\Charge::create(array(
            'customer' => $customer->id,
            'amount' => 5000,
            'currency' => 'usd'
        ));

echo '<h1>Successfully charged $50.00!</h1>';
?>

