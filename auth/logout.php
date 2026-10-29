
<?php
/*
Name: Hamed Zon
ID: C00259228
Date: 18/10/2026
Description: This file logs users out of CareMatch AI.
It works for workers, employers and administrators.
*/

// starting the current user session
session_start();

// preventing the browser from caching the logout response
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

// removing all stored session variables
// this includes the user ID, email and role
$_SESSION = [];

// checking if PHP is using session cookies
if (ini_get("session.use_cookies")) {

    // getting the current session cookie settings
    $params = session_get_cookie_params();

    // deleting the session cookie from the browser
    // setting an expiry date in the past removes the cookie
    setcookie(
        session_name(),
        "",
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// destroying the current session on the server
session_destroy();

// redirecting the user back to the CareMatch AI homepage
header("Location: ../index.php", true, 303);

// stopping the PHP script after redirecting
exit;
