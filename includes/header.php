<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Document</title>
        <style>
            * {
                padding: 0;
                margin: 0;
                font-family: sans-serif;
            }
            body {
                background-color:rgb(59, 224, 224);
            }
            .navbar h1 {
                flex-grow: 1;
            }
            .navbar a {
                padding: 0px 8px;
            }
            .navbar {
                background-color: aqua;
                padding: 8px 24px;
                display: flex;
                align-items: center;
            }
            .error, .success {
                font-size : 18px;
            }
            .error {
                color : red;
            }
            .success {
                color : green;
            }
            th {
                text-align : left;
            }
            table {
                width : 100%;
            }
        </style>