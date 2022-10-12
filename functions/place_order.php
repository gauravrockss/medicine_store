<?php
session_start();
if(!isset($_POST['submit'])) die();
include_once '../database/database.php';

$user_id = $_SESSION['id'];
$db = new DB();
[$user] = $db->fetch('users', ['id' => $user_id]);

$cart = $db->fetch('cart', ['user_id' => $user_id]);
$products = array_column($cart, 'id');
$quantity = array_column($cart, 'quantity');

$address = $_POST['address'];
$mode = $_POST['mode'] ?? 'PAID';       

$inserted = $db->insert('order_user', [
    'name' => $user['firstname'] . ' ' . $user['lastname'],
    'phone number' => $user['phonenumber'],
    'address' => $address ? $address : $user['address'],
    'mode' => $mode,
    'product_ids' => implode(',', $products),
    'quantity' => implode(',', $quantity)

]);
if($inserted) {
    $db->update('cart',[
        'user_id' => $user_id,
    ],[
        'status' => 1
    ]);
}


header('location:../cart.php?message=order placed successfully');