<?php

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "config.php";
require_once "PHPMailer/Exception.php";
require_once "PHPMailer/PHPMailer.php";
require_once "PHPMailer/SMTP.php";


if (isset($_POST["submit"])) {
  $email_id = trim($_POST["email"]);

  $sql = "SELECT * FROM users WHERE email='" . $email_id . "';";
  $result = $mysqli->query($sql);

  if ($row = $result->fetch_assoc()) {
    $id = $row["id"];
    $sql = "SELECT * FROM tokens WHERE id=" . $id;
    $result = $mysqli->query($sql);
    if ($row = $result->fetch_assoc()) {
?>
      <script>
        alert("Token is already generated. Please check your registered email");
      </script>
      <?php
    } else {
      $token = bin2hex(openssl_random_pseudo_bytes(8));
      $mail = new PHPMailer(TRUE);
      try {
        $mail->setFrom('lucifershrike@gmail.com', 'Exaam Portal');
        $mail->addAddress($email_id);
        $mail->Subject = 'Exaam Portal Reset Password Link';
        $mail->Body = "Hello, $email_id click here to reset your password http://localhost/online_exam/reset-password.php?email=" . $email_id . "&token=" . $token;

        $mail->isSMTP();
        $mail->SMTPDebug = 2;
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = TRUE;
        $mail->SMTPSecure = 'tls';
        $mail->Username = 'lucifershrike@gmail.com';
        $mail->Password = 'LUCIFER@1207';
        $mail->Port = 587;
        $mail->SMTPOptions = array(
          'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
          )
        );
        $mail->send();
        $sql = "INSERT INTO tokens(id, email, token) VALUES($id, '$email_id', '$token');";
        if ($mysqli->query($sql)) {
      ?>
          <script>
            alert("Reset Password link sent to your registerd email. Link is valid only once.")
            location.href = "index.php";
          </script>
    <?php
        } else {
          echo "Failed to update database. Please try again.";
          echo $mysqli->error;
        }
      } catch (Exception $e) {
        echo $e->errorMessage();
      } catch (Exception $e) {
        echo $e->getMessage();
      }
    }
  } else {
    ?>
    <script>
      alert("Provided Email is not registerd with us.");
    </script>
<?php
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot password</title>
  <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" integrity="sha512-1PKOgIY59xJ8Co8+NE6FZ+LOAZKjy+KY8iq0G4B3CyeY6wYHN3yt9PW0XpSriVlkMXe40PTKnXrLnZ9+fkDaog==" crossorigin="anonymous" />
  <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
  <style type="text/css">
    html {
      width: 100%;
      height: 100%;
    }

    body {
      background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(img2.jpeg);
      background-size: cover;
      background-position: 75%;
      background-attachment: fixed;
      font-family: 'Quicksand', serif;
      color: white;
    }

    .frm {
      margin-top: 40%;
      padding: 2rem 2rem;
      box-shadow: 0px 0px 15px 4px rgba(0, 0, 0, 0.75);
      border-radius: 20px;
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="row d-flex justify-content-center">
      <div class="col-sm-10 col-md-7 col-lg-5">
        <form class="frm" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
          <h1 class="text-center pb-3">Reset Password</h1>
          <div class="form-group">
            <label for="email">Email ID</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
              </div>
              <input type="email" name="email" id="email" class="form-control rounded-right" placeholder="Enter Email ID" required>
            </div>
            <span class="text-danger" id="email_err"></span>
          </div>

          <div class="form-group text-center">
            <button type="submit" name="submit" class="btn btn-primary btn-block">Send Link</button>
          </div>

          <div class="form-group text-center">
            <a href="index.php">Log In</a>
          </div>

        </form>
      </div>
    </div>
  </div>
  </div>
</body>

</html>