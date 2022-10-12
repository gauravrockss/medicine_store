<?php
if(!isset($_POST["submit"])) die();
session_start();

include_once"../database/database.php";

$db = new DB();

$username = $_POST["user"];
$password = $_POST["password"];

$db->fetch('users',[
    'username' => $username,
    'password' => $password
]);

if($db->getNumRows()) {
    $user = $db->getSingleRow();
    $_SESSION['id'] = $user['id'];
    $_SESSION['user'] = $user['username'];
    $_SESSION['role'] = $user['usertype'];
    if($user['usertype'] == 'user') {
        header('location:../data.php');
    } elseif ($user['usertype'] == 'admin') {
        header('location:../data.php');
    }
} else {
    header('location:../login.php?error=Username or password is incorrect');
}