<?php
// Set the directory to store the JSON file
$directory = 'OpenPositions/';
if (!file_exists($directory)) {
    mkdir($directory, 0777, true); // Create the directory if it doesn't exist
}

// Get the JSON data from the POST request
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true); // Decode the JSON into an associative array

// Extract the "symbol" and date-time from "title"
if (isset($data['timestamp'])) {
    $timestamp = $data['timestamp'];

} else {
    $timestamp = '000000';
}

// Define the base file name dynamically from the symbol and date-time
$baseFileName = 'orderbook-' . $timestamp . '.json';

// Function to check for duplicate and rename
function getUniqueFileName($directory, $baseFileName) {
    $filePath = $directory . $baseFileName;
    $fileInfo = pathinfo($filePath);
    $count = 0;

    // If file exists, append a number to the file name
    while (file_exists($filePath)) {
        $count++;
        // Format the new filename with (number) before the extension
        $filePath = $fileInfo['dirname'] . '/' . $fileInfo['filename'] . '(' . $count . ').' . $fileInfo['extension'];
    }

    return $filePath; // Return the unique file path
}

// Get a unique file name if a duplicate exists
$filePath = getUniqueFileName($directory, $baseFileName);

// Try saving the JSON data to a file
if (file_put_contents($filePath, $jsonData)) {
    // Send a success response back to Node.js
    echo json_encode(["status" => "success", "message" => "JSON data successfully saved.", "file" => basename($filePath)]);
} else {
    // Send an error response back to Node.js
    echo json_encode(["status" => "error", "message" => "Error saving JSON data."]);
}
?>
