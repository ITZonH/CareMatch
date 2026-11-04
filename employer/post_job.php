
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 04/11/2026
// Description: This page allows employers to enter new job details

// starting the session
session_start();

// checking if the user is logged in
if (!isset($_SESSION["user_id"])) {

    // redirecting users who are not logged in
    header("Location: ../auth/employer_login.php");
    exit;
}

// checking if the logged-in user is an employer
if (($_SESSION["role"] ?? "") !== "employer") {

    // preventing workers and admins from accessing this page
    http_response_code(403);
    exit("Access denied.");
}

// generating a CSRF token for the job posting form
if (empty($_SESSION["csrf_token"])) {

    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Post New Job | CareMatch AI</title>

    <!-- connecting the employer dashboard CSS -->
    <link rel="stylesheet" href="../assets/css/employer_dashboard.css">

</head>

<body>

    <!-- this is the main employer layout -->
    <main class="employer-layout">

        <!-- displaying the employer sidebar -->
        <aside class="employer-sidebar">

            <!-- displaying the CareMatch AI branding -->
            <div class="sidebar-brand">

                <div class="brand-icon">+</div>

                <div>
                    <div class="brand-name">
                        CareMatch <span>AI</span>
                    </div>

                    <div class="brand-subtitle">
                        Employer Panel
                    </div>
                </div>

            </div>

            <!-- displaying the employer navigation -->
            <nav class="employer-navigation" aria-label="Employer navigation">

                <!-- link to the employer dashboard -->
                <a href="dashboard.php">
                    Dashboard
                </a>

                <!-- link to the current job posting page -->
                <a href="post_job.php" class="active" aria-current="page">
                    Post New Job
                </a>

            </nav>

            <!-- link for logging out -->
            <a href="../auth/logout.php" class="logout-link">
                Log Out
            </a>

        </aside>

        <!-- displaying the main job posting content -->
        <section class="employer-content">

            <!-- displaying the page heading -->
            <header class="dashboard-header">

                <div>

                    <h1>Post New Job</h1>

                    <p>
                        Create a new healthcare job vacancy.
                    </p>

                </div>

                <span class="employer-badge">
                    Employer
                </span>

            </header>

            <!-- displaying the job posting form -->
            <section class="job-form-section">

                <h2>Job Details</h2>

                <!-- displaying a message after a job is published -->
                <?php if (isset($_GET["success"]) && $_GET["success"] === "1"): ?>

                    <p class="success-message" role="status">
                        Job published successfully!
                    </p>

                <?php endif; ?>

                <!-- submitting the job details to the PHP handler -->
                <form action="post_job_handler.php"  method="POST"  class="job-form">

                    <!-- sending the CSRF token with the form -->
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?php echo htmlspecialchars(  $_SESSION["csrf_token"],  ENT_QUOTES,   "UTF-8" ); ?>" >

                    <!-- entering the job title -->
                    <label for="job_title">
                        Job Title
                    </label>

                    <input type="text" id="job_title"   name="job_title"   maxlength="150"  required  >

                    <!-- selecting the healthcare category -->
                    <label for="category">
                        Healthcare Category
                    </label>

                    <select
                        id="category" name="category" required >

                        <option value="">
                            Select category
                        </option>

                        <option value="Nursing">
                            Nursing
                        </option>

                        <option value="Healthcare Assistant">
                            Healthcare Assistant
                        </option>

                        <option value="Home Care">
                            Home Care
                        </option>

                        <option value="Allied Health">
                            Allied Health
                        </option>

                        <option value="Other">
                            Other
                        </option>

                    </select>

                    <!-- entering the job location -->
                    <label for="location">
                        Location
                    </label>

                    <input  type="text"  id="location"  name="location"  maxlength="150"   required  >

                    <!-- selecting the employment type -->
                    <label for="employment_type">
                        Employment Type
                    </label>

                    <select id="employment_type" name="employment_type" required >

                        <option value="">
                            Select employment type
                        </option>

                        <option value="Full-time">
                            Full-time
                        </option>

                        <option value="Part-time">
                            Part-time
                        </option>

                        <option value="Contract">
                            Contract
                        </option>

                        <option value="Temporary">
                            Temporary
                        </option>

                    </select>

                    <!-- entering the salary -->
                    <label for="salary">
                        Salary (Optional)
                    </label>

                    <input type="text"  id="salary"  name="salary"  maxlength="100"  placeholder="e.g. €35,000 per year" >

                    <!-- entering the job description -->
                    <label for="job_description">
                        Job Description
                    </label>

                    <textarea
                        id="job_description"
                        name="job_description"
                        rows="6"
                        required></textarea>

                    <!-- entering the job requirements -->
                    <label for="requirements">
                        Job Requirements
                    </label>

                    <textarea
                        id="requirements"
                        name="requirements"
                        rows="5"></textarea>

                    <!-- selecting the application deadline -->
                    <label for="application_deadline">
                        Application Deadline (Optional)
                    </label>

                    <input type="date" id="application_deadline"  name="application_deadline" min="<?php echo date('Y-m-d'); ?>" >

                    <!-- button for publishing the job -->
                    <button
                        type="submit"
                        class="post-job-btn"
                    >
                        Publish Job
                    </button>

                </form>

            </section>

        </section>

    </main>

</body>
</html>
