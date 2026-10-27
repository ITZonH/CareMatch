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
    
    <?php
        // this display error message when wrong details is enter
        if (isset($_SESSION["register_error"])) {

            echo '<p class="error-message">'
                . htmlspecialchars($_SESSION["register_error"], ENT_QUOTES, 'UTF-8')
                . '</p>';

            unset($_SESSION["register_error"]);
        }
    ?>

    <h1>Create Your Worker Account</h1>
    <p>Join CareMatch AI and discover healthcare job opportunities.</p>

    <form class = "register-form" action="register_handler.php" method = "POST">
        <label for="full_name">Full Name</label>
        <input type="text" name="full_name" id="full_name" autocomplete = "name" required>

        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" autocomplete = "email" require>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" autocomplete = "new_password" require>

        <label for="confirm_password"> Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" autocomplete = "new_password" require>

        <button type="submit">Create Account</button>
    </form>
    <p>
        Already have an account?
        <a href="worker_login.php">Login</a>
    </p>
</body>
</html>