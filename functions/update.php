<?php
if(!isset($_POST["update"])) die();
session_start();
$user = $_SESSION['user'];
include_once "../database/database.php";

$username =  $_POST["user"];
$password = $_POST["password"];
$email = $_POST['email'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$dob = $_POST['dob'];
$phonenumber = $_POST['phonenumber'];
$address = $_POST['address'];

$db = new DB();
$update = $db->update('users', [
    'username' => $user,
],[
    'username' => $username,
    'password' => $password,
    'email' => $email,
    'firstname' => $firstname,
    'lastname' => $lastname,
    'dob' => $dob,
    'phonenumber' => $phonenumber,
    'address' => $address

]);

if($update) {
    header('location:../data.php');
}






