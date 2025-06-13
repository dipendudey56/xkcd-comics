<?php

/**
 * Generate a 6-digit numeric verification code.
 */
function generateVerificationCode(): string {
    return str_pad(strval(rand(0, 999999)), 6, '0', STR_PAD_LEFT);
}

/**
 * Send a verification code to an email.
 */
function sendVerificationEmail(string $email, string $code): bool {
    $subject = "Your Verification Code";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";
    $body = "<p>Your verification code is: <strong>$code</strong></p>";
    return mail($email, $subject, $body, $headers);
}

/**
 * Send an unsubscribe verification code.
 */
function sendUnsubscribeVerificationEmail(string $email, string $code): bool {
    $subject = "Confirm Un-subscription";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";
    $body = "<p>To confirm un-subscription, use this code: <strong>$code</strong></p>";
    return mail($email, $subject, $body, $headers);
}

/**
 * Register an email by storing it in a file.
 */
function registerEmail(string $email): bool {
  $file = __DIR__ . '/registered_emails.txt';
      $emails = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

    if (!in_array($email, $emails)) {
        file_put_contents($file, $email . PHP_EOL, FILE_APPEND);
    }

    return true;
}

/**
 * Unsubscribe an email by removing it from the list.
 */
function unsubscribeEmail(string $email): bool {
  $file = __DIR__ . '/registered_emails.txt';
    if (!file_exists($file)) return false;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $emails = array_filter($emails, fn($e) => trim($e) !== $email);
    file_put_contents($file, implode(PHP_EOL, $emails) . PHP_EOL);

    return true;
}

/**
 * Fetch random XKCD comic and format data as HTML.
 */
function fetchAndFormatXKCDData(): string {
     $latestData = json_decode(file_get_contents("https://xkcd.com/info.0.json"), true);
    $max = $latestData['num'];
    $randomId = rand(1, $max);

    $comicData = json_decode(@file_get_contents("https://xkcd.com/$randomId/info.0.json"), true);

    if (!$comicData) {
        return "<p>Failed to fetch XKCD comic.</p>";
    }

    $img = htmlspecialchars($comicData['img']);
    return "<h2>XKCD Comic</h2>
            <img src=\"$img\" alt=\"XKCD Comic\">
            <p><a href=\"#\" id=\"unsubscribe-button\">Unsubscribe</a></p>";
}

/**
 * Send the formatted XKCD updates to registered emails.
 */
function sendXKCDUpdatesToSubscribers(): void {
  $file = __DIR__ . '/registered_emails.txt';
     if (!file_exists($file)) return;

    $emails = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $content = fetchAndFormatXKCDData();
    $subject = "Your XKCD Comic";
    $headers = "From: no-reply@example.com\r\n";
    $headers .= "Content-Type: text/html\r\n";

    foreach ($emails as $email) {
        $content = str_replace("#","https://github.com/dipendudey56/xkcd-comics/src/unsubscribe.php?email=$email", $content);
        mail($email, $subject, $content, $headers);
    }
}
