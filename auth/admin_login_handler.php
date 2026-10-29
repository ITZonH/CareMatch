
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 28/10/2026
// Description: This file handles the CareMatch AI admin login

session_start();

// only allowing POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin_login.php", true, 303);
    exit;
}

// getting the login details
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

// checking the input
if ($email === "" || $password === "") {
    $_SESSION["admin_login_error"] = "Please enter your email and password.";
    header("Location: admin_login.php", true, 303);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["admin_login_error"] = "Please enter a valid email address.";
    header("Location: admin_login.php", true, 303);
    exit;
}

// connecting to the database
require_once __DIR__ . "/../config/database.php";

try {

    // finding the user account
    $stmt = $pdo->prepare(
        "SELECT user_id, email, password_hash, role, account_status
         FROM users
         WHERE email = ?
         LIMIT 1"
    );

    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // checking the email and password
    if (!$user || !password_verify($password, $user["password_hash"])) {
        $_SESSION["admin_login_error"] = "Invalid email address or password.";
        header("Location: admin_login.php", true, 303);
        exit;
    }

    // only allowing admins
    if ($user["role"] !== "admin") {
        $_SESSION["admin_login_error"] = "Invalid email address or password.";
        header("Location: admin_login.php", true, 303);
        exit;
    }

    // checking the account status
    if ($user["account_status"] !== "active") {
        $_SESSION["admin_login_error"] = "Your admin account is not active.";
        header("Location: admin_login.php", true, 303);
        exit;
    }

    // creating a fresh session after successful login
    session_regenerate_id(true);

    // saving the admin details
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["role"] = "admin";

    // redirecting to the admin dashboard
    header("Location: ../admin/dashboard.php", true, 303);
    exit;

} catch (PDOException $e) {

    $_SESSION["admin_login_error"] = "Database error. Please try again.";
    header("Location: admin_login.php", true, 303);
    exit;
}
