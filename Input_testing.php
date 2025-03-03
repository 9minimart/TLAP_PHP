<?php
// รับ JSON data ที่ส่งมาจาก Node.js
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {

    // ส่งค่ากลับไปยัง Node.js เพื่อยืนยัน
    echo json_encode(["status" => "success", "message" => "Data received", "data" => $data]);
} else {
    echo json_encode(["status" => "error", "message" => "No data received"]);
}
?>