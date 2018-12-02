<?php
    require_once('database.php');
    
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    echo $fname . $lname . $username . $password;

    $database = new Database("localhost", "root", "", "");

    $database->createUser($username, $password, $fname, $lname);
?>