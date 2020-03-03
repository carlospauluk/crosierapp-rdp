<?php
/**
 * Exemplo de chamada:
 *
 * php curl_setarImagemParaUsuario.php USUARIO_ID RECNUM AMBIENTE log
 *
 */

if (!isset($argv[1])) {
    die('Usuário não informado' . PHP_EOL);
}
$usuario = $argv[1];


if (!isset($argv[2])) {
    die('recnum não informado' . PHP_EOL);
}
$recnum = $argv[2];

if (!isset($argv[3]) || !in_array($argv[3], array('DEV', 'HOM', 'PROD'))) {
    die('Ambiente não informado (DEV,HOM,PROD).' . PHP_EOL);
}
$ambiente = $argv[3];

$props = parse_ini_file('curl.env');

$token = $props['apiToken_' . $ambiente];

$endpoint = $props['setarImagemParaUsuario_' . $ambiente . '_endpoint'];


$ch = curl_init();

if (isset($argv[4]) && $argv[4] === 'log') {
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
}
if (isset($props['setarImagemParaUsuario_' . $ambiente . '_CURLOPT_CAINFO'])) {
    curl_setopt($ch, CURLOPT_CAINFO, $props['setarImagemParaUsuario_' . $ambiente . '_CURLOPT_CAINFO']);
}
curl_setopt($ch, CURLOPT_URL, $endpoint);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Authorization: Bearer ' . $token
));


//Create a POST array with the file in it
$postData = array(
    'usuario' => $usuario,
    'recnum' => $recnum
);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

$response = curl_exec($ch);

if (isset($argv[4]) && $argv[4] === 'log') {
    $curlInfo = curl_getinfo($ch);
    print_r($curlInfo);
}

if ($response) {
    print_r($response);
} else {
    echo 'Não enviado.';
}

echo PHP_EOL;


