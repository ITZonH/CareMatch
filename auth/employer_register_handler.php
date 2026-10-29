<?php
// Name : Hamed
// ID :C00259228
// Date: 18/10/2026
//Decription: this file handles employer registration

// starting the session to display registration errors
session_start();

// checking if the form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: employer_register.php", true, 303);
    exit;
}

// getting the employer details from the registration form
$organisation_name = trim($_POST["organisation_name"] ?? "");
$contact_name = trim($_POST["contact_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";


/* checking if any required fields are empty */
if (
    $organisation_name === "" ||
    $contact_name === "" ||
    $email === "" ||
    $password === "" ||
    $confirm_password === ""
) {
    $_SESSION["employer_register_error"] = "Please fill in all fields.";

    header("Location: employer_register.php", true,303);
    exit;
}

// checking if the organisation name is too long
if (mb_strlen($organisation_name) > 150) {
    $_SESSION["employer_register_error"] = "Organisation name is too long.";

    header("Location: employer_register.php", true, 303);
    exit;
}

// checking if the contact name is too long
if (mb_strlen($contact_name) > 100) {
    $_SESSION["employer_register_error"] = "Contact name is too long.";

    header("Location: employer_register.php", true, 303);
    exit;
}

// checking if the email address is valid
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["employer_register_error"] = "Please enter a valid email address.";

    header("Location: employer_register.php", true, 303);
     exit;
}

// checking if the password has at least 12 characters
if (strlen($password) < 12) {
    $_SESSION["employer_register_error"] = "Password must be at least 12 characters.";

    header("Location: employer_register.php", true, 303);
    exit;
}

// checking if both passwords match
if ($password !== $confirm_password) {
    $_SESSION["employer_register_error"] = "Passwords do not match.";

    header("Location: employer_register.php", true, 303);
    exit;
}



/* connecting to the CareMatch AI database */
require_once __DIR__ . "/../config/database.php";

try {

    // checking if the email address is already registered
    $stmt = $pdo->prepare(
        "SELECT user_id FROM users WHERE email = ? LIMIT 1"
    );

    // running the query using the employer email
    $stmt->execute([$email]);

    // checking if the database returned an existing account
    if ($stmt->fetch()) {

        // storing the error message in the session
        $_SESSION["employer_register_error"] =
            "This email address is already registered.";

        // redirecting the employer back to the registration page
        header("Location: employer_register.php", true, 303);
        exit;
    }

} catch (PDOException $e) {

    // showing a general error without exposing database details
    $_SESSION["employer_register_error"] =
        "Something went wrong. Please try again later.";

    // redirecting the employer back to the registration page
    header("Location: employer_register.php", true, 303);
    exit;
}


/* hashing the employer password before saving it */
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {

    // starting a transaction so both tables are updated together
    $pdo->beginTransaction();

    // adding the employer login details to the users table
    $stmt = $pdo->prepare(
        "INSERT INTO users (email, password_hash, role, account_status)
         VALUES (?, ?, 'employer', 'pending')"
    );

    // saving the employer account
    $stmt->execute([$email, $password_hash]);

    // getting the user ID created by MySQL
    $user_id = $pdo->lastInsertId();

    // adding the organisation details to the employers table
    $stmt = $pdo->prepare(
        "INSERT INTO employers (user_id, organisation_name, contact_name)
         VALUES (?, ?, ?)"
    );

    // saving the organisation details
    $stmt->execute([
        $user_id,
        $organisation_name,
        $contact_name
    ]);

    // saving both changes permanently
    $pdo->commit();

    // storing a message to show after registration
    $_SESSION["register_success"] =
        "Your employer account has been created and is waiting for admin approval.";

    // redirecting the employer to the login page
    header("Location: employer_login.php", true, 303);
    exit;

} catch (PDOException $e) {

    // undoing any database changes if something went wrong
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // showing a general error without exposing database details
    $_SESSION["employer_register_error"] =
        "Unable to create your account. Please try again.";

    // redirecting back to the registration page
    header("Location: employer_register.php", true, 303);
    exit;
}

