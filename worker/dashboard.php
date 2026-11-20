
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 30/10/2026
// Description: This is the healthcare worker dashboard for CareMatch AI

// starting the session to check the logged-in worker
session_start();

// checking if the user is logged in
if (!isset($_SESSION["user_id"])) {

    // redirecting users who are not logged in
    header("Location: ../auth/worker_login.php", true, 303);
    exit;
}

// checking if the logged-in user is a worker
if (($_SESSION["role"] ?? "") !== "worker") {

    // preventing employers and admins from accessing this dashboard
    http_response_code(403);
    exit("Access denied.");
}


// connecting to the database
require_once __DIR__ . "/../config/database.php";

try {

    // checking that the worker account is active
    $stmt = $pdo->prepare(
        "SELECT email, account_status
         FROM users
         WHERE user_id = :user_id
         AND role = :role"
    );

    // executing the query
    $stmt->execute([
        ":user_id" => (int) $_SESSION["user_id"],
        ":role" => "worker"
    ]);

    // getting the worker account details
    $worker = $stmt->fetch(PDO::FETCH_ASSOC);

    // preventing unapproved workers from accessing the dashboard
    if (!$worker || $worker["account_status"] !== "active") {
        http_response_code(403);
        exit("Your worker account must be approved and active.");
    }

    // getting the worker email from the database
    $email = $worker["email"];

} catch (PDOException $e) {

    // recording database errors
    error_log("Worker dashboard error: " . $e->getMessage());

    http_response_code(500);
    exit("Unable to load worker dashboard. Please try again.");
}
    // setting the title for the shared header
    $pageTitle = "Healthcare Worker Dashboard";
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <!-- connecting the shared page layout CSS -->
    <link rel="stylesheet" href="../assets/css/shared/layout.css">

    <!-- connecting the shared sidebar CSS -->
    <link rel="stylesheet" href="../assets/css/shared/sidebar.css">

    <!-- connecting the shared header CSS -->
    <link rel="stylesheet" href="../assets/css/shared/header.css">

    <!-- connecting the shared footer CSS -->
    <link rel="stylesheet" href="../assets/css/shared/footer.css">

    <!-- connecting the worker dashboard CSS -->
    <link rel="stylesheet" href="../assets/css/worker_dashboard.css">

    <title>Worker Dashboard | CareMatch AI</title>



</head>

<body>

    <!-- this is the shared page layout -->
    <div class="cm-layout cm-layout--worker">

        <!-- displaying the shared sidebar -->
        <?php require __DIR__ . "/../includes/sidebar.php"; ?>

        <!-- holding the header, dashboard content and footer -->
        <div class="cm-layout__main">

            <!-- displaying the shared header -->
            <?php require __DIR__ . "/../includes/header.php"; ?>

            <!-- displaying the main dashboard content -->
            <main class="cm-layout__content">

            <!-- displaying the dashboard heading -->
            <h1>Healthcare Worker Dashboard</h1>

            <!-- welcoming the logged-in worker -->
            <p class="dashboard-description">
                Welcome to CareMatch AI,
                <?php echo htmlspecialchars($email, ENT_QUOTES, "UTF-8"); ?>
            </p>

            <!-- displaying the worker dashboard cards -->
            <div class="dashboard-cards">

                <!-- card for finding healthcare jobs -->
                <section class="dashboard-card" id="find-jobs">

                    <h3>Find Jobs</h3>

                    <p>
                        Explore healthcare job opportunities
                        that match your skills and experience.
                    </p>

                    <!-- link to browse available healthcare jobs -->
                    <a href="find_jobs.php" class="worker-btn">
                        Browse Jobs
                    </a>

                </section>

                
                <!-- card for viewing job applications -->
                <section class="dashboard-card" id="my-applications">

                    <h3>My Applications</h3>

                    <p>
                        View and track your applications
                        for healthcare job opportunities.
                    </p>

                    <!-- opening the worker applications page -->
                    <a href="my_applications.php" class="worker-btn">
                        View My Applications
                    </a>

                </section>


                <!-- card for managing worker profile -->
                <section class="dashboard-card" id="my-profile">

                    <h3>My Profile</h3>

                    <p>
                        View and update your professional
                        details, qualifications and experience.
                    </p>

                        <!-- worker profile feature will be added later -->
                        <span class="coming-soon">Coming Soon</span>
                </section>

            </div>

        </main>

        <!-- displaying the shared footer -->
        <?php require __DIR__ . "/../includes/footer.php"; ?>

    </div>

</div>

</body>
</html>
