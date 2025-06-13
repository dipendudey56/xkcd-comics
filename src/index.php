<?php
require_once 'functions.php';

session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            $_SESSION['verify_email'] = $email;
            $_SESSION['verify_code'] = $code;

            if (sendVerificationEmail($email, $code)) {
                $message = "Verification code sent to $email";
            } else {
                $message = "Failed to send verification email.";
            }
        } else {
            $message = "Invalid email address.";
        }
    }

    if (isset($_POST['verification_code'])) {
        if (isset($_SESSION['verify_code'], $_SESSION['verify_email']) &&
            $_POST['verification_code'] === $_SESSION['verify_code']
        ) {
            registerEmail($_SESSION['verify_email']);
            $message = "Email registered successfully.";
            unset($_SESSION['verify_email'], $_SESSION['verify_code']);
        } else {
            $message = "Invalid verification code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Email Subscription</title></head>
<body>
<h2>Subscribe to XKCD Comics</h2>
<p><?= htmlspecialchars($message) ?></p>

<form method="POST">
    <label>Email:</label><br>
    <input type="email" name="email" required><br>
    <button id="submit-email">Submit</button>
</form>

<br>

<form method="POST">
    <label>Verification Code:</label><br>
    <input type="text" name="verification_code" maxlength="6" required><br>
    <button id="submit-verification">Verify</button>
</form>
</body>
</html>

