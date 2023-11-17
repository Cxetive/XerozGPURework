<?php
    ini_set('display_errors', '0'); 
@include 'config.php';

session_start();

if(!isset($_SESSION['user_name'])){
   header('location:login_form.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>user page</title>

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
         $sql = "SELECT is_free_tier FROM user_form WHERE id = " . $_SESSION['id'];
         $result = mysqli_query($conn, $sql);
         $is_free_tier = mysqli_fetch_assoc($result);
         mysqli_free_result($result);
         if ($is_free_tier["is_free_tier"] == 1) {
            $is_free_tier = true;
         } else {
            $is_free_tier = false;
         }
      ?>
      <?php
      if (!$is_free_tier){
         $timeslots_table = "timeslots";
         $current_creds_id = 1;
      } else {
            $timeslots_table = "free_tier_timeslots";
            $current_creds_id = 2;
      }

      // check if the user has a timeslot booked for today and the timeslot started already but will end in the future
        $today = date("d/m/Y"); 
        $sql = "SELECT * FROM $timeslots_table WHERE day = '$today' AND assigned_to = " . $_SESSION['id'];
        $result = mysqli_query($conn, $sql);
        $slot = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        if (empty($slot))
        {
            echo "<p style='color: red; background-color: #1f364d; padding: 10px; border-radius: 5px;'>You currently don't have an active timeslot.</p>";
        } else {
            // check if start time is in the past and end time is in the future
            $start_time = $slot["start_time"];
            $end_time = $slot["end_time"];
            include_once("functions/utils_functions.php");
            if (!is_in_the_past($start_time) || !is_in_the_future($end_time)) {
                echo "<p style='color: red; background-color: #1f364d; padding: 10px; border-radius: 5px;'>You currently don't have an active timeslot.</p>";
                exit();
            } 
            // check if previous slot has server_reset to 1
            $prev_slot_id = $slot["id"] - 1;
            $sql = "SELECT * FROM $timeslots_table WHERE id = '$prev_slot_id'";
            $result = mysqli_query($conn, $sql);
            $previous_slot = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            if ($previous_slot["server_reset"] == 0) {
                echo "<p style='color: red; background-color: #1f364d; padding: 10px; border-radius: 5px;'>You currently don't have an active timeslot.</p>";
                exit();
            }

                $sql = "SELECT * FROM current_credentials WHERE id = '$current_creds_id'";
                $result = mysqli_query($conn, $sql);
                $current_creds = mysqli_fetch_assoc($result);

                $ip = $current_creds["ip"];
                $password = $current_creds["password"];

                echo "<p style='color: green; background-color: #1f364d; padding: 10px; border-radius: 5px;'>You currently have an active timeslot. You can connect to the server with the following credentials:</p>";
                echo "<p style='color: green; background-color: #1f364d; padding: 10px; border-radius: 5px;'>IP: " . $ip . "</p>";
                echo "<p style='color: green; background-color: #1f364d; padding: 10px; border-radius: 5px;'>Username: Administrator</p>";
                echo "<p style='color: green; background-color: #1f364d; padding: 10px; border-radius: 5px;'>Password: " . $password . "</p>";

                // show a timer for the remaining time
                $end_time = $slot["end_time"];
                $end_time = explode(":", $end_time);
                $end_time = (int) $end_time[0] * 60 + (int) $end_time[1];
                $current_time = date("H:i");
                $current_time = explode(":", $current_time);
                $current_time = (int) $current_time[0] * 60 + (int) $current_time[1];
                $remaining_time = $end_time - $current_time;
                echo "<p style='color: green; background-color: #1f364d; padding: 10px; border-radius: 5px;'>Remaining time: " . $remaining_time . " minutes</p>";
        }
      ?>
      <a href="user_menu.php" class="btn">Back to User Menu</a>
      <a href="logout.php" class="btn">logout</a>
   </div>

</div>

</body>
</html>