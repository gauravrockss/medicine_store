<?php
session_start();
include_once '../includes/verify.php';
verify('admin');
if(!isset($_POST["update"])) die();

include_once "../database/database.php";
$db = new DB();

$id = $_POST['id'];
$name =  $_POST["name"];
$price = $_POST["price"];
$type = $_POST['type'];
$description = $_POST['description'];
$start = $_POST['start'];
$end = $_POST['end'];

$update = $db->update('medicine', 
    [
        'id' => $id,
    ],
    [
        'name' => $name,
        'price' => $price,
        'type' => $type,
        'description' => $description,
        'manufacter' => $start,
        'expiry' => $end
    ]
);
if($update) {
    header('location:../medicine.php');
}
