<?php
session_start();

//if(!isset($_POST['submit'])) die();

include_once "../database/database.php";

$quantity = $_POST['qty'];
$product_id = $_POST['id'];
echo $product_id;
echo $quantity;
$db = new DB();

$db->update('cart',[
    'product_id' => $product_id,
],[
    'quantity' => $quantity
]);
header('location:../cart.php');