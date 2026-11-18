
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 17/11/2026
// Description: this manage admin approval and rejection of employer accounts

// starting the session
session_start();

// checking if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/admin_login.php");
    exit;
}

// checking if the loggedin user is an admin
if (($_SESSION["role"] ?? "") !== "admin") {
    http_response_code(403);
    exit("Access denied.");
}

// only allowing POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: manage_employers.php");
    exit;
}

// checking the CSRF token
$submittedToken = $_POST["csrf_token"] ?? "";
$sessionToken = $_SESSION["csrf_token"] ?? "";

if (
    !is_string($submittedToken) ||
    !is_string($sessionToken) ||
    $sessionToken === "" ||
    !hash_equals($sessionToken, $submittedToken)
) {
    http_response_code(403);
    exit("Invalid form submission. Please try again.");
}

// getting the submitted employer ID
$employerId = filter_input(INPUT_POST, "employer_id", FILTER_VALIDATE_INT);

if ($employerId === false || $employerId === null || $employerId < 1) {
    http_response_code(400);
    exit("Invalid employer ID.");
}

// getting the approval action
$action = $_POST["action"] ?? "";

// checking that the action is valid
$allowedActions = [
    "active",
    "rejected",
    "pending"
];

if (!is_string($action) || !in_array($action, $allowedActions, true)) {
    http_response_code(400);
    exit("Invalid approval action.");
}

// connecting to the database
require_once __DIR__ . "/../config/database.php";

try {

    // checking that the admin account is active
    $stmt = $pdo->prepare(
        "SELECT account_status
         FROM users
         WHERE user_id = :user_id
         AND role = :role"
    );

    $stmt->execute([
        ":user_id" => (int) $_SESSION["user_id"],
        ":role" => "admin"
    ]);

    $accountStatus = $stmt->fetchColumn();

    if ($accountStatus !== "active") {
        http_response_code(403);
        exit("Your admin account must be active.");
    }

    // updating the employer account status
    // allowing admins to undo approved or rejected decisions
    $sql = "UPDATE users
            SET account_status = :account_status
            WHERE user_id = :employer_id
            AND role = 'employer'
            AND EXISTS (
                SELECT 1
                FROM employers
                WHERE employers.user_id = users.user_id
            )
            AND (
                (:requested_action = 'pending'
                 AND account_status IN ('active', 'rejected'))
                OR
                (:requested_action_review IN ('active', 'rejected')
                 AND account_status = 'pending')
            )";

    // preparing the SQL query
    $stmt = $pdo->prepare($sql);

    // executing the query
    $stmt->execute([
        ":account_status" => $action,
        ":employer_id" => $employerId,
        ":requested_action" => $action,
        ":requested_action_review" => $action
    ]);

    // checking if the employer was updated
    if ($stmt->rowCount() === 0) {
        http_response_code(409);
        exit("Employer not found or approval status has already changed.");
    }

    // redirecting after updating the employer approval status
    header("Location: manage_employers.php?updated=1");
    exit;

} catch (PDOException $e) {

    // recording database errors
    error_log("Admin employer approval error: " . $e->getMessage());

    http_response_code(500);
    exit("Unable to update employer approval status. Please try again.");
}
