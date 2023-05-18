<?php
// Set database connection variables
$hostname = "127.0.0.1";
$username = "root";
$password = "David910139";
$databasename = "FuseGap_Login";

// Connect to the database
$conn = mysqli_connect($hostname, $username, $password, $databasename);

// Check if the connection was successful
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}


// Check if the email or phone number has been submitted
if (isset($_POST['login_type'])) {
    session_start();

    // Sanitize the input
    $login_type = mysqli_real_escape_string($conn, $_POST['login_type']);
    $login_value = mysqli_real_escape_string($conn, $_POST['login_value']);
  
    // Query the database to check if the email or phone number exists
    if ($login_type == 'phone') {
      $sql = "SELECT * FROM users WHERE user_phone = '$login_value'";
      $session_variable = 'phone';
      $label_text = 'Phone Number:';
      $input_type = 'tel';
      $input_placeholder = '0123456789';
    } else {
      $sql = "SELECT * FROM users WHERE user_email = '$login_value'";
      $session_variable = 'email';
      $label_text = 'Email:';
      $input_type = 'email';
      $input_placeholder = 'example@example.com';
    }
    $result = mysqli_query($conn, $sql);
  
    if (mysqli_num_rows($result) > 0) {
      $_SESSION[$session_variable] = $login_value;
      header("Location: otp.php");
      exit();
    } else {
      $error_message = "The " . ($login_type == 'phone' ? "phone number" : "email") . " does not exist.";
    }
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login Page</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="login-box">
    <h1>Login</h1>
    <form id="login-form" action="" method="POST">
      <div>
        <input type="radio" id="email-radio" name="login_type" value="email" checked>
        <label for="email-radio">Login with Email</label>
      </div>
      <div>
        <input type="radio" id="phone-radio" name="login_type" value="phone">
        <label for="phone-radio">Login with Phone Number</label>
      </div>
      <label for="login_value" id="login_label"></label>
      <input type="email" id="login_value" name="login_value" required>
      <button type="submit">Log In</button>
    </form>
    <?php if (isset($error_message)) { ?>
      <div class="alert">
        <p style="color: red;"><?php echo $error_message; ?></p>
      </div>
    <?php } ?>
  </div>
  
  <script>
   
    const emailRadio = document.getElementById('email-radio');
    const phoneRadio = document.getElementById('phone-radio');
    const loginValueInput = document.getElementById('login_value');
    const loginLabel = document.getElementById('login_label');
    
    emailRadio.addEventListener('click', () => {
      loginValueInput.type = 'email';
      loginValueInput.placeholder = 'example@example.com';
      loginLabel.innerHTML = "Email:";
    });
    
    phoneRadio.addEventListener('click', () => {
      loginValueInput.type = 'tel';
      loginValueInput.placeholder = '0123456789';
      loginLabel.innerHTML = "Phone Number:";
    });
  </script>
</body>
</html>