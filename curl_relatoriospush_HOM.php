<?php

//Initialise the cURL var
$ch = curl_init();

//Get the response from cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

//Set the Url
curl_setopt($ch, CURLOPT_URL, 'https://rdp.crosier-hom.rodoponta.com.br/api/utils/relatoriosPush/upload');

curl_setopt($ch, CURLOPT_POST, 1);

if (function_exists('curl_file_create')) { // php 5.5+
    $cFile = curl_file_create($argv[1]);
} else { //
    $cFile = '@' . realpath($argv[1]);
}

//Create a POST array with the file in it
$postData = array(
    'file' => $cFile,
    'userDestinatarioId' => $argv[2]
);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Authorization: Bearer 1',
    'Content-Type: multipart/form-data'
));

// Execute the request
$response = curl_exec($ch);

print_r($response);

echo PHP_EOL;

