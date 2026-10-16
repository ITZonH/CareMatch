<?php

session_start();
//Only allows post requests
if($_SERVER["REQUEST_METHOD"] !== "POST"){

    http_response_code(405);
    exit("Method Not Allow");
}

    //read the details submied 
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    if($email ==="" || $password === ""){
        $_SESSION["login_error"] = "Email and password are required.";

        header("Location:worker_login.php", true, 303);
        exit;
    }

    // validate te email format using the built in methods,
    //and also redirect back to the login page if any error 
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION["login_error"] = "Please enter a valid email address.";
        header("Location: worker_login.php", true,303);
        exit;
    }

    //connect to the carematch db
require_once __DIR__.'/config/database.php';

//finding user using the email
$spl = "SELECT user_id, email, password_hash, role, account_status FROM users WHERE email = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
$user = $stmt->fetch();

//cheching if the email exists and the password is correct
if(!$user || !password_verify($password, $user["password_hash"]) ){
    $_SESSION["login_error"] = "Invalid email or password.";
    header("Location: worker_login.php", true,303);
    exit;
}

//check if users account is active

if($user["account_status"] !=="active"){
    $_SESSION["login_error"] = "Your account is not active.";

    header("Location:worker_login.php", true. 303);
    exit;
}

//checking role and only workers can use this login
if($user["role"] !=="worker"){
    $_SESSION["login_error"] = "Invalid email or password.";
    header("Location: worker_login.php", true,303);
    exit;
}

// generate a new session id after successful login
session_regenerate_id(true);
// Store the logged-in user's details
$_SESSION["user_id"] = $user["user_id"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = $user["role"];

// Redirect the worker to their dashboard
header("Location: ../worker/dashboard.php", true, 303);
exit;



    