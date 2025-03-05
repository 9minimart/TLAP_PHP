<?php
// Set the directory to store the JSON file
$directory = 'OrderBook/';
if (!file_exists($directory)) {
    mkdir($directory, 0777, true); // Create the directory if it doesn't exist
}

// Get the JSON data from the POST request
$jsonData = file_get_contents('php://input');
$data = json_decode($jsonData, true); // Decode the JSON into an associative array

// Extract the "symbol" and date-time from "title"
if (isset($data['symbol']) && isset($data['title'])) {
    $symbol = $data['symbol']; // Get the symbol (e.g., "EURUSD")

    // Use regular expression to extract the date-time from the title (e.g., "04.03.2025 04:00")
    preg_match('/(\d{2})\.(\d{2})\.(\d{4}) (\d{2}):(\d{2})/', $data['title'], $matches);
    
    // Reformat the date-time from "dd.mm.yyyy hh:mm" to "yyyy.mm.dd-hhmm"
    if (!empty($matches)) {
        $day = $matches[1];
        $month = $matches[2];
        $year = $matches[3];
        $hour = $matches[4];
        $minute = $matches[5];
        
        // Convert to "yyyy.mm.dd-hhmm" format
        $dateTime = "$year.$month.$day-$hour$minute";
    } else {
        $dateTime = 'unknown-time'; // Use "unknown-time" if no match
    }
} else {
    // Fallback values if the fields are not present
    $symbol = 'unknown-symbol';
    $dateTime = 'unknown-time';
}

// Sanitize the symbol and dateTime to create a valid file name (e.g., remove special characters)
$symbol = preg_replace('/[^a-zA-Z0-9_-]/', '', $symbol);
$dateTime = preg_replace('/[^a-zA-Z0-9_-]/', '', $dateTime);

// Define the base file name dynamically from the symbol and date-time
$baseFileName = 'orderbook-' . $symbol . '-' . $dateTime . '.json';

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
