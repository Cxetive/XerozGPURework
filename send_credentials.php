<?php
    ini_set('display_errors', '0'); 
@include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

session_start();

if(!isset($_SESSION['admin_name'])){
   header('location:index.php');
}

?>


<?php
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $timeslot_id = $_POST['timeslot_id'];
        $ip = $_POST['ip'];
        $password = $_POST['password'];
        include_once('config.php');
        $sql = "SELECT * FROM timeslots WHERE id = '$timeslot_id'";
        $result = mysqli_query($conn, $sql);
        $slot = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        if (empty($slot)) {
            $error_message = "Timeslot not found!";
            include_once('timeslot_manage.php');
            exit();
        }
        if ($slot["is_booked"] == 0) {
            $error_message = "Timeslot is not booked!";
            include_once('timeslot_manage.php');
            exit();
        }
        if ($slot["email_sent"] == 1) {
            $error_message = "Email has already been sent!";
            include_once('timeslot_manage.php');
            exit();
        }
        $sql = "UPDATE timeslots SET email_sent = 1 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $timeslot_id);
        mysqli_stmt_execute($stmt);
        $sql = "SELECT * FROM user_form WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $slot["assigned_to"]);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        $to = $user["email"];
        $subject = "Access to the timeslot";
        $message = "Dear Customer,<br><br>

We're delighted to announce that your Xeroz.Tech cloud gaming service is now active, and it's your turn to experience top-notch gaming excitement!<br><br>

Log In:<br><br>

In order to connect to your GPU-PC we advice to follow our guide, https://xeroz-1.gitbook.io/xeroz-knowledgebase/v/gpupc/first-steps<br><br>

IP: " . $ip . "<br>
Password: " . $password . "<br><br>

For any questions, technical challenges, or assistance, our dedicated support team is here to assist you. Please feel free to reach out to us at support@xeroz.tech, and we'll be swift in addressing your concerns.<br><br>

We are devoted to providing you with a premium gaming experience, and we highly value your input. Don't hesitate to share your thoughts and suggestions. Your gaming adventure is just about to unfold, and we're thrilled to be part of it.<br><br>

Thank you for choosing Xeroz.Tech as your cloud gaming service provider. Prepare for an immersive gaming experience filled with excitement and victories!<br><br>

Warm regards,<br><br>

Team Xeroz.Tech";

   $username = "gpu@xeroz.tech";
   $smtp_password = "DvJRAa2ZD";

   $smtp_host = "mail.xeroz.tech";
   $smtp_port = "587";


   $from = "gpu@xeroz.tech";
   $subject = "Xeroz GPU server access";

   $phpmailer = new PHPMailer(true);
   $phpmailer->isSMTP();
   $phpmailer->Host = $smtp_host;
   $phpmailer->SMTPAuth = true;
   $phpmailer->Username = $username;
   $phpmailer->Password = $smtp_password;
   $phpmailer->SMTPSecure = 'tls';
   $phpmailer->Port = $smtp_port;

   $phpmailer->setFrom($from, 'Xeroz.Tech');
   $phpmailer->addAddress($to);
   $phpmailer->Subject = $subject;
   $phpmailer->Body = $message;
   $phpmailer->isHTML(true);

   try {
         $phpmailer->send();
         $message = "Email sent successfully!";
         include_once('timeslot_manage.php');
         exit();
      } catch (Exception $e) {
         $error_message = "Email could not be sent. Mailer Error: {$phpmailer->ErrorInfo}";
         include_once('timeslot_manage.php');
         exit();
    }
   }

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>manage timeslot</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <style>
      .timeslot {
         display: flex;
         flex-wrap: wrap;
         justify-content: space-between;
         margin: 50px 0;
      }

      .slot {
         width: 48%;
         padding: 20px;
         border: 1px solid #ccc;
         border-radius: 5px;
         margin-bottom: 20px;
      }
   </style>

</head>
<body>
   
<div class="container">

   <div class="content">
      <h3>hi, <span>user</span></h3>
      <h1>welcome <span><?php echo $_SESSION['user_name'] ?></span></h1>
      
      <!-- hroizontal divs containing available timeslots to book -->
      <?php
         if (isset($message)) {
            echo "<p style='color: green; background-color: #1f364d; padding: 10px; border-radius: 5px;'>" . $message . "</p>";
         }
         if (isset($error_message)) {
            echo "<p style='color: red; background-color: #1f364d; padding: 10px; border-radius: 5px;'>" . $error_message . "</p>";
         }
      ?>
         <?php
            $today = date("d/m/Y");
            $timeslot_id = $_GET['timeslot_id'];
            $sql = "SELECT * FROM timeslots WHERE day = '$today' AND id = '$timeslot_id'";
            $result = mysqli_query($conn, $sql);
            $slot = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            if (empty($slot)) {
               $error_message = "Timeslot not found!";
                include_once('timeslot_manage.php');
                exit();
            }
            if ($slot["is_booked"] == 0) {
                $error_message = "Timeslot is not booked!";
                include_once('timeslot_manage.php');
                exit();
            }
            if ($slot["email_sent"] == 1) {
                $error_message = "Email has already been sent!";
                include_once('timeslot_manage.php');
                exit();
            }
            echo "<form action='' method='POST'>";
            echo "<input type='hidden' name='timeslot_id' value='" . $slot["id"] . "'>";
            echo "<input type='text' name='ip' placeholder='The server IP.' class='box'><br><br>";
            echo "<input type='password' name='password' placeholder='The server password.' class='box'><br><br>";
            echo "<input type='submit' name='send_credentials' value='Send Credentials to User' class='btn'><br><br>";
         ?>

      <a href="logout.php" class="btn">logout</a>
   </div>

</div>

</body>
</html>