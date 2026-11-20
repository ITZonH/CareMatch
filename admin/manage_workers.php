
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 19/11/2026
// Description: Allows admins to view  and manage registered workers and their account status

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

// generating a CSRF token for worker management actions
if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
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

    // getting all registered workers
    $sql = "SELECT
                user_id,
                email,
                account_status
            FROM users
            WHERE role = :role
            ORDER BY user_id DESC";

    // preparing the SQL query
    $stmt = $pdo->prepare($sql);

    // executing the query
    $stmt->execute([
        ":role" => "worker"
    ]);

    // getting all workers
    $workers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    // recording database errors
    error_log("Admin worker management error: " . $e->getMessage());

    http_response_code(500);
    exit("Unable to load workers. Please try again.");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Workers | CareMatch AI Admin</title>

    <!-- connecting the admin CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
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

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="manage_employers.php">
                Employer Management
            </a>

            <a href="manage_workers.php" class="active" aria-current="page">
                Worker Management
            </a>

            <a href="manage_jobs.php">
                Job Management
            </a>

        </nav>

        <a href="../auth/logout.php" class="logout-link">
            Log Out
        </a>

    </aside>

    <!-- main page content -->
    <section class="admin-content">

        <!-- displaying the worker management heading -->
        <header class="dashboard-header">

            <div>
                <h1>Admin Worker Management</h1>

                <p>View and manage registered workers.</p>
            </div>

            <span class="admin-badge">
                Administrator
            </span>

        </header>

        <!-- link back to the dashboard -->
        <p>
            <a href="dashboard.php">Back to Dashboard</a>
        </p>

        <!-- displaying all registered workers -->
        <h2 class="job-management">All Registered Workers</h2>

        <!-- displaying a message after updating worker status -->
        <?php if (isset($_GET["updated"]) && $_GET["updated"] === "1"): ?>

            <p class="job-success-message" role="status">
                Worker account status updated successfully!
            </p>

        <?php endif; ?>

        <!-- checking if any workers have registered -->
        <?php if (empty($workers)): ?>

            <p class="job-empty-message">
                No workers have registered yet.
            </p>

        <?php else: ?>

            <!-- displaying workers in a table -->
            <div class="job-table-container">

                <table class="job-table">

                    <thead>
                        <tr>
                            <th>Worker ID</th>
                            <th>Email</th>
                            <th>Account Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <!-- displaying each registered worker -->
                        <?php foreach ($workers as $worker): ?>

                            <tr>

                                <td>
                                    <?php echo (int) $worker["user_id"]; ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $worker["email"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ); ?>
                                </td>

                                <!-- displaying the worker account status -->
                                <td>
                                    <span class="job-status <?php echo htmlspecialchars(
                                        $worker["account_status"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ); ?>">

                                        <?php echo htmlspecialchars(
                                            ucfirst($worker["account_status"]),
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ); ?>

                                    </span>
                                </td>

                               
                                
                                <!-- displaying actions based on worker account status -->
                                <td>

                                    <?php if ($worker["account_status"] === "pending"): ?>

                                        <!-- form for approving or rejecting a worker -->
                                        <form action="update_worker_status.php" method="POST" class="job-actions">

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php echo htmlspecialchars(
                                                    $_SESSION["csrf_token"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ); ?>">

                                            <input
                                                type="hidden"
                                                name="worker_id"
                                                value="<?php echo (int) $worker["user_id"]; ?>" >

                                            <button type="submit" name="action" value="active" class="approve-button">
                                                Approve
                                            </button>

                                            <button type="submit" name="action" value="rejected" class="reject-button">
                                                Reject
                                            </button>

                                        </form>

                                    <?php elseif ($worker["account_status"] === "active"): ?>

                                        <!-- form for revoking worker approval -->
                                        <form action="update_worker_status.php" method="POST" class="job-actions">

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php echo htmlspecialchars(
                                                    $_SESSION["csrf_token"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ); ?>" >

                                            <input
                                                type="hidden"
                                                name="worker_id"
                                                value="<?php echo (int) $worker["user_id"]; ?>" >

                                            <button type="submit" name="action" value="pending" class="reject-button">
                                                Revoke Approval
                                            </button>

                                        </form>

                                    <?php elseif ($worker["account_status"] === "rejected"): ?>

                                        <!-- form for undoing worker rejection -->
                                        <form action="update_worker_status.php" method="POST" class="job-actions">

                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php echo htmlspecialchars(
                                                    $_SESSION["csrf_token"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ); ?>" >

                                            <input
                                                type="hidden"
                                                name="worker_id"
                                                value="<?php echo (int) $worker["user_id"]; ?>" >

                                            <button type="submit" name="action" value="pending" class="approve-button">
                                                Undo Rejection
                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <span class="reviewed-label">
                                            No Action
                                        </span>

                                    <?php endif; ?>

                                </td>


                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </section>

</main>

</body>
</html>
