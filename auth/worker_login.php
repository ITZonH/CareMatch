<!DOCTYPE html>
<!-- Name: Hamed Zon
ID: cOO259228
This is workers login screen -->

<html lang = "en">
<head>
    <meta charset =  "UTF-8">
    <meta name = "Viewport" content = " width = device-width, initial-scale = 1.0">
    <link rel = "stylesheet" href = "../assets/css/auth.css">
    <title> Worker Login | CareMatch AI</title>
</head>
<body>
    <main class = "login-container">
        <h1> Welcome to CareMatch AI </h1>
    
        <p> Sign in to your CareMacth AI  Worker account.</p>
        <form action = "Login_handler.php" method = "POST">
            <label for = "email"> Email </label>
            <input type = "email" id = "email" name = "email" required>

            <label for = "password"> Password </label>
            <input type = "password" id = "password" name = "password" required>
            <button type = "submit"> Sign In </button>
        </form>
    </main>
</body>
</html>
