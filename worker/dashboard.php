
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

// getting the worker email from the session
$email = $_SESSION["email"] ?? "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Worker Dashboard | CareMatch AI</title>

    <!-- connecting the worker dashboard CSS file -->
    <link rel="stylesheet" href="../assets/css/worker_dashboard.css">

</head>

<body>

    <!-- this is the main dashboard layout -->
    <div class="dashboard-layout">

        <!-- this section holds the sidebar navigation -->
        <aside class="sidebar">

            <!-- displaying the CareMatch AI name -->
            <h2>CareMatch <span>AI</span></h2>

            <!-- navigation links for the healthcare worker -->
            <nav aria-label="Worker navigation">

                <!-- link to the current dashboard page -->
                <a href="dashboard.php" class="active" aria-current="page">
                    Dashboard
                </a>

                <!-- job search feature will be added later -->
                <a href="#find-jobs">Find Jobs</a>

                <!-- applications feature will be added later -->
                <a href="#my-applications">My Applications</a>

                <!-- worker profile feature will be added later -->
                <a href="#my-profile">My Profile</a>

                <!-- link for logging out -->
                <a href="../auth/logout.php">Log Out</a>

            </nav>

        </aside>

        <!-- this section holds the main dashboard content -->
        <main class="dashboard-main">

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

                    <span class="coming-soon">Coming Soon</span>

                </section>

                <!-- card for viewing job applications -->
                <section class="dashboard-card" id="my-applications">

                    <h3>My Applications</h3>

                    <p>
                        View and track your applications
                        for healthcare job opportunities.
                    </p>

                    <span class="coming-soon">Coming Soon</span>

                </section>

                <!-- card for managing worker profile -->
                <section class="dashboard-card" id="my-profile">

                    <h3>My Profile</h3>

                    <p>
                        View and update your professional
                        details, qualifications and experience.
                    </p>

                    <span class="coming-soon">Coming Soon</span>

                </section>

            </div>

        </main>

    </div>

</body>
</html>
