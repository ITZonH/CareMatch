<?php
// Name: Hamed Zon
// ID: cOO259228
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
    <main class = "login-container">
        <section class = "login-card">
            <h1> Welcome to CareMatch AI </h1>
        
            <p> Sign in to your Health Care Worker account.</p>
            <?php if(!empty($error)):?>
                <p class = "error-message" role = "alert">
                    <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8');?>
            </p>
            <?php endif; ?>
            <form action = "Login_handler.php" method = "POST">
                <label for = "email"> Email </label>
                <input type = "email" id = "email" name = "email" required>

                <label for = "password"> Password </label>
                <input type = "password" id = "password" name = "password" required>
                <button type = "button" id = "togglePassword" class = "password-toggle"  aria-press = "false">
                    Show Password
                </button>

                <button type = "submit"> Sign In </button>
            </form>
        </section>
    </main>
    <script src = "../assets/js/auth.js" defer></script>
</body>
</html>
