<?php

require_once('vendor/autoload.php');

$stripe = array(
    "secret_key" => "sk_test_002dJofp6PF2BC6B0lMkDb0j",
    "publishable_key" => "pk_test_9TfEoBZhdGU1iBx521GMjild"
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);
?>