<?php
// Name: Hamed Zon
// Id: C00259228
// Date: 28/10/2026
// Description: CareMatch AI administrator registration page

session_start();

$error = $_SESSION["admin_register_error"] ?? "";
unset($_SESSION["admin_register_error"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Registration | CareMatch AI</title>

    <link rel="stylesheet" href="../assets/css/auth.css">
</head>

<body>

<main class="login-container">
    <section class="login-card">

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

        <h1>Admin Registration</h1>

        <p class="auth-description">
            Create an administrator account for CareMatch AI.
        </p>

        <?php if ($error): ?>
            <p class="error-message" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, "UTF-8") ?>
            </p>
        <?php endif; ?>

        <form action="admin_register_handler.php" method="POST">

            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                minlength="12"
                autocomplete="new-password"
                required
            >

            <label for="confirm_password">Confirm Password</label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                minlength="12"
                autocomplete="new-password"
                required
            >


            <button type="submit">
                Register Admin
            </button>

        </form>

        <p>
            Already registered?
            <a href="admin_login.php">Admin Login</a>
        </p>

    </section>
</main>

</body>
</html>
