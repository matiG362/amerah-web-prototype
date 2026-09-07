<?php
// Suppress warnings so they never break the JSON output
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// 1. Sanitize input fields
$name     = filter_var(trim($_POST['name'] ?? ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$phone    = filter_var(trim($_POST['phone'] ?? ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$guests   = filter_var(trim($_POST['guests'] ?? ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$dates    = filter_var(trim($_POST['dates'] ?? ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$room     = filter_var(trim($_POST['room-interest'] ?? ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$message  = filter_var(trim($_POST['message'] ?? ''), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

// 2. Validate required inputs
if (empty($name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please provide a valid name and email address.']);
    exit;
}

// 3. Email details
$to      = 'reservations@amerahhotels.com';
$subject = 'New Stay Inquiry: ' . $name . ' (' . ($room ?: 'General Inquiry') . ')';

// 4. HTML Email Body
$emailBody = "
<!DOCTYPE html>
<html>
<head>
<style>
  body { font-family: Arial, sans-serif; color: #262220; line-height: 1.6; }
  .container { max-width: 600px; margin: 20px auto; border: 1px solid #E7DCC7; border-radius: 4px; overflow: hidden; }
  .header { background-color: #1E3A38; color: #F6F1E7; padding: 24px; text-align: center; }
  .header h2 { margin: 0; font-size: 22px; font-weight: normal; }
  .content { padding: 24px; background-color: #FAFAF8; }
  .field-row { margin-bottom: 14px; border-bottom: 1px solid #EFE8D9; padding-bottom: 8px; }
  .label { font-size: 11px; text-transform: uppercase; color: #BD5B39; font-weight: bold; letter-spacing: 0.08em; }
  .value { font-size: 15px; margin-top: 3px; }
  .footer { background-color: #EFE8D9; color: #5B554C; font-size: 12px; text-align: center; padding: 12px; }
</style>
</head>
<body>
<div class='container'>
  <div class='header'>
    <h2>Amerah Palace — New Guest Inquiry</h2>
  </div>
  <div class='content'>
    <div class='field-row'><div class='label'>Guest Name</div><div class='value'>{$name}</div></div>
    <div class='field-row'><div class='label'>Email Address</div><div class='value'><a href='mailto:{$email}'>{$email}</a></div></div>
    <div class='field-row'><div class='label'>Phone Number</div><div class='value'>" . ($phone ?: 'Not provided') . "</div></div>
    <div class='field-row'><div class='label'>Target Dates</div><div class='value'>" . ($dates ?: 'Flexible / Not specified') . "</div></div>
    <div class='field-row'><div class='label'>Party Size</div><div class='value'>{$guests}</div></div>
    <div class='field-row'><div class='label'>Room / Venue Interest</div><div class='value'>{$room}</div></div>
    <div class='field-row'><div class='label'>Guest Notes & Preferences</div><div class='value'>" . nl2br($message ?: 'No additional notes.') . "</div></div>
  </div>
  <div class='footer'>
    Received via website inquiry form on amerahhotels.com
  </div>
</div>
</body>
</html>
";

// 5. Headers
$headers   = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/html; charset=UTF-8';
$headers[] = 'From: Amerah Palace Website <reservations@amerahhotels.com>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'X-Mailer: PHP/' . phpversion();

// 6. Safe Mail Dispatch
// Try sending with the envelope flag first; fall back to standard mail() if restricted
$sent = @mail($to, $subject, $emailBody, implode("\r\n", $headers), "-freservations@amerahhotels.com");
if (!$sent) {
    $sent = @mail($to, $subject, $emailBody, implode("\r\n", $headers));
}

if ($sent) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'The server was unable to dispatch the message.']);
}
?>