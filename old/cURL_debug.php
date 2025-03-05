<?php
// URL to fetch the JSON data from
$url = "https://scripts.tlap.biz/open_positions/index3.php?action=getdata";
$headers = [
    'Accept' => 'application/json',
];

/*
 * We're going to use the output buffer to store the debug info.
 */
ob_start();
$out = fopen('php://output', 'w');

$handler = curl_init($url);

/*
 * Here we set the library verbosity and redirect the error output to the 
 * output buffer.
 */
curl_setopt($handler, CURLOPT_VERBOSE, true);
curl_setopt($handler, CURLOPT_STDERR, $out);

$requestHeaders = [];
foreach ($headers as $k => $v) {
    $requestHeaders[] = $k . ': ' . $v;
}
curl_setopt($handler, CURLOPT_HTTPHEADER, $requestHeaders);
curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($handler);
fclose($out);

/*
 * Joining debug info and response body.
 */
$data = ob_get_clean();
$data .= PHP_EOL . $response . PHP_EOL;
echo $data;

?>