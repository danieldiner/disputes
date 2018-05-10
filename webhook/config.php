<?php

$paypalMode = "sandbox";

$host = "";

$api_clientId = "";
$api_secret = "";

$return_url = "";
$cancel_url = "";

if ($paypalMode == "sandbox") {

    $host = 'https://api.sandbox.paypal.com';

    $api_clientId = "AS3GJc29BfOvxgWfFn1hp8_6zJaGmJrmzw4n6s5v3EaoxsWQfxObw82h57lgbUVH4pnNQmtyFQ0qfqkW"; //PP
    $api_secret = "EBkJjNpRWVx46jH1zABiM3Fnp4GZFkiZtCN-f3kTvvig53kSB-huMxxaSmksZX5bojN6oyvfb-VOuUFU"; //PP

    $return_url = "http://www.example.com/index.html";
    $cancel_url = "http://www.example.com/index.html";

}

if ($paypalMode == "production") {

    $host = 'https://api.paypal.com';

    $api_clientId = ""; 
    $api_secret = ""; 

    $return_url = "";
    $cancel_url = "";

}








