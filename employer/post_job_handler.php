
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 04/11/2026
// Description: Handles job postings created by employers

// starting the session
session_start();

// checking if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/employer_login.php");
    exit;
}

// checking if the logged-in user is an employer
if (($_SESSION["role"] ?? "") !== "employer") {
    http_response_code(403);
    exit("Access denied.");
}

// only allowing POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: post_job.php");
    exit;
}

// getting the logged-in employer's user ID
$employerUserId = (int) $_SESSION["user_id"];

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

// connecting to the CareMatch AI database
require_once __DIR__ . "/../config/database.php";

// getting the submitted job details
$jobTitle = trim($_POST["job_title"] ?? "");
$category = trim($_POST["category"] ?? "");
$location = trim($_POST["location"] ?? "");
$employmentType = trim($_POST["employment_type"] ?? "");
$salary = trim($_POST["salary"] ?? "");
$jobDescription = trim($_POST["job_description"] ?? "");
$requirements = trim($_POST["requirements"] ?? "");
$applicationDeadline = trim($_POST["application_deadline"] ?? "");

// checking required fields
if (
    $jobTitle === "" ||
    $category === "" ||
    $location === "" ||
    $employmentType === "" ||
    $jobDescription === ""
) {
    exit("Please complete all required fields.");
}

// checking maximum field lengths
if (
    mb_strlen($jobTitle) > 150 ||
    mb_strlen($category) > 100 ||
    mb_strlen($location) > 150 ||
    mb_strlen($employmentType) > 50 ||
    mb_strlen($salary) > 100
) {
    exit("One or more fields are too long.");
}

// checking the healthcare category
$allowedCategories = [
    "Nursing",
    "Healthcare Assistant",
    "Home Care",
    "Allied Health",
    "Other"
];

if (!in_array($category, $allowedCategories, true)) {
    exit("Invalid healthcare category.");
}

// checking the employment type
$allowedTypes = [
    "Full-time",
    "Part-time",
    "Contract",
    "Temporary"
];

if (!in_array($employmentType, $allowedTypes, true)) {
    exit("Invalid employment type.");
}

// checking the application deadline
if ($applicationDeadline !== "") {

    $date = DateTimeImmutable::createFromFormat(
        "!Y-m-d",
        $applicationDeadline
    );

    if (
        !$date ||
        $date->format("Y-m-d") !== $applicationDeadline
    ) {
        exit("Invalid application deadline.");
    }

    if ($applicationDeadline < date("Y-m-d")) {
        exit("The application deadline cannot be in the past.");
    }
}

// converting empty optional fields to NULL
$salary = $salary !== "" ? $salary : null;
$requirements = $requirements !== "" ? $requirements : null;
$applicationDeadline = $applicationDeadline !== ""
    ? $applicationDeadline
    : null;

try {

    // checking that the employer account is approved
    $stmt = $pdo->prepare(
        "SELECT account_status
         FROM users
         WHERE user_id = :user_id
         AND role = 'employer'"
    );

    $stmt->execute([
        ":user_id" => $employerUserId
    ]);

    $accountStatus = $stmt->fetchColumn();

    // preventing pending or unapproved employers from posting
    if ($accountStatus !== "active") {
        http_response_code(403);
        exit("Your employer account must be approved before posting jobs.");
    }

    // preparing the SQL query
    $sql = "INSERT INTO jobs (
                employer_user_id,
                job_title,
                category,
                location,
                employment_type,
                salary,
                job_description,
                requirements,
                application_deadline,
                status
            ) VALUES (
                :employer_user_id,
                :job_title,
                :category,
                :location,
                :employment_type,
                :salary,
                :job_description,
                :requirements,
                :application_deadline,
                'open'
            )";

    $stmt = $pdo->prepare($sql);

    // inserting the job details
    $stmt->execute([
        ":employer_user_id" => $employerUserId,
        ":job_title" => $jobTitle,
        ":category" => $category,
        ":location" => $location,
        ":employment_type" => $employmentType,
        ":salary" => $salary,
        ":job_description" => $jobDescription,
        ":requirements" => $requirements,
        ":application_deadline" => $applicationDeadline
    ]);

    // redirecting after successful job posting
    header("Location: post_job.php?success=1");
    exit;

} catch (PDOException $e) {

    // recording database errors
    error_log("Employer job posting error: " . $e->getMessage());

    http_response_code(500);
    exit("Unable to publish the job. Please try again.");
}
