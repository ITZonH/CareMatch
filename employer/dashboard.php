
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 30/10/2026
// Description: This is the employer dashboard for CareMatch AI

// starting the session to check the logged-in employer
session_start();

// checking if the user is logged in
if (!isset($_SESSION["user_id"])) {

    // sending users who are not logged in to the login page
    header("Location: ../auth/employer_login.php");
    exit;
}

// checking if the logged-in user is an employer
if (($_SESSION["role"] ?? "") !== "employer") {

    // stopping workers and admins from accessing this dashboard
    http_response_code(403);
    exit("Access denied.");
}

// getting the employer email from the session
$email = $_SESSION["email"] ?? "";
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Employer Dashboard | CareMatch AI</title>

    <!-- connecting the employer dashboard CSS file -->
    <link rel="stylesheet" href="../assets/css/employer_dashboard.css">
</head>

<body>

    <!-- this is the main dashboard layout -->
    <div class="dashboard-layout">

        <!-- this section holds the sidebar navigation -->
        <aside class="sidebar">

            <!-- displaying the CareMatch AI name -->
            <h2>CareMatch <span>AI</span></h2>

            <!-- navigation links for the employer -->
            <nav aria-label="Employer navigation">

                <!-- link to the current dashboard page -->
                <a href="dashboard.php"
                   class="active"
                   aria-current="page">
                    Dashboard
                </a>

                <!-- job posting page will be added later -->
                <a href="#job-postings">Job Postings</a>

                <!-- applications page will be added later -->
                <a href="#applications">Applications</a>

                <!-- organisation profile will be added later -->
                <a href="#organisation">Organisation Profile</a>

                <!-- link for logging out -->
                <a href="../auth/logout.php">Log Out</a>

            </nav>

        </aside>

        <!-- this section holds the main dashboard content -->
        <main class="dashboard-main">

            <!-- welcoming the logged-in employer -->
            <h1>Employer Dashboard</h1>

            <p class="dashboard-description">
                Welcome to CareMatch AI,
                <?php
                    // displaying the employer email safely
                    echo htmlspecialchars(
                        $email,
                        ENT_QUOTES,
                        "UTF-8"
                    );
                ?>
            </p>

            <!-- displaying the employer dashboard cards -->
            <div class="dashboard-cards">

                <!-- card for managing job postings -->
                <section class="dashboard-card" id="job-postings">

                    <h3>Job Postings</h3>

                    <p>
                        Create and manage healthcare job
                        opportunities for your organisation.
                    </p>

                </section>

                <!-- card for viewing job applications -->
                <section class="dashboard-card" id="applications">

                    <h3>Applications</h3>

                    <p>
                        View applications from healthcare
                        professionals interested in your jobs.
                    </p>

                </section>

                <!-- card for managing organisation details -->
                <section class="dashboard-card" id="organisation">

                    <h3>Organisation Profile</h3>

                    <p>
                        View and update your organisation's
                        information and contact details.
                    </p>

                </section>

            </div>

        </main>

    </div>

</body>
</html>

