<?php
if(!isset($_POST["submit"])) die();
include_once "../database/database.php";



    $medicinename =  $_POST["mname"];
    $price = $_POST["price"];
    $type = $_POST['type'];
    $description = $_POST['description'];
    $manufacter = $_POST['start'];
    $expiry = $_POST['end'];


    $db = new DB();
$inserted = $db->insert('medicine', [
        "name" => $medicinename,
        "price" => $price,
        "type" => $type,
        "description" => $description,
        "manufacter" => $manufacter,
        "expiry" => $expiry
    ]);
    
if($inserted) {
    header('location:../medicine.php');
} else {
    header('location:../insert-medicine.php?message = data is not inserted')
}