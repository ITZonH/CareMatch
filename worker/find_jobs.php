
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 20/11/2026
// Description: Allows healthcare workers to search and filter available jobs

// starting the session
session_start();

// checking if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/worker_login.php", true, 303);
    exit;
}

// checking if the logged in user is a worker
if (($_SESSION["role"] ?? "") !== "worker") {
    http_response_code(403);
    exit("Access denied.");
}

// connecting to the database
require_once __DIR__ . "/../config/database.php";

// listing the healthcare job categories
$categories = [
    "Nursing",
    "Healthcare Assistant",
    "Home Care",
    "Social Care",
    "Allied Health",
    "Medical and Clinical",
    "Mental Health",
    "Disability Support",
    "Elderly Care",
    "Childcare",
    "Healthcare Administration",
    "Healthcare Management",
    "Pharmacy",
    "Other"
];

// listing the counties in Ireland
$locations = [
    "Carlow",
    "Cavan",
    "Clare",
    "Cork",
    "Donegal",
    "Dublin",
    "Galway",
    "Kerry",
    "Kildare",
    "Kilkenny",
    "Laois",
    "Leitrim",
    "Limerick",
    "Longford",
    "Louth",
    "Mayo",
    "Meath",
    "Monaghan",
    "Offaly",
    "Roscommon",
    "Sligo",
    "Tipperary",
    "Waterford",
    "Westmeath",
    "Wexford",
    "Wicklow"
];

