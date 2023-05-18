<?php
session_start();

require_once '//Applications/XAMPP/xamppfiles/htdocs/PHPMailer/src/PHPMailer.php';

// Check if the email or phone value is set in the session
if (!isset($_SESSION['email']) && !isset($_SESSION['phone'])) {
  // If the session variable for the email or phone is not set, redirect the user to the login page
  header("Location: login.php");
  exit();
}

// Check if the OTP has been submitted
if (isset($_POST['otp'])) {
  // Sanitize the OTP input
  $otp_input = mysqli_real_escape_string($conn, $_POST['otp']);

  // Check if the input code matches the generated code
  if ($otp_input == $_SESSION['otp']) {
    // OTP is correct, redirect to dashboard.php
    header("Location: dashboard.php");
    exit();
  } else {
    // OTP is incorrect, display error message
    $error_message = "Please enter a valid OTP.";
  }
}

// Check if the OTP code has already been sent
if (!isset($_SESSION['otp'])) {
  // Generate a random 4-digit code
  $otp_code = rand(1000, 9999);

  if (isset($_SESSION['email'])) {
    // Send the code to the email in the session
    $to = $_SESSION['email'];
    $subject = 'OTP Code';
    $message = 'Your OTP Code is: ' . $otp_code;
    $headers = 'From: sender email';
    $sent = mail($to, $subject, $message, $headers);
    $label_text = 'Email:';
    $input_type = 'email';
    $input_placeholder = 'example@example.com';
  } elseif (isset($_SESSION['phone'])) {
    // Send the code to the phone number in the session (requires a third-party SMS API)
    // ...
    $label_text = 'Phone Number:';
    $input_type = 'tel';
    $input_placeholder = '0123456789';
  }

  if ($sent) {
    // OTP code sent successfully, store it in the session
    $_SESSION['otp'] = $otp_code;
    $info_message = "OTP code has been sent to your " . (isset($_SESSION['email']) ? "email" : "phone number") . ".";
  } else {
    // OTP code not sent, display error message
    $error_message = "Failed to send OTP code. Please try again later.";
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>OTP</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="otp-box">
    <h1>OTP</h1>
    <?php if (isset($_SESSION['email'])) { ?>
      <p>Please enter the code sent to <?php echo $_SESSION['email']; ?></p>
    <?php } elseif (isset($_SESSION['phone'])) { ?>
      <p>Please enter the code sent to <?php echo $_SESSION['phone']; ?></p>
    <?php } ?>
    <?php if (isset($info_message)) { ?>
      <div class="info">
        <p><?php echo $info_message; ?></p>
      </div>
    <?php } ?>
    <form id="otp-form" action="" method="POST">
      <?php if (isset($_SESSION['email'])) { ?>
        <label for="otp">Email:</label>
        <input type="email" id="otp" name="otp" placeholder="Enter OTP" required>
      <?php } elseif (isset($_SESSION['phone'])) { ?>
        <label for="otp">Phone Number:</label>
        <input type="tel" id="otp" name="otp" placeholder="Enter OTP" required>
      <?php } ?>
      <button type="submit">Submit</button>
    </form>
    <?php if (isset($error_message)) { ?>
      <div class="alert">
        <p style="color: red;"><?php echo $error_message; ?></p>
      </div>
    <?php } ?>
  </div>
</body>
</html>