
<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 28/10/2026

// Description: This is the admin login page for CareMatch AI

// starting the session to display login messages
session_start();

// getting the login error message if there is one
$error = $_SESSION["admin_login_error"] ?? "";

// removing the error message after reading it
unset($_SESSION["admin_login_error"]);

// getting the registration success message if there is one
$success = $_SESSION["admin_login_success"] ?? "";

// removing the success message after reading it
unset($_SESSION["admin_login_success"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login | CareMatch AI</title>

    <!-- connecting the shared login CSS -->
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>

<body>

    <!-- this section holds the admin login form -->
    <main class="login-container">

        <section class="login-card">

            <!-- displaying the CareMatch AI branding -->
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

            <h1>Admin Login</h1>

            <p class="auth-description">
                Sign in to manage the CareMatch AI platform.
            </p>

            <!-- displaying a success message after admin registration -->
            <?php if (!empty($success)): ?>

                <p class="success-message" role="status">
                    <?php echo htmlspecialchars($success, ENT_QUOTES, "UTF-8"); ?>
                </p>

            <?php endif; ?>

            <!-- displaying an error if login fails -->
            <?php if (!empty($error)): ?>

                <p class="error-message" role="alert">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, "UTF-8"); ?>
                </p>

            <?php endif; ?>

            <!-- sending the login details to the admin login handler -->
            <form action="admin_login_handler.php" method="POST">

                <label for="email">Email Address</label>

                <input type="email" id="email" name="email" autocomplete="email" required>

                <label for="password">Password</label>

                <input type="password" id="password" name="password" autocomplete="off" required>

                <!-- button for showing and hiding the password -->
                <button type="button" id="togglePassword" class="password-toggle" aria-pressed="false">
                    Show Password
                </button>

                <!-- submitting the admin login form -->
                <button type="submit">
                    Sign In
                </button>

            </form>

        </section>

    </main>

    <!-- connecting the password toggle JavaScript -->
    <script src="../assets/js/auth.js" defer></script>

</body>
</html>