// listing the available employment types
$employmentTypes = [
    "Full-time",
    "Part-time",
    "Contract",
    "Temporary",
    "Permanent",
    "Agency",
    "Casual",
    "Internship"
];

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

    // preventing unapproved workers from accessing the page
    if (!$worker || $worker["account_status"] !== "active") {
        http_response_code(403);
        exit("Your worker account must be approved and active.");
    }

    // getting the search and filter values
    $search = trim($_GET["search"] ?? "");
    $category = trim($_GET["category"] ?? "");
    $location = trim($_GET["location"] ?? "");
    $employmentType = trim($_GET["employment_type"] ?? "");

    // limiting the length of the search values
    if (
        strlen($search) > 150 ||
        strlen($category) > 100 ||
        strlen($location) > 150 ||
        strlen($employmentType) > 50
    ) {
        http_response_code(400);
        exit("Invalid search or filter value.");
    }

    // checking that the selected category is valid
    if ($category !== "" && !in_array($category, $categories, true)) {
        http_response_code(400);
        exit("Invalid job category selected.");
    }

    // checking that the selected county is valid
    if ($location !== "" && !in_array($location, $locations, true)) {
        http_response_code(400);
        exit("Invalid county selected.");
    }

    // checking that the selected employment type is valid
    if (
        $employmentType !== "" &&
        !in_array($employmentType, $employmentTypes, true)
    ) {
        http_response_code(400);
        exit("Invalid employment type selected.");
    }

    // getting approved and open healthcare jobs
    $sql = "SELECT job_id, job_title, category, location,
                   employment_type, salary, job_description,
                   requirements, application_deadline, created_at
            FROM jobs
            WHERE approval_status = 'approved'
            AND status = 'open'
            AND (
                application_deadline IS NULL
                OR application_deadline >= CURRENT_DATE()
            )";

    // storing the search parameters
    $params = [];

    // filtering jobs by title, description or requirements
    if ($search !== "") {

        $sql .= " AND (
            job_title LIKE :search_title
            OR job_description LIKE :search_description
            OR requirements LIKE :search_requirements
        )";

        $params[":search_title"] = "%" . $search . "%";
        $params[":search_description"] = "%" . $search . "%";
        $params[":search_requirements"] = "%" . $search . "%";
    }

    // filtering jobs by healthcare category
    if ($category !== "") {
        $sql .= " AND category = :category";
        $params[":category"] = $category;
    }

    // filtering jobs by county using the location column
    if ($location !== "") {
        $sql .= " AND location = :location";
        $params[":location"] = $location;
    }

    // filtering jobs by employment type
    if ($employmentType !== "") {
        $sql .= " AND employment_type = :employment_type";
        $params[":employment_type"] = $employmentType;
    }

    // displaying the newest jobs first
    $sql .= " ORDER BY created_at DESC, job_id DESC";

    // preparing the job search query
    $stmt = $pdo->prepare($sql);

    // executing the query with the selected filters
    $stmt->execute($params);

    // getting the matching jobs
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    // recording database errors
    error_log("Worker job search error: " . $e->getMessage());

    http_response_code(500);
    exit("Unable to load jobs. Please try again.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Find Jobs | CareMatch AI</title>

    <!-- connecting the worker dashboard CSS -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/worker_dashboard.css">

</head>

<body>

    <!-- placing the sidebar and job search beside each other -->
    <div class="dashboard-layout">

        <!-- displaying the worker sidebar -->
        <aside class="sidebar">

            <!-- displaying the CareMatch AI name -->
            <h2>CareMatch <span>AI</span></h2>

            <!-- displaying the worker navigation links -->
            <nav aria-label="Worker navigation">

                <a href="dashboard.php">
                    Dashboard
                </a>

                <a href="find_jobs.php"
                   class="active"
                   aria-current="page">
                    Find Jobs
                </a>

                <a href="my_applications.php">
                    My Applications
                </a>

                <a href="dashboard.php#my-profile">
                    My Profile
                </a>

                <a href="../auth/logout.php">
                    Log Out
                </a>

            </nav>

        </aside>

        <!-- displaying the job search content -->
        <main class="dashboard-main">

            <!-- displaying the page heading -->
            <h1>Find Healthcare Jobs</h1>

            <p class="dashboard-description">
                Search and filter available healthcare opportunities.
            </p>

            <!-- displaying the job search section -->
            <section class="job-search-section">

                <h2>Search Jobs</h2>

                <!-- form for searching and filtering jobs -->
                <form action="find_jobs.php"
                      method="GET"
                      class="job-search-form">

                    <!-- searching by keyword -->
                    <div class="filter-group">

                        <label for="search">
                            Keyword
                        </label>

                        <input
                            type="search"
                            id="search"
                            name="search"
                            placeholder="Search job title or skills"
                            value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                        >

                    </div>

                    <!-- filtering by healthcare job category -->
                    <div class="filter-group">

                        <label for="category">
                            Job Category
                        </label>

                        <select id="category" name="category">

                            <option value="">
                                All Categories
                            </option>

                            <?php foreach ($categories as $item): ?>

                                <option
                                    value="<?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo $category === $item ? 'selected' : ''; ?>
                                >
                                    <?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- filtering by county -->
                    <div class="filter-group">

                        <label for="location">
                            County
                        </label>

                        <select id="location" name="location">

                            <option value="">
                                All Counties
                            </option>

                            <?php foreach ($locations as $item): ?>

                                <option
                                    value="<?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo $location === $item ? 'selected' : ''; ?>
                                >
                                    <?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- filtering by employment type -->
                    <div class="filter-group">

                        <label for="employment_type">
                            Employment Type
                        </label>

                        <select id="employment_type"
                                name="employment_type">

                            <option value="">
                                All Employment Types
                            </option>

                            <?php foreach ($employmentTypes as $item): ?>

                                <option
                                    value="<?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?>"
                                    <?php echo $employmentType === $item ? 'selected' : ''; ?>
                                >
                                    <?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <!-- displaying the search buttons -->
                    <div class="filter-actions">

                        <button type="submit" class="worker-btn">
                            Search Jobs
                        </button>

                        <a href="find_jobs.php" class="clear-filter-btn">
                            Clear Filters
                        </a>

                    </div>

                </form>

            </section>

            <!-- displaying available healthcare jobs -->
            <section class="job-results-section">

                <h2>
                    Available Jobs (<?php echo count($jobs); ?>)
                </h2>

                <!-- checking whether any jobs were found -->
                <?php if (empty($jobs)): ?>

                    <p class="no-jobs-message">
                        No healthcare jobs match your search.
                    </p>

                <?php else: ?>

                    <!-- displaying the matching job cards -->
                    <div class="job-results">

                        <?php foreach ($jobs as $job): ?>

                            <!-- displaying an available job -->
                            <article class="job-result-card">

                                <!-- displaying the job title -->
                                <h3>
                                    <?php echo htmlspecialchars($job["job_title"], ENT_QUOTES, "UTF-8"); ?>
                                </h3>

                                <!-- displaying the job category -->
                                <p>
                                    <strong>Category:</strong>
                                    <?php echo htmlspecialchars($job["category"], ENT_QUOTES, "UTF-8"); ?>
                                </p>

                                <!-- displaying the job county -->
                                <p>
                                    <strong>County:</strong>
                                    <?php echo htmlspecialchars($job["location"], ENT_QUOTES, "UTF-8"); ?>
                                </p>

                                <!-- displaying the employment type -->
                                <p>
                                    <strong>Employment Type:</strong>
                                    <?php echo htmlspecialchars($job["employment_type"], ENT_QUOTES, "UTF-8"); ?>
                                </p>

                                <!-- displaying the salary -->
                                <p>
                                    <strong>Salary:</strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $job["salary"] ?: "Not specified",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );
                                    ?>
                                </p>

                                <!-- displaying the application deadline -->
                                <p>
                                    <strong>Application Deadline:</strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $job["application_deadline"] ?: "Not specified",
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );
                                    ?>
                                </p>

                                <!-- displaying a short job description -->
                                <p class="job-result-description">

                                    <?php

                                    // getting the job description
                                    $description = $job["job_description"];

                                    // shortening the description
                                    if (function_exists("mb_strimwidth")) {

                                        $description = mb_strimwidth(
                                            $description,
                                            0,
                                            180,
                                            "...",
                                            "UTF-8"
                                        );

                                    } elseif (strlen($description) > 180) {

                                        $description = substr(
                                            $description,
                                            0,
                                            177
                                        ) . "...";
                                    }

                                    // displaying the description safely
                                    echo htmlspecialchars(
                                        $description,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    );

                                    ?>

                                </p>

                                <!-- linking to the job details page -->
                                <a
                                    href="job_details.php?job_id=<?php echo (int) $job['job_id']; ?>"
                                    class="worker-btn">
                                    View Job
                                </a>

                            </article>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </section>

        </main>

    </div>

</body>
</html>
