<?php


if(!isset($_GET['id'])) die();

include_once "../database/database.php";

$productId = $_GET['id'];
$db = new DB();

$db->delete('cart',[
    'product_id' => $productId
]);
header('location:../cart.php');