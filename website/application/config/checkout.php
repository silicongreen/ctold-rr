<?php
$config['charges'] = array(
    'default_currency' => 'usd',
    'override_currency' => false,
    'stripe' => array(
        'free' => array(
            'price' => 0.00,
            'charge' => 0.00,
            'currency' => 'bdt',
        ),
        'gold' => array(
            'price' => 10,
            'charge' => 1,
            'currency' => 'bdt',
        ),
    ),
    'paypal' => array(
        'free' => array(
            'price' => 0.00,
            'charge' => 0.00,
            'currency' => 'bdt',
        ),
        'gold' => array(
            'price' => 10,
            'charge' => 1,
            'currency' => 'bdt',
        ),
    ),
);
