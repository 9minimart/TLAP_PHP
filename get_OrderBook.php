<?php

echo 'YES';
// Database credentials
$host = 'fdb28.awardspace.net';
$dbname = '4597042_scraptlap';
$username = '4597042_scraptlap';
$password = '123456789M';

// Create a connection to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error! Unable to connect: " . $e->getMessage());
}

// URL to fetch the JSON data from
$jsonUrl = "https://tlap.onrender.com/get_orderbook/symbol/GBPUSD/time/1740712800";  // Replace with your actual URL

// Fetch JSON data from the URL
$jsonData = file_get_contents($jsonUrl);

// Decode the JSON data
$data = json_decode($jsonData, true);

// Check if data is decoded successfully
if ($data === null) {
    die("Error decoding JSON data.");
}

try {
    // Start a transaction
    $pdo->beginTransaction();

    // Insert data into the currency_data table
    $stmt = $pdo->prepare("
        INSERT INTO currency_data (
            symbol, title, 
            positions_summ_sell_profit, positions_summ_sell_loss, positions_summ_buy_profit, positions_summ_buy_loss,
            positions_max, positions_min,
            orders_price, orders_summ_sell_limit, orders_summ_sell_stop, orders_summ_buy_limit, orders_summ_buy_stop,
            orders_max, orders_min
        ) VALUES (
            :symbol, :title,
            :positions_summ_sell_profit, :positions_summ_sell_loss, :positions_summ_buy_profit, :positions_summ_buy_loss,
            :positions_max, :positions_min,
            :orders_price, :orders_summ_sell_limit, :orders_summ_sell_stop, :orders_summ_buy_limit, :orders_summ_buy_stop,
            :orders_max, :orders_min
        )
    ");

    // Bind parameters and execute
    $stmt->execute([
        ':symbol' => $data['symbol'],
        ':title' => $data['title'],
        ':positions_summ_sell_profit' => $data['positions']['summ']['sell_profit'],
        ':positions_summ_sell_loss' => $data['positions']['summ']['sell_loss'],
        ':positions_summ_buy_profit' => $data['positions']['summ']['buy_profit'],
        ':positions_summ_buy_loss' => $data['positions']['summ']['buy_loss'],
        ':positions_max' => $data['positions']['max'],
        ':positions_min' => $data['positions']['min'],
        ':orders_price' => $data['orders']['price'],
        ':orders_summ_sell_limit' => $data['orders']['summ']['sell_limit'],
        ':orders_summ_sell_stop' => $data['orders']['summ']['sell_stop'],
        ':orders_summ_buy_limit' => $data['orders']['summ']['buy_limit'],
        ':orders_summ_buy_stop' => $data['orders']['summ']['buy_stop'],
        ':orders_max' => $data['orders']['max'],
        ':orders_min' => $data['orders']['min']
    ]);

    // Get the last inserted ID for currency_data (to link with positions_data and orders_data)
    $currencyDataId = $pdo->lastInsertId();

    // Insert positions data
    $positionsDataStmt = $pdo->prepare("
        INSERT INTO positions_data (currency_data_id, title, sell, buy) 
        VALUES (:currency_data_id, :title, :sell, :buy)
    ");

    foreach ($data['positions']['data'] as $position) {
        $positionsDataStmt->execute([
            ':currency_data_id' => $currencyDataId,
            ':title' => $position['title'],
            ':sell' => $position['sell'],
            ':buy' => $position['buy']
        ]);
    }

    // Insert orders data
    $ordersDataStmt = $pdo->prepare("
        INSERT INTO orders_data (currency_data_id, title, sell, buy) 
        VALUES (:currency_data_id, :title, :sell, :buy)
    ");

    foreach ($data['orders']['data'] as $order) {
        $ordersDataStmt->execute([
            ':currency_data_id' => $currencyDataId,
            ':title' => $order['title'],
            ':sell' => $order['sell'],
            ':buy' => $order['buy']
        ]);
    }

    // Commit the transaction
    $pdo->commit();

    echo "Data successfully inserted into the database.";

} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $pdo->rollBack();
    echo "Failed to insert data: " . $e->getMessage();
}
?>