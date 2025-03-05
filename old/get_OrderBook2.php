<?php
// URL to fetch the JSON data from
$jsonUrl = "https://tlap.onrender.com/get_orderbook/symbol/GBPUSD/time/1740712800";

// Initialize a cURL session
$ch = curl_init();

// Set the URL and other required options
curl_setopt($ch, CURLOPT_URL, $jsonUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Set a common User-Agent string to mimic a browser
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36");

// Disable SSL verification (optional but necessary if SSL errors occur)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL verification (use with caution)

// Set a timeout for the connection (in seconds)
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

// Execute the cURL request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
    exit;
}

// Get HTTP response code
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($httpCode != 200) {
    echo "Error: Failed to fetch data. HTTP Status Code: " . $httpCode;
    exit;
}

// Close the cURL session
curl_close($ch);

// Decode the JSON data
$data = json_decode($response, true);

// Check if the data is decoded successfully
if ($data === null) {
    echo "Error decoding JSON data.";
    exit;
}

// Optionally, print the JSON data
echo "<pre>";
print_r($data);
echo "</pre>";

// Proceed with your MySQL insertion logic here
?>
