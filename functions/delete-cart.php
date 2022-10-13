<?php
if(!isset($_GET['id'])) die();
session_start();

include_once "../database/database.php";

$userId = $_SESSION['id'];
$productId = $_GET['id'];
$db = new DB();

$db->delete('cart', [
    'user_id' => $userId,
    'product_id' => $productId
]);
header('location:../cart.php');