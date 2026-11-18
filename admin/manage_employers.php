<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 16/11/2026
// Description: Allows admins to view registered employers and their approval status

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

// generating a CSRF token for employer approval actions
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

    // getting all registered employers
    $sql = "SELECT
                users.user_id,
                users.email,
                users.account_status,
                employers.organisation_name
            FROM users
            INNER JOIN employers
            ON users.user_id = employers.user_id
            WHERE users.role = :role
            ORDER BY users.user_id DESC";

    // preparing the SQL query
    $stmt = $pdo->prepare($sql);

    // executing the query
    $stmt->execute([
        ":role" => "employer"
    ]);

    // getting all employers
    $employers = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    // recording database errors
    error_log("Admin employer management error: " . $e->getMessage());

    http_response_code(500);
    exit("Unable to load employers. Please try again.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Employers | CareMatch AI Admin</title>

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

            <a href="dashboard.php">Dashboard</a>

            <a href="manage_employers.php" class="active" aria-current="page">
                Employer Approvals
            </a>

            <a href="dashboard.php#worker-management">
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

        <!-- displaying the employer management heading -->
        <header class="dashboard-header">

            <div>
                <h1>Admin Employer Management</h1>

                <p>View and review registered employers.</p>
            </div>

            <span class="admin-badge">Administrator</span>

        </header>

        <!-- link back to the admin dashboard -->
        <a href="dashboard.php">Back to Dashboard</a>

        <!-- displaying all registered employers -->
        <h2 class="job-management">All Registered Employers</h2>

        <!-- displaying a message after updating employer status -->
        <?php if (isset($_GET["updated"]) && $_GET["updated"] === "1"): ?>

            <p class="job-success-message" role="status">
                Employer approval status updated successfully!
            </p>

        <?php endif; ?>

        <!-- checking if any employers have registered -->
        <?php if (empty($employers)): ?>

            <p class="job-empty-message">
                No employers have registered yet.
            </p>

        <?php else: ?>

            <!-- displaying employers in a table -->
            <div class="job-table-container">

                <table class="job-table">

                    <thead>
                        <tr>
                            <th>Organisation</th>
                            <th>Email</th>
                            <th>Account Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <!-- displaying each registered employer -->
                        <?php foreach ($employers as $employer): ?>

                            <tr>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $employer["organisation_name"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $employer["email"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ); ?>
                                </td>

                                <!-- displaying the employer account status -->
                                <td>
                                    <span class="job-status <?php echo htmlspecialchars(
                                        $employer["account_status"],
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ); ?>">

                                        <?php echo htmlspecialchars(
                                            ucfirst($employer["account_status"]),
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ); ?>

                                    </span>
                                </td>

                                <!-- displaying actions based on employer account status -->
                                <td>

                                    <?php if ($employer["account_status"] === "pending"): ?>

                                        <!-- form for approving or rejecting an employer -->
                                        <form action="update_employer_approval.php" method="POST" class="job-actions">

                                            <!-- sending the CSRF token -->
                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?php echo htmlspecialchars(
                                                    $_SESSION["csrf_token"],
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ); ?>">

                                            <!-- sending the employer ID -->
                                            <input
                                                type="hidden"
                                                name="employer_id"
                                                value="<?php echo (int) $employer["user_id"]; ?>">

                                            <!-- button for approving an employer -->
                                            <button type="submit" name="action" value="active" class="approve-button">
                                                Approve
                                            </button>

                                            <!-- button for rejecting an employer -->
                                            <button type="submit" name="action" value="rejected" class="reject-button">
                                                Reject
                                            </button>

                                        </form>

                                    <?php elseif ($employer["account_status"] === "active"): ?>

                                        <!-- allowing admins to revoke employer approval -->
                                        <form action="update_employer_approval.php" method="POST" class="job-actions">

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
                                                name="employer_id"
                                                value="<?php echo (int) $employer["user_id"]; ?>" >

                                            <button type="submit" name="action" value="pending" class="reject-button">
                                                Revoke Approval
                                            </button>

                                        </form>

                                    <?php elseif ($employer["account_status"] === "rejected"): ?>

                                        <!-- allowing admins to undo employer rejection -->
                                        <form action="update_employer_approval.php" method="POST" class="job-actions">

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
                                                name="employer_id"
                                                value="<?php echo (int) $employer["user_id"]; ?>">

                                            <button type="submit" name="action" value="pending" class="approve-button">
                                                Undo Rejection
                                            </button>

                                        </form>

                                    <?php else: ?>

                                        <span class="reviewed-label">No Action</span>

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