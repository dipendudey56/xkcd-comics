<?php
require_once 'functions.php';

session_start();

$message = '';
$prefilledEmail = strtolower(trim($_GET['email']));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['unsubscribe_email'])) {
        $email = trim($_POST['unsubscribe_email']);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $code = generateVerificationCode();
            $_SESSION['unsubscribe_email'] = $email;
            $_SESSION['unsubscribe_code'] = $code;

            if (sendUnsubscribeVerificationEmail($email, $code)) {
                $message = "Unsubscribe code sent to $email";
            } else {
                $message = "Failed to send email.";
            }
        } else {
            $message = "Invalid email address.";
        }
    }

    if (isset($_POST['verification_code'])) {
        if (isset($_SESSION['unsubscribe_email'], $_SESSION['unsubscribe_code']) &&
            $_POST['verification_code'] === $_SESSION['unsubscribe_code']
        ) {
            unsubscribeEmail($_SESSION['unsubscribe_email']);
            $message = "Successfully unsubscribed.";
            unset($_SESSION['unsubscribe_email'], $_SESSION['unsubscribe_code']);
            $prefilledEmail = '';
        } else {
            $message = "Invalid code.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Unsubscribe</title></head>
<body>
<h2>Unsubscribe from XKCD Comics</h2>
<p><?= htmlspecialchars($message) ?></p>

<form method="POST">
    <label>Email:</label><br>
    <input type="email" name="unsubscribe_email" value="<?=htmlspecialchars($prefilledEmail)?>" readonly><br>
    <button id="submit-unsubscribe">Unsubscribe</button>
</form>

<br>

<form method="POST">
    <label>Verification Code:</label><br>
    <input type="text" name="verification_code" maxlength="6" required><br>
    <button id="submit-verification">Verify</button>
</form>

<a href="https://github.com/dipendudey56/xkcd-comics/src/index.php"> Back to Home Page </a>
</body>
</html>
