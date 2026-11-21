
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 19/11/2026
// Description: Allows healthcare workers to apply for approved jobs

// starting the session
session_start();

// checking if the worker is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/worker_login.php");
    exit;
}

// checking the user role
if (($_SESSION["role"] ?? "") !== "worker") {
    http_response_code(403);
    exit("Access denied.");
}

// connecting to the database
require_once __DIR__ . "/../config/database.php";

// getting the logged-in worker ID
$workerUserId = (int) $_SESSION["user_id"];

$error = "";
$success = "";

// displaying database values safely
function escape($value) {
    return htmlspecialchars(
        (string) ($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// getting the selected job ID
$jobId = filter_input(INPUT_GET, "job_id", FILTER_VALIDATE_INT);

if (!$jobId || $jobId < 1) {
    http_response_code(400);
    exit("Invalid job ID.");
}

// checking the worker account and selected job
try {

    // checking if the worker account is active
    $stmt = $pdo->prepare(
        "SELECT account_status
         FROM users
         WHERE user_id = :user_id
         AND role = 'worker'"
    );

    $stmt->execute([
        ":user_id" => $workerUserId
    ]);

    if ($stmt->fetchColumn() !== "active") {
        http_response_code(403);
        exit("Your worker account must be active to apply for jobs.");
    }

    // getting approved and available job details
    $stmt = $pdo->prepare(
        "SELECT *
         FROM jobs
         WHERE job_id = :job_id
         AND approval_status = 'approved'
         AND status = 'open'
         AND (
             application_deadline IS NULL
             OR application_deadline >= CURRENT_DATE()
         )"
    );

    $stmt->execute([
        ":job_id" => $jobId
    ]);

    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        http_response_code(404);
        exit("Job not found or no longer available.");
    }

    // checking if the worker has already applied
    $stmt = $pdo->prepare(
        "SELECT application_id
         FROM job_applications
         WHERE job_id = :job_id
         AND worker_user_id = :worker_user_id"
    );

    $stmt->execute([
        ":job_id" => $jobId,
        ":worker_user_id" => $workerUserId
    ]);

    $alreadyApplied = (bool) $stmt->fetchColumn();

} catch (PDOException $e) {

    // recording database errors
    error_log("Worker application page error: " . $e->getMessage());

    http_response_code(500);
    exit("Unable to load the application page. Please try again.");
}

// creating a token to protect the application form
if (empty($_SESSION["application_csrf_token"])) {
    $_SESSION["application_csrf_token"] = bin2hex(random_bytes(32));
}

// storing the cover letter when the form has an error
$coverLetter = "";

// processing the application form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // checking the form token
    $token = $_POST["csrf_token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["application_csrf_token"], $token)
    ) {
        http_response_code(403);
        exit("Invalid form submission.");
    }

    // preventing duplicate applications
    if ($alreadyApplied) {

        $error = "You have already applied for this job.";

    } else {

        // getting the cover letter and uploaded CV
        $coverLetter = $_POST["cover_letter"] ?? "";
        $cv = $_FILES["cv"] ?? null;

        // checking the cover letter
        if (!is_string($coverLetter)) {

            $error = "Invalid cover letter.";
            $coverLetter = "";

        } else {

            $coverLetter = trim($coverLetter);
        }

        // checking the cover letter length
        if (!$error && mb_strlen($coverLetter) > 5000) {
            $error = "Your cover letter must not exceed 5000 characters.";
        }

        // checking if a CV was uploaded
        if (
            !$error &&
            (
                !is_array($cv) ||
                !isset($cv["error"]) ||
                $cv["error"] !== UPLOAD_ERR_OK
            )
        ) {
            $error = "Please upload your CV in PDF, DOC or DOCX format.";
        }

        // checking the CV size and temporary file
        if (!$error) {

            // maximum CV size is 5MB
            if (
                !isset($cv["size"]) ||
                !is_int($cv["size"]) ||
                $cv["size"] < 1 ||
                $cv["size"] > 5 * 1024 * 1024
            ) {
                $error = "Your CV must not exceed 5MB.";
            }

            if (
                !$error &&
                (
                    !isset($cv["tmp_name"]) ||
                    !is_string($cv["tmp_name"]) ||
                    !is_uploaded_file($cv["tmp_name"])
                )
            ) {
                $error = "Invalid CV upload.";
            }
        }

        // checking the uploaded CV format
        if (!$error) {

            // checking the uploaded CV filename
            if (
                !isset($cv["name"]) ||
                !is_string($cv["name"])
            ) {
                $error = "Invalid CV filename.";

            } else {

                // getting the original file extension
                $extension = strtolower(
                    pathinfo($cv["name"], PATHINFO_EXTENSION)
                );

                // allowing PDF, DOC and DOCX
                $allowedExtensions = ["pdf", "doc", "docx"];

                if (!in_array($extension, $allowedExtensions, true)) {

                    $error = "Only PDF, DOC and DOCX CV files are allowed.";

                } else {

                    // checking the actual file MIME type
                    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $fileInfo->file($cv["tmp_name"]);

                    $allowedTypes = [
                        "pdf" => [
                            "application/pdf"
                        ],
                        "doc" => [
                            "application/msword",
                            "application/x-ole-storage",
                            "application/CDFV2"
                        ],
                        "docx" => [
                            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
                            "application/zip"
                        ]
                    ];

                    if (
                        !in_array(
                            $mimeType,
                            $allowedTypes[$extension],
                            true
                        )
                    ) {
                        $error = "The uploaded CV format is not valid.";
                    }
                }
            }
        }

        // checking the document structure
        if (!$error) {

            if ($extension === "pdf") {

                // checking the PDF file header
                $handle = fopen($cv["tmp_name"], "rb");

                if ($handle === false) {

                    $error = "Unable to read the uploaded CV.";

                } else {

                    $header = fread($handle, 5);
                    fclose($handle);

                    if ($header !== "%PDF-") {
                        $error = "The uploaded PDF is not valid.";
                    }
                }

            } elseif ($extension === "docx") {

                // checking the Word document ZIP structure
                if (!class_exists("ZipArchive")) {

                    $error = "Word document uploads are temporarily unavailable.";

                } else {

                    $zip = new ZipArchive();

                    if ($zip->open($cv["tmp_name"]) !== true) {

                        $error = "The uploaded Word document is not valid.";

                    } else {

                        // checking for required DOCX files
                        $validDocx =
                            $zip->locateName("[Content_Types].xml") !== false &&
                            $zip->locateName("_rels/.rels") !== false &&
                            $zip->locateName("word/document.xml") !== false;

                        $zip->close();

                        if (!$validDocx) {
                            $error = "The uploaded Word document is not valid.";
                        }
                    }
                }

            } elseif ($extension === "doc") {

                // checking the legacy Word document header
                $handle = fopen($cv["tmp_name"], "rb");

                if ($handle === false) {

                    $error = "Unable to read the uploaded CV.";

                } else {

                    $header = fread($handle, 8);
                    fclose($handle);

                    $validDocHeader = hex2bin("D0CF11E0A1B11AE1");

                    if ($header !== $validDocHeader) {
                        $error = "The uploaded DOC file is not valid.";
                    }
                }
            }
        }

        // saving the CV and application
        if (!$error) {

            // this folder must be outside the public web root
            $uploadDirectory = dirname(__DIR__, 2)
                . "/carematch_private/cvs/";

            // creating the CV folder if it does not exist
            if (!is_dir($uploadDirectory)) {

                if (!mkdir($uploadDirectory, 0700, true)) {
                    $error = "Unable to prepare CV storage.";
                }
            }

            // checking the CV folder is writable
            if (!$error && !is_writable($uploadDirectory)) {
                $error = "CV storage is not writable.";
            }

            if (!$error) {

                // generating a unique filename
                $fileName = bin2hex(random_bytes(16))
                    . "." . $extension;

                $filePath = $uploadDirectory . $fileName;

                // moving the uploaded CV to private storage
                if (!move_uploaded_file($cv["tmp_name"], $filePath)) {

                    $error = "Unable to upload your CV.";

                } else {

                    // restricting access to the CV file
                    chmod($filePath, 0600);

                    try {

                        // checking the job is still accepting applications
                        $stmt = $pdo->prepare(
                            "SELECT job_id
                             FROM jobs
                             WHERE job_id = :job_id
                             AND approval_status = 'approved'
                             AND status = 'open'
                             AND (
                                 application_deadline IS NULL
                                 OR application_deadline >= CURRENT_DATE()
                             )"
                        );

                        $stmt->execute([
                            ":job_id" => $jobId
                        ]);

                        if (!$stmt->fetchColumn()) {

                            $error = "This job is no longer accepting applications.";

                        } else {

                            // saving the worker application
                            $stmt = $pdo->prepare(
                                "INSERT INTO job_applications
                                 (
                                     job_id,
                                     worker_user_id,
                                     cover_letter,
                                     cv_path
                                 )
                                 VALUES
                                 (
                                     :job_id,
                                     :worker_user_id,
                                     :cover_letter,
                                     :cv_path
                                 )"
                            );

                            $stmt->execute([
                                ":job_id" => $jobId,
                                ":worker_user_id" => $workerUserId,
                                ":cover_letter" => $coverLetter,
                                ":cv_path" => $fileName
                            ]);

                            // marking the application as submitted
                            $alreadyApplied = true;

                            $success = "Your application has been submitted successfully.";

                            // creating a new form token
                            $_SESSION["application_csrf_token"] =
                                bin2hex(random_bytes(32));
                        }

                    } catch (PDOException $e) {

                        // checking for duplicate applications
                        if (
                            $e->getCode() === "23000" &&
                            isset($e->errorInfo[1]) &&
                            (int) $e->errorInfo[1] === 1062
                        ) {

                            $error = "You have already applied for this job.";
                            $alreadyApplied = true;

                        } else {

                            // recording unexpected database errors
                            error_log(
                                "Job application error: " . $e->getMessage()
                            );

                            $error = "Unable to submit your application. Please try again.";
                        }
                    }

                    // removing the CV if the application was not saved
                    if ($error && is_file($filePath)) {
                        unlink($filePath);
                    }
                }
            }
        }
    }
}

// setting the title for the shared header
$pageTitle = "Apply for Job";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Apply for Job | CareMatch AI</title>

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

</head>

<body>

<!-- this is the shared page layout -->
<div class="cm-layout cm-layout--worker">

    <!-- displaying the shared sidebar -->
    <?php require __DIR__ . "/../includes/sidebar.php"; ?>

    <!-- holding the header, application content and footer -->
    <div class="cm-layout__main">

        <!-- displaying the shared header -->
        <?php require __DIR__ . "/../includes/header.php"; ?>

        <!-- displaying the application content -->
        <main class="cm-layout__content">

            <h1>Apply for Job</h1>

            <p class="dashboard-description">
                Submit your application for the selected healthcare position.
            </p>

            <section class="job-details-card">

                <!-- displaying the selected job -->
                <h2>
                    <?php echo escape($job["job_title"]); ?>
                </h2>

                <p class="job-organisation">
                    <?php echo escape($job["location"] ?? ""); ?>
                </p>

                <!-- displaying application errors -->
                <?php if ($error): ?>

                    <p class="application-error" role="alert">
                        <?php echo escape($error); ?>
                    </p>

                <?php endif; ?>

                <!-- displaying successful applications -->
                <?php if ($success): ?>

                    <p class="application-success" role="status">
                        <?php echo escape($success); ?>
                    </p>

                <?php endif; ?>

                <!-- preventing workers from applying twice -->
                <?php if ($alreadyApplied): ?>

                    <p>
                        You have already submitted an application for this job.
                    </p>

                    <div class="job-details-actions">

                        <a href="my_applications.php" class="worker-btn">
                            View My Applications
                        </a>

                    </div>

                    <a href="find_jobs.php" class="job-back-link">
                        Back to Find Jobs
                    </a>

                <?php else: ?>

                    <!-- job application form -->
                    <form
                        method="POST"
                        action="apply_job.php?job_id=<?php echo (int) $jobId; ?>"
                        enctype="multipart/form-data">

                        <!-- form security token -->
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php echo escape($_SESSION["application_csrf_token"]); ?>"
                        >

                        <!-- CV upload section -->
                        <div class="job-detail-section">

                            <h3>Upload Your CV</h3>

                            <p>
                                Upload your CV in PDF, DOC or DOCX format
                                (maximum 5MB).
                            </p>

                            <!-- allowing PDF and Word CVs -->
                            <input
                                type="file"
                                name="cv"
                                class="application-cv-input"
                                accept=".pdf,.doc,.docx"
                                required
                            >

                        </div>

                        <!-- cover letter section -->
                        <div class="job-detail-section">

                            <h3>Cover Letter (Optional)</h3>

                            <textarea
                                name="cover_letter"
                                class="application-cover-letter"
                                rows="7"
                                maxlength="5000"
                                placeholder="Explain why you are suitable for this position...">
                                <?php echo escape($coverLetter); ?></textarea>

                        </div>

                        <!-- submit application button -->
                        <div class="job-details-actions">

                            <button type="submit" class="worker-btn">
                                Submit Application
                            </button>

                        </div>

                    </form>

                    <!-- returning to job details -->
                    <a
                        href="job_details.php?job_id=<?php echo (int) $jobId; ?>"
                        class="job-back-link"
                    >
                        Back to Job Details
                    </a>

                <?php endif; ?>

            </section>

        </main>

        <!-- displaying the shared footer -->
        <?php require __DIR__ . "/../includes/footer.php"; ?>

    </div>

</div>

</body>
</html>
