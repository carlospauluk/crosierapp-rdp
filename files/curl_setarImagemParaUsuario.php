<?php

$ch = curl_init();
//Get the response from cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, 'https://rdp.rodoponta.dev.crosier/api/utils/setarImagemParaUsuario');
curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_CAINFO, '/home/carlos/_.dev.crosier');

//Create a POST array with the file in it
$postData = array(
    'usuario' => $argv[1],
    'recnum' => $argv[2]
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

