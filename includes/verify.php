<?php
function verify($role) {
    if(!(isset($_SESSION['user']) && $_SESSION['user'] && isset($_SESSION['role']) && $_SESSION['role'] === $role)) {
        session_start();
        session_unset();
        session_destroy();
        header('location:login.php');
        die();
    }
}