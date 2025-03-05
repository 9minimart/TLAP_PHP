<?php
$symbolIDs = [
    'EURUSD', 'GBPUSD', 'EURGBP', 'AUDUSD', 'NZDUSD', 'USDCAD', 'USDJPY',
    'USDCHF', 'AUDJPY', 'EURAUD', 'GBPJPY', 'XAUUSD', 'EURJPY'
];

// Define start and end times
$startDate = DateTime::createFromFormat('Ymd-Hi', '20250202-0000');
$endDate = DateTime::createFromFormat('Ymd-Hi', '20250305-0000');

// Define the step interval (20 minutes)
$interval = new DateInterval('PT20M');

// Array to hold missing files
$missingFiles = [];

// Loop through all time slots until the end date
while ($startDate <= $endDate) {
    $timestamp = $startDate->format('Ymd-Hi'); // Get current time in 'Ymd-Hi' format

    // Check for each symbolID
    foreach ($symbolIDs as $symbolID) {
        $filename = "OrderBook/orderbook-{$symbolID}-{$timestamp}.json";
        
        // Check if the file exists
        if (!file_exists($filename)) {
            // Convert 'Ymd-Hi' to Unix timestamp and add 4 hours (14400 seconds)
            $dateTime = DateTime::createFromFormat('Ymd-Hi', $timestamp);
            $unixTimestamp = $dateTime->getTimestamp() + 4 * 3600; // Add 4 hours

            // Add missing file details to the array
            $missingFiles[] = [
                'symbolID' => $symbolID,
                'time' => $unixTimestamp,
                'filename' => $filename
            ];
        }
    }

    // Add 20 minutes to the current time slot
    $startDate->add($interval);
}

// Return missing files in JSON format
header('Content-Type: application/json');
echo json_encode($missingFiles, JSON_PRETTY_PRINT);
?>
