
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 19/10/2026
// Description: This is the employer login page for CareMatch AI

// starting the session to access login messages
session_start();

// getting the employer login error
$error = $_SESSION["employer_login_error"] ?? "";

// removing the error after retrieving it
unset($_SESSION["employer_login_error"]);

// getting the registration success message
$success = $_SESSION["register_success"] ?? "";

// removing the success message after retrieving it
unset($_SESSION["register_success"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Employer Login | CareMatch AI</title>

    <!-- connecting the CSS file -->
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>

<body>

    <!-- this is the main container for employer login -->
    <main class="login-container">

        <!-- this section holds the employer login form -->
        <section class="login-card">

            <!-- displaying the CareMatch AI name and logo -->
            <div class="auth-brand">

                <div class="brand-icon">+</div>

                <div>
                    <div class="brand-name">
                        CareMatch <span>AI</span>
                    </div>

                    <div class="brand-subtitle">
                        Healthcare careers, connected.
                    </div>
                </div>

            </div>

            <!-- heading for the employer login page -->
            <h1>Employer Login</h1>

            <!-- explaining what the employer login is for -->
            <p class="auth-description">
                Sign in to manage your organisation and connect with healthcare professionals.
            </p>

            <!-- checking if employer registration was successful -->
            <?php if (!empty($success)): ?>

                <!-- displaying the success message safely -->
                <p class="success-message" role="status">
                    <?php
                        echo htmlspecialchars($success, ENT_QUOTES, "UTF-8");
                    ?>
                </p>

            <?php endif; ?>

            <!-- checking if there is any employer login error -->
            <?php if (!empty($error)): ?>

                <!-- displaying the login error safely -->
                <p class="error-message" role="alert">
                    <?php
                        echo htmlspecialchars($error, ENT_QUOTES,"UTF-8");
                    ?>
                </p>

            <?php endif; ?>

            <!-- form for collecting the employer login details -->
            <form action="employer_login_handler.php" method="POST">

                <!-- getting the employer email address -->
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" autocomplete="email" required>

                <!-- getting the employer password -->
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>

                <!-- button for showing or hiding the password -->
                <button type="button" id="togglePassword" class="password-toggle" aria-pressed="false">
                    Show Password
                </button>

                <!-- button for submitting the login form -->
                <button type="submit">Sign In</button>

            </form>

            <!-- link for employers who do not have an account -->
            <p class="auth-footer">
                Don't have an employer account?
                <a href="employer_register.php">Create an account</a>
            </p>

        </section>

    </main>

    <!-- connecting JavaScript for showing and hiding the password -->
    <script src="../assets/js/auth.js" defer></script>

</body>
</html>
