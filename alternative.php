<?php

$jsonUrl = "https://scripts.tlap.com/indicators/book.php?type=all&symbol=GBPUSD&time=1740712800";
$jsonData = file_get_contents($jsonUrl);
if ($jsonData === false) {
    die("Error fetching JSON data");
}
$data = json_decode($jsonData, true);

?>