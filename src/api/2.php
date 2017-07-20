<?php
$url = "http://api.champs21.com/api/cardatt/attsync";
$fields = array(
    'school_id' => 319,
    'card_number' => $card_number,
    'add_att' => 1
);

$fields_string = "";

foreach ($fields as $key => $value) {
    $fields_string .= $key . '=' . $value . '&';
}

rtrim($fields_string, '&');
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch, CURLOPT_URL, $url);

curl_setopt($ch, CURLOPT_POST, count($fields));
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/json',
    'Content-Length: ' . strlen($fields_string)
        )
);

$result = curl_exec($ch);

curl_close($ch);
