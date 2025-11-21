<?php
// logout.php - Robust logout script
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Log the logout attempt
error_log("Logout initiated for user: " . (isset($_SESSION['EMAIL']) ? $_SESSION['EMAIL'] : 'Guest'));

// Clear all session variables
$_SESSION = array();

// Delete the session cookie if it exists
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to home page
$redirect_url = "home.php";

// Check if headers have already been sent
if (!headers_sent()) {
    header("Location: " . $redirect_url);
    exit();
} else {
    // If headers already sent, use JavaScript redirect as fallback
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Redirecting...</title>
        <script>
            window.location.href = '" . $redirect_url . "';
        </script>
    </head>
    <body>
        <p>If you are not redirected automatically, <a href='" . $redirect_url . "'>click here</a>.</p>
    </body>
    </html>";
    exit();
}
?>