<?php
session_start();

require_once '//Applications/XAMPP/xamppfiles/htdocs/PHPMailer/src/PHPMailer.php';

if (!isset($_SESSION['email']) && !isset($_SESSION['phone'])) {
  header("Location: login.php");
  exit();
}

if (isset($_POST['otp'])) {
  $otp_input = mysqli_real_escape_string($conn, $_POST['otp']);

  if ($otp_input == $_SESSION['otp']) {
    header("Location: dashboard.php");
    exit();
  } else {
    $error_message = "Please enter a valid OTP.";
  }
}

if (!isset($_SESSION['otp'])) {
  $otp_code = rand(1000, 9999);

  if (isset($_SESSION['email'])) {
    $to = $_SESSION['email'];
    $subject = 'OTP Code';
    $message = 'Your OTP Code is: ' . $otp_code;
    $headers = 'From: sender email';
    $sent = mail($to, $subject, $message, $headers);
    $label_text = 'Email:';
    $input_type = 'email';
    $input_placeholder = 'example@example.com';
  } elseif (isset($_SESSION['phone'])) {
    $label_text = 'Phone Number:';
    $input_type = 'tel';
    $input_placeholder = '0123456789';
  }

  if ($sent) {
    $_SESSION['otp'] = $otp_code;
    $info_message = "OTP code has been sent to your " . (isset($_SESSION['email']) ? "email" : "phone number") . ".";
  } else {
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