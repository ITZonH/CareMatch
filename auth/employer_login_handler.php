
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 24/10/2026
// Description: This file handles employer login

// starting the session to store login information
session_start();

// checking if the login form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    // redirecting back to the employer login page
    header("Location: employer_login.php", true, 303);
    exit;
}

// getting the employer email address from the form
$email = trim($_POST["email"] ?? "");

// getting the employer password from the form
$password = $_POST["password"] ?? "";

// checking if the email or password is empty
if ($email === "" || $password === "") {

    // storing the error message in the session
    $_SESSION["employer_login_error"] =
        "Please enter your email address and password.";

    // redirecting back to the login page
    header("Location: employer_login.php", true, 303);
    exit;
}

// checking if the email address is valid
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    // storing the error message in the session
    $_SESSION["employer_login_error"] =
        "Please enter a valid email address.";

    // redirecting back to the login page
    header("Location: employer_login.php", true, 303);
    exit;
}


/* connecting to the CareMatch AI database */
require_once __DIR__ . "/../config/database.php";

try {

   // searching for an employer account using their email
    $stmt = $pdo->prepare(
        "SELECT user_id, email, password_hash, role, account_status
        FROM users
        WHERE email = ?
        AND role = 'employer'
        LIMIT 1"
    );

    // running the query using the email entered
    $stmt->execute([$email]);

    // getting the account details from the database
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // checking if the account exists and the password is correct
    if (!$user || !password_verify($password, $user["password_hash"])) {

        // storing a general error message for incorrect details
        $_SESSION["employer_login_error"] =
            "Invalid email address or password.";

        // redirecting back to the employer login page
        header("Location: employer_login.php", true, 303);
        exit;
    }

    // checking if the account belongs to an employer
    if ($user["role"] !== "employer") {

        // preventing other account types from logging in here
        $_SESSION["employer_login_error"] =
            "Invalid email address or password.";

        header("Location: employer_login.php", true, 303);
        exit;
    }

    // checking if the employer account has been approved
    if ($user["account_status"] !== "active") {

        // telling the employer their account is not active yet
        $_SESSION["employer_login_error"] =
            "Your employer account is not active. Please contact the administrator.";

        header("Location: employer_login.php", true, 303);
        exit;
    }

    // creating a new session ID after successful login
    session_regenerate_id(true);

    // storing the employer details in the session
    $_SESSION["user_id"] = $user["user_id"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["role"] = $user["role"];

    // redirecting the employer to their dashboard
    header("Location: ../employer/dashboard.php", true, 303);
    exit;

} catch (PDOException $e) {

    // showing a general message without exposing database errors
    $_SESSION["employer_login_error"] =
        "Something went wrong. Please try again later.";

    // redirecting back to the employer login page
    header("Location: employer_login.php", true, 303);
    exit;
}
