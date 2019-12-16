<?php

$operacao = $argv[1] ?? die('Operação não informado' . PHP_EOL);
$uuidPV = $argv[2] ?? null;

$endpointBase = 'https://rdp.rodoponta.dev.crosier/api/ven/pv/';
$endpoint = $endpointBase . $operacao;

$ch = curl_init();

curl_setopt($ch, CURLOPT_CAINFO, '/home/carlos/_.dev.crosier');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $endpoint);
curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Authorization: Bearer 1',
    'Content-Type: multipart/form-data'
));

// Execute the request

$response = curl_exec($ch);
if ($response) {
    print_r($response);
} else {
    echo 'Não enviado.';
}

echo PHP_EOL;


