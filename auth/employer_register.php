<?php
// Name : Hamed
// ID :C00259228
// Date: 18/10/2026
//Decription: this file is employer registration file
session_start();
$error = $_SESSION["employer_register_error"] ?? "";

unset($_SESSION["employer_register_error"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Employer Registration | CareMatch AI</title>

    <link rel="stylesheet" href="../assets/css/auth.css">
</head>


<body>

    <!-- this is the main container for employer registration -->
    <main class="auth-container">

        <!-- this section holds the employer registration form -->
        <section class="auth-card">

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

            <!-- heading for the employer registration page -->
            <h1>Create Your Employer Account</h1>

            <!-- explaining what the employer account is for -->
            <p class="auth-description">
                Register your organisation and connect with healthcare professionals.
            </p>

            <!-- checking if there is any registration error -->
            <?php if (!empty($error)): ?>

                <!-- displaying the error message safely -->
                <p class="error-message" role="alert">
                    <?php
                        echo htmlspecialchars($error,ENT_QUOTES,"UTF-8");
                    ?>
                </p>

            <?php endif; ?>

            <!-- form for collecting the employer registration details -->
            <form class="register-form" action="employer_register_handler.php" method="POST">

                <!-- getting the organisation name -->
                <label for="organisation_name">Organisation Name</label>

                <input type="text" id="organisation_name" name="organisation_name" maxlength="150" required>

                <!-- getting the contact person's name -->
                <label for="contact_name">Contact Person's Name</label>
                <input type="text" id="contact_name" name="contact_name" maxlength="100" required>

                <!-- getting the employer email address -->
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" autocomplete="email" required>

                <!-- getting the employer password -->
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="new-password" minlength="12" required>

                <!-- asking the employer to confirm the password -->
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" minlength="12" required>

                <!-- button for creating the employer account -->
                <button type="submit">
                    Create Employer Account
                </button>

            </form>

            <!-- link for employers who already have an account -->
            <p class="auth-footer">
                Already have an employer account?
                <a href="employer_login.php">Sign in</a>
            </p>

        </section>

    </main>

</body>

</html>