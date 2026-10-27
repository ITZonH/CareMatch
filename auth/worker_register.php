<?php
session_start();
?>
<!DOCTYPE html>
<!-- Name: Hamed Zon
ID: C00259228
Date: 16/10/2026
Description: This is the workers registration page -->
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/auth.css">
    <title>Worker Registration | CareMatch AI</title>
</head>


<body>

    <!-- this is the main container for the registration page -->
    <main class="auth-container">

        <!-- This section holds the registration form -->
        <section class="auth-card">

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

            <!-- Heading and description for worker registration -->
            <h1>Create Your Worker Account</h1>

            <p>Join CareMatch AI and discover healthcare job opportunities.</p>

            <?php
                // checking if there is any registration error
                if (isset($_SESSION["register_error"])):
            ?>

                <!-- Displaying the error message to the user -->
                <p class="error-message" role="alert">
                    <?php
                        // displaying the error safely
                        echo htmlspecialchars(
                            $_SESSION["register_error"],
                            ENT_QUOTES,
                            "UTF-8"
                        );
                    ?>
                </p>

                <?php
                    // removing the error after displaying it
                    unset($_SESSION["register_error"]);
                ?>

            <?php endif; ?>

            <!-- This form collects the worker registration details -->
            <form class="register-form"
                  action="worker_register_handler.php"
                  method="POST">

                <!-- Getting the worker full name -->
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name"
                       name="full_name" autocomplete="name"
                       maxlength="100" required>

                <!-- Getting the worker email address -->
                <label for="email">Email Address</label>
                <input type="email" id="email"
                       name="email" autocomplete="email" required>

                <!-- Getting the worker password -->
                <label for="password">Password</label>
                <input type="password" id="password"
                       name="password" autocomplete="new-password"
                       minlength="12" required>

                <!-- Asking the worker to confirm the password -->
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password"
                       name="confirm_password" autocomplete="new-password"
                       minlength="12" required>

                <!-- Button to submit the registration form -->
                <button type="submit">Create Account</button>

            </form>

            <!-- Link for workers who already have an account -->
            <p class="auth-footer">
                Already have an account?
                <a href="worker_login.php">Sign in</a>
            </p>

        </section>
    </main>

    </body>
</html>