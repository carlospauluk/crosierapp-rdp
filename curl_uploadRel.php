<?php

if (!isset($argv[1])) {
    die('Tipo de relatório não informado.' . PHP_EOL);
}

if (!isset($argv[2])) {
    die('Nenhum arquivo informado.' . PHP_EOL);
}

// $endpoint = 'https://rdp.dev.crosier/api/relatorios/upload';


$tipoRelatorio = $argv[1];
$arquivo = $argv[2];


$ch = curl_init();

// curl_setopt($ch, CURLOPT_CAINFO, '/home/carlos/Downloads/_.dev.crosier');

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $endpoint);
curl_setopt($ch, CURLOPT_POST, 1);

if (!file_exists($arquivo)) {
    die('O arquivo não existe.' . PHP_EOL);
}
if (@!is_file($arquivo)) {
    die('Não é arquivo.' . PHP_EOL);
}

if (function_exists('curl_file_create')) { // php 5.5+
    $cFile = curl_file_create($arquivo);
} else {
    $cFile = '@' . realpath($arquivo);
}

//Create a POST array with the file in it
$postData = array(
    'tipoRelatorio' => $tipoRelatorio,
    'file' => $cFile
);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Authorization: Bearer 999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999999',
    'Content-Type: multipart/form-data'
));

echo 'Executando...' . PHP_EOL;

// Execute the request
$response = curl_exec($ch);


print_r($response);

echo PHP_EOL;

