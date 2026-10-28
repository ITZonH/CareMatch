
<?php
// Name: Hamed Zon
// ID: C00259228
// Date: 17/10/2026
// Description: the is the worker register manager

session_start();

// this only POST is allow
if($_SERVER["REQUEST_METHOD"] !=="POST"){
    http_response_code(405);
    exit("Method Not Allowed");
}

// get all info submited by user
$full_name = trim($_POST["full_name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

$confirm_password = $_POST["confirm_password"] ?? "";

//checking if is any empty fields
if($full_name === "" || $email === "" || $password === "" || $confirm_password === ""){
    $_SESSION["register_error"] = "All fields are required.";

    header("Location:worker_register.php", true, 303);
    exit;
}

//checking if email is valid
if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $_SESSION["register_error"] = "Please enter a valid email address.";
    header("Location:worker_register.php", true, 303);
    exit;
}

//password lent should  be minimum 12 characters
if(strlen($password)<12){
    $_SESSION["register_error"] = "Password must be at least 12 characters..";
    header("Location:worker_register.php", true, 303);
    exit;
}

//checking to see if both match
if($password !== $confirm_password){
     $_SESSION["register_error"] = "Passwords do not match.";
    header("Location:worker_register.php", true, 303);
    exit;
}

// Connect to the database
require_once __DIR__ . '/../config/database.php';

//checking to see if the email already exist
$sql = "SELECT user_id FROM users WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);

$user = $stmt->fetch();

if($user){
    $_SESSION["register_error"] = "This email is already registered.";

    header("Location: worker_register.php", true, 303);
    exit;
}

//hash the password before registered in db
$password_hash = password_hash($password, PASSWORD_DEFAULT);

//starting a db transaction

try {
    $pdo->beginTransaction();

    // Insert the new account into the users table
    $sql = "INSERT INTO users(email,password_hash,role) VALUES (?,?,?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email,$password_hash,"worker"]);

    //geting id of new registered is PDO Method
    $user_id = $pdo->lastInsertId();

    // Create the healthcare worker's profile
    $sql = "INSERT INTO workers (user_id, full_name)
            VALUES (?, ?)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([$user_id, $full_name]);


// Saving both records
    $pdo->commit();
}

catch (PDOException $e) {

    // Undo the changes if something goes wrong
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Log the technical error privately
    error_log($e->getMessage());

    $_SESSION["register_error"] = "Registration failed. Please try again.";

    header("Location: worker_register.php", true, 303);
    exit;
}

// Registration completed successfully
$_SESSION["register_success"] = "Account created successfully. You can now log in.";

header("Location: worker_login.php", true, 303);
exit;
