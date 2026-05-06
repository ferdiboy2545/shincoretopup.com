<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

include "config.php";

/* CEK DB */
if (!isset($conn) || $conn->connect_error) {
  echo json_encode([
    "status" => "error",
    "message" => "Database tidak connect"
  ]);
  exit;
}

/* CEK METHOD */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo json_encode([
    "status" => "error",
    "message" => "Gunakan POST"
  ]);
  exit;
}

/* DATA */
$game      = $_POST['game'] ?? '';
$player_id = $_POST['player_id'] ?? '';
$diamond   = $_POST['diamond'] ?? '';
$payment   = $_POST['payment'] ?? '';

if(!$game || !$player_id || !$diamond || !$payment){
  echo json_encode([
    "status" => "error",
    "message" => "Data tidak lengkap"
  ]);
  exit;
}

/* PRICE */
$priceList = [
  100 => 15000,
  200 => 28000,
  500 => 65000,
  1000 => 120000
];

$price = $priceList[$diamond] ?? 0;

$order_id = "ORDER-" . time() . rand(100,999);

/* INSERT */
$query = mysqli_query($conn, "
INSERT INTO orders 
(order_id, game, player_id, diamond, payment, price, status)
VALUES 
('$order_id','$game','$player_id','$diamond','$payment','$price','Pending')
");

if($query){
  echo json_encode([
    "status" => "success",
    "message" => "Order berhasil dibuat",
    "order_id" => $order_id
  ]);
}else{
  echo json_encode([
    "status" => "error",
    "message" => mysqli_error($conn)
  ]);
}