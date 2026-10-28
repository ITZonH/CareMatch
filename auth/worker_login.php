<?php
// Name: Hamed Zon
// ID: cOO259228
// date: 11/10/2026
// This is workers login screen


//this opens the browser's php session 
session_start();
$error = $_SESSION["login_error"] ?? "";
unset($_SESSION["login_error"]);
?>
<!DOCTYPE html>

<html lang = "en">
<head>
    <meta charset =  "UTF-8">
    <meta name = "Viewport" content = " width = device-width, initial-scale = 1.0">
    <link rel = "stylesheet" href = "../assets/css/auth.css">
    <title> Worker Login | CareMatch AI</title>
</head>

<body>

    <!-- This is the main container for the worker login page -->
    <main class="login-container">

        <!-- This section holds the login form -->
        <section class="login-card">

            <!-- Displaying the CareMatch AI name and logo -->
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

            <!-- Heading and description for worker login -->
            <h1>Welcome Back</h1>

            <p>Sign in to your healthcare worker account.</p>

            <?php
                // checking if registration was successful
                if (isset($_SESSION["register_success"])):
            ?>

                <!-- Displaying the success message -->
                <p class="success-message" role="status">
                    <?php
                        echo htmlspecialchars($_SESSION["register_success"], ENT_QUOTES, "UTF-8");

                        // removing the message after displaying it
                        unset($_SESSION["register_success"]);
                    ?>
                </p>

            <?php endif; ?>

            <?php
                // checking if there is any login error
                if (!empty($error)):
            ?>

                <!-- Displaying the login error -->
                <p class="error-message" role="alert">
                    <?php
                        echo htmlspecialchars($error, ENT_QUOTES, "UTF-8");
                    ?>
                </p>

            <?php endif; ?>

            <!-- This form collects the worker login details -->
            <form action="worker_login_handler.php" method="POST">

                <!-- Getting the worker email address -->
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" autocomplete="email" required>

                <!-- Getting the worker password -->
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>

                <!-- Button to show or hide the password -->
                <button type="button" id="togglePassword" class="password-toggle" aria-pressed="false">
                    Show Password
                </button>

                <!-- Button to submit the login form -->
                <button type="submit">Sign In</button>

            </form>

            <!-- Link for workers who do not have an account -->
            <p class="auth-footer">
                Don't have an account?
                <a href="worker_register.php">Create an account</a>
            </p>

        </section>
    </main>

    <!-- Connecting the JavaScript for showing the password -->
    <script src="../assets/js/auth.js" defer></script>

</body>

</html>
