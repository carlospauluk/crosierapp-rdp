<?php
/**
 * Exemplo de chamada:
 * php curl_relatoriospush.php /caminho/do/arquivo.ext 1 DEV log
 */

echo PHP_EOL . PHP_EOL . PHP_EOL;

if (!isset($argv[1])) {
    die('Nenhum arquivo informado.' . PHP_EOL);
}
$arquivo = $argv[1];

if (!isset($argv[2])) {
    die('Id do usuário destinatário não informado.' . PHP_EOL);
}
$userDestinatarioId = $argv[2];

if (!isset($argv[3]) || !in_array($argv[3], ['DEV', 'HOM', 'PROD'])) {
    die('Ambiente não informado (DEV,HOM,PROD).' . PHP_EOL);
}
$ambiente = $argv[3];

if (!file_exists('curl.env')) {
    die('curl.env não definido' . PHP_EOL);
}

$props = parse_ini_file('curl.env');
$token = $props['apiToken_' . $ambiente];

$endpoint = $props['relatoriosPush_' . $ambiente . '_endpoint'];

$ch = curl_init();

if (isset($argv[4]) && $argv[4] === 'log') {
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
}
if (isset($props['relatoriosPush_' . $ambiente . '_CURLOPT_CAINFO'])) {
    curl_setopt($ch, CURLOPT_CAINFO, $props['relatoriosPush_' . $ambiente . '_CURLOPT_CAINFO']);
}
curl_setopt($ch, CURLOPT_URL, $endpoint);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Authorization: Bearer ' . $token
));

$encoded = base64_encode(gzencode(file_get_contents($arquivo)));

$postData = array(
    'file' => $encoded,
    'filename' => $arquivo,
    'userDestinatarioId' => $userDestinatarioId
);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);


echo 'Executando...' . PHP_EOL;

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


