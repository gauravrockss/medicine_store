<?php
    if(!isset($_POST["submit"])) die();
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
    $users = $db->fetch('users',[
        "username" => $username
    ]);

    if(count($users)) {
        header('location:../register.php?error=Username already exist');
        die();
    }

    $inserted = $db->insert('users', [
        "username" => $username,
        "password" => $password,
        "email" => $email,
        "firstname" => $firstname,
        "lastname" => $lastname,
        "dob" => $dob,
        "phonenumber" => $phonenumber,
        "address" => $address
    ]);

    
    if($inserted) {
        header('location:../login.php?success=Registration Success');
    } else {
        header('location:../register.php?error=Something went wrong');
    }
?>