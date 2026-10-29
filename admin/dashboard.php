
<?php
// Name : Hamed Zon
// Id: C00259228
// Date: 29/10/2026
// Description: This is the admin dashboard for CareMatch AI

// starting the session to check the logged-in admin
session_start();

// checking if the user is logged in
if (!isset($_SESSION["user_id"])) {

    // redirecting users who are not logged in
    header("Location: ../auth/admin_login.php");
    exit;
}

// checking if the logged-in user is an admin
if (($_SESSION["role"] ?? "") !== "admin") {

    // preventing workers and employers from accessing the admin dashboard
    http_response_code(403);
    exit("Access denied.");
}

// getting the admin email from the session
$email = $_SESSION["email"] ?? "";

// connecting to the CareMatch AI database
require_once __DIR__ . "/../config/database.php";

// setting the default dashboard statistics
$totalEmployers = 0;
$totalWorkers = 0;
$pendingApprovals = 0;

// tracking whether the statistics loaded successfully
$statisticsLoaded = false;

try {

    // counting all registered employers
    $stmt = $pdo->query(
        "SELECT COUNT(*) FROM users WHERE role = 'employer'"
    );

    $totalEmployers = (int) $stmt->fetchColumn();

    // counting all registered healthcare workers
    $stmt = $pdo->query(
        "SELECT COUNT(*) FROM users WHERE role = 'worker'"
    );

    $totalWorkers = (int) $stmt->fetchColumn();

    // counting employers waiting for approval
    $stmt = $pdo->query(
        "SELECT COUNT(*) FROM users
         WHERE role = 'employer'
         AND account_status = 'pending'"
    );

    $pendingApprovals = (int) $stmt->fetchColumn();

    // confirming that the statistics loaded successfully
    $statisticsLoaded = true;

} catch (PDOException $e) {

    // recording database errors without displaying sensitive details
    error_log("Admin dashboard error: " . $e->getMessage());

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | CareMatch AI</title>

    <!-- connecting the admin dashboard CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body>

    <!-- this section holds the main admin dashboard -->
    <main class="admin-layout">

        <!-- displaying the admin sidebar -->
        <aside class="admin-sidebar">

            <!-- displaying the CareMatch AI branding -->
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

            <!-- displaying the admin navigation -->
            <nav class="admin-navigation" aria-label="Admin navigation">

                <a href="dashboard.php" class="active">
                    Dashboard
                </a>

                <a href="#employer-approvals">
                    Employer Approvals
                </a>

                <a href="#worker-management">
                    Worker Management
                </a>

                <a href="#job-management">
                    Job Management
                </a>

            </nav>

            <!-- link for logging out -->
            <a href="../auth/logout.php" class="logout-link">
                Log Out
            </a>

        </aside>

        <!-- displaying the main dashboard content -->
        <section class="admin-content">

            <!-- displaying the dashboard heading -->
            <header class="dashboard-header">

                <div>

                    <h1>Admin Dashboard</h1>

                    <!-- welcoming the logged-in admin -->
                    <p>
                        Welcome,
                        <?php echo htmlspecialchars($email, ENT_QUOTES, "UTF-8"); ?>
                    </p>

                </div>

                <span class="admin-badge">
                    Administrator
                </span>

            </header>

            <!-- explaining what the admin can manage -->
            <p class="dashboard-description">
                From here, you will be able to manage employers,
                healthcare workers and job postings.
            </p>

            <!-- displaying an error if statistics cannot be loaded -->
            <?php if (!$statisticsLoaded): ?>

                <p class="error-message" role="alert">
                    Unable to load dashboard statistics at the moment.
                </p>

            <?php endif; ?>

            <!-- displaying the dashboard overview -->
            <section class="dashboard-overview">

                <h2>Platform Overview</h2>

                <div class="stats-grid">

                    <!-- displaying the total number of registered employers -->
                    <article class="stat-card">

                        <h3>Total Employers</h3>

                        <p>
                            <?php
                            echo $statisticsLoaded ? $totalEmployers : "—";
                            ?>
                        </p>

                        <span>Registered employers</span>

                    </article>

                    <!-- displaying the total number of healthcare workers -->
                    <article class="stat-card">

                        <h3>Healthcare Workers</h3>

                        <p>
                            <?php
                            echo $statisticsLoaded ? $totalWorkers : "—";
                            ?>
                        </p>

                        <span>Registered workers</span>

                    </article>

                    <!-- displaying the number of employers awaiting approval -->
                    <article class="stat-card">

                        <h3>Pending Approvals</h3>

                        <p>
                            <?php
                            echo $statisticsLoaded ? $pendingApprovals : "—";
                            ?>
                        </p>

                        <span>Employers awaiting review</span>

                    </article>

                    <!-- job posting statistics will be connected later -->
                    <article class="stat-card">

                        <h3>Job Postings</h3>

                        <p>—</p>

                        <span>Jobs on the platform</span>

                    </article>

                </div>

            </section>

            <!-- this section will hold the admin features -->
            <section class="admin-management">

                <h2>Admin Management</h2>

                <div class="management-grid">

                    <!-- employer approval feature will be added later -->
                    <article class="management-card" id="employer-approvals">

                        <h3>Employer Approvals</h3>

                        <p>
                            Review employer registrations and manage
                            account approval requests.
                        </p>

                        <span class="coming-soon">
                            Coming Soon
                        </span>

                    </article>

                    <!-- worker management feature will be added later -->
                    <article class="management-card" id="worker-management">

                        <h3>Worker Management</h3>

                        <p>
                            View registered healthcare workers and
                            manage their accounts.
                        </p>

                        <span class="coming-soon">
                            Coming Soon
                        </span>

                    </article>

                    <!-- job management feature will be added later -->
                    <article class="management-card" id="job-management">

                        <h3>Job Management</h3>

                        <p>
                            Review job postings and manage
                            opportunities published by employers.
                        </p>

                        <span class="coming-soon">
                            Coming Soon
                        </span>

                    </article>

                </div>

            </section>

        </section>

    </main>

</body>
</html>
