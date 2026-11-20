
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 06/11/2026
// Description: this allows admins to view and approve jobs submitted by employers

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

// generating a CSRF token for approving or rejecting jobs
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// connecting to the database
require_once __DIR__ . "/../config/database.php";


 // checking that the logged-in admin account is active
try {

    // preparing the query to get the admin account status
    $stmt = $pdo->prepare(
        "SELECT account_status
         FROM users
         WHERE user_id = :user_id
         AND role = 'admin'"
    );

    // executing the query using the logged in admin ID
    $stmt->execute([
        ":user_id" => (int) $_SESSION["user_id"]
    ]);

    // getting the admin account status from the database
    $accountStatus = $stmt->fetchColumn();

    // preventing inactive admins from accessing this page
    if ($accountStatus !== "active") {
        http_response_code(403);
        exit("Your admin account must be active.");
    }

} catch (PDOException $e) {

    // recording database errors without displaying sensitive details
    error_log("Admin verification error: " . $e->getMessage());

    // displaying a general error if admin verification fails
    http_response_code(500);
    exit("Unable to verify admin account.");
}

// getting all jobs submitted by employers
$sql = "SELECT
            jobs.job_id,
            jobs.job_title,
            jobs.category,
            jobs.location,
            jobs.employment_type,
            jobs.status,
            jobs.approval_status,
            jobs.created_at,
            employers.organisation_name
        FROM jobs
        INNER JOIN employers
        ON jobs.employer_user_id = employers.user_id
        ORDER BY jobs.created_at DESC, jobs.job_id DESC";

// preparing the SQL query
$stmt = $pdo->prepare($sql);

// executing the query
$stmt->execute();

// getting all job postings
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- connecting the admin CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    <title>Manage Jobs | CareMatch AI Admin</title>
    

</head>

<body>

<!-- main admin layout -->
<main class="admin-layout">

    <!-- admin sidebar -->
    <aside class="admin-sidebar">

        <div class="sidebar-brand">
            <div class="brand-icon">+</div>

            <div>
                <div class="brand-name">
                    CareMatch <span>AI</span>
                </div>

                <div class="brand-subtitle">
                    Admin Panel
                </div>
            </div>
        </div>

        <!-- admin navigation -->
        <nav class="admin-navigation" aria-label="Admin navigation">

            <a href="dashboard.php">Dashboard</a>

            <a href="dashboard.php#employer-approvals">
                Employer Approvals
            </a>

            <a href="dashboard.php#worker-management">
                Worker Management
            </a>

            <a href="manage_jobs.php" class="active" aria-current="page">
                Job Management
            </a>

        </nav>

        <a href="../auth/logout.php" class="logout-link">
            Log Out
        </a>

    </aside>

    <!-- main page content -->
    <section class="admin-content">
      
        <!-- displaying the admin job management heading -->
        <header class="dashboard-header">
            <div>
                <h1>Admin Job Management</h1>

                <p>View and review jobs submitted by employers.</p>
            </div>
            <span class="admin-badge">Administrator</span>
        </header>

        <!-- link back to the admin dashboard -->
        <a href="dashboard.php">Back to Dashboard</a>

        <!-- displaying all job postings -->
        <h2 class="job-management">All Job Postings</h2>

        <!-- displaying a message after updating approval status -->
        <?php if (isset($_GET["updated"]) && $_GET["updated"] === "1"): ?>

            <p class="job-success-message" role="status">
                Job approval status updated successfully!
            </p>

        <?php endif; ?>

        <!-- checking if any jobs have been submitted -->
        <?php if (empty($jobs)): ?>

            <p class="job-empty-message">No jobs have been submitted yet.</p>

        <?php else: ?>

            <!-- displaying the jobs in a table -->
            <div class="job-table-container">
                <table class="job-table">

                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Organisation</th>
                            <th>Location</th>
                            <th>Job Status</th>
                            <th>Approval Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                <tbody>

                    <!-- displaying each job submitted by employers -->
                    <?php foreach ($jobs as $job): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars(
                                    $job["job_title"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    $job["organisation_name"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars(
                                    $job["location"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ); ?>
                            </td>
                            
                            <!-- displaying the job status -->
                            <td>
                                <?php echo htmlspecialchars(
                                    ucfirst($job["status"]),
                                    ENT_QUOTES,
                                    "UTF-8"
                                ); ?>
                            </td>

                            <td>
                                <span class="job-status <?php echo htmlspecialchars(
                                    $job["approval_status"],
                                    ENT_QUOTES,
                                    "UTF-8"
                                ); ?>">

                                    <?php echo htmlspecialchars(
                                        ucfirst($job["approval_status"]),
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ); ?>

                                </span>
                            </td>

                            
                            <td>
                                <!-- allowing admins to review the job before making a decision -->
                                <a href="job_details.php?job_id=<?php echo (int) $job["job_id"]; ?>"
                                class="management-link">
                                    View Details
                                </a>
                            </td>


                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>
           

            <?php endif; ?>

        </section> <!-- closing admin content -->

    </main> <!-- closing admin layout -->

</body>
</html>
