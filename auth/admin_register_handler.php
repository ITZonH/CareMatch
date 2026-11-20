
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 28/10/2026
// Description: Handles CareMatch AI administrator registration

session_start();

// Redirect back to registration with an error
function registerError(string $message): void
{
    $_SESSION["admin_register_error"] = $message;

    header("Location: admin_register.php", true, 303);
    exit;
}

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin_register.php", true, 303);
    exit;
}

// Only allow registration locally or by an existing administrator
$remoteAddress = $_SERVER["REMOTE_ADDR"] ?? "";

$isLocal = in_array(
    $remoteAddress,
    ["127.0.0.1", "::1"],
    true
);


// Get registration details
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";

// Validate registration details
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    registerError("Please enter a valid email address.");
}

if (strlen($password) < 12) {
    registerError("Password must contain at least 12 characters.");
}

if ($password !== $confirmPassword) {
    registerError("Passwords do not match.");
}

// Connect to MySQL
require_once __DIR__ . "/../config/database.php";

// checking if administrator registration is permitted
if (!$isLocal) {

    // checking if the logged in user is an administrator
    if (
        !isset($_SESSION["user_id"]) ||
        ($_SESSION["role"] ?? "") !== "admin"
    ) {
        http_response_code(403);
        exit("Administrator registration is not permitted.");
    }

    try {

        // checking that the administrator account is active
        $stmt = $pdo->prepare(
            "SELECT account_status
             FROM users
             WHERE user_id = :user_id
             AND role = 'admin'"
        );

        // executing the query using the logged-in admin ID
        $stmt->execute([
            ":user_id" => (int) $_SESSION["user_id"]
        ]);

        // preventing inactive administrators from registering admins
        if ($stmt->fetchColumn() !== "active") {
            http_response_code(403);
            exit("Your administrator account must be active.");
        }

    } catch (PDOException $e) {

        // recording database errors
        error_log("Admin verification error: " . $e->getMessage());

        http_response_code(500);
        exit("Unable to verify administrator account.");
    }
}

try {

    // Check if the email is already registered
    $stmt = $pdo->prepare(
        "SELECT user_id FROM users WHERE email = ? LIMIT 1"
    );

    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        registerError("This email address is already registered.");
    }

    // Hash the password before saving
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert the new administrator
    $stmt = $pdo->prepare(
        "INSERT INTO users
         (email, password_hash, role, account_status)
         VALUES (?, ?, 'admin', 'active')"
    );

    $stmt->execute([
        $email,
        $passwordHash
    ]);

    // Show a success message on the login page
    $_SESSION["admin_login_success"] =
        "Administrator account created successfully. Please sign in.";

    // Redirect to admin login
    header("Location: admin_login.php", true, 303);
    exit;

} catch (PDOException $e) {

    error_log("Admin registration error: " . $e->getMessage());

    registerError("Unable to register administrator. Please try again.");
}
