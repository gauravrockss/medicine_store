<?php           

session_start();

if(!isset($_POST['id'])) die();
if(!isset($_POST['name'])) die();

include_once "../database/database.php";

$productId = $_POST['id'];
$name = $_POST['name'];

$db = new DB();

$db->fetch('medicine', [
    'id' => $productId,
    'name' => $name
]);
if($db->getNumRows()) {
    $db->insert('cart', [
        'user_id' => $_SESSION['id'],
        'product_id' => $productId,
        'quantity' => 1
        
    ]);
    header('location:../cart.php');
} else {
    header('location:../medicine.php');
}