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
         echo "<p style='color: green; background-color: #1f364d; padding: 10px; border-radius: 5px;'> Dear customer, please note that the timeslots are temporarily lasting 3 hours to give everyone a chance to use our service. We are actively working on new solutions to improve our service. Thank you for your patience. </p>";
         $sql = "SELECT expiration FROM user_form WHERE id = " . $_SESSION['id'];
         $result = mysqli_query($conn, $sql);
         $expiration = mysqli_fetch_assoc($result);
         mysqli_free_result($result);
         echo "<p style='color: green; background-color: #1f364d; padding: 10px; border-radius: 5px;'>Your subscription will expire on " . $expiration["expiration"] . "</p>";
      } else {
         echo "<p style='color: green; background-color: #1f364d; padding: 10px; border-radius: 5px;'>Dear customer, please note that you are currently using the free tier. You can only book a timeslot for today. If you want to book a timeslot for tomorrow, please upgrade your subscription.</p>";
      }
      ?>
      <div class="timeslot">
         <?php
            if ($is_free_tier) {
               $timeslots_table = "free_tier_timeslots";
            } else {
               $timeslots_table = "timeslots";
            }
            $today = date("d/m/Y");
            $tomorrow = date("d/m/Y", strtotime("tomorrow"));
            $sql = "SELECT * FROM $timeslots_table WHERE day = '$today'";
            $result = mysqli_query($conn, $sql);
            $slots = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            // add a slot from tomorrow
            $sql = "SELECT * FROM $timeslots_table WHERE day = '$tomorrow'";
            $result = mysqli_query($conn, $sql);
            $tomorrow_slots = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_close($conn);
            $slots = array_merge($slots, $tomorrow_slots);
            foreach ($slots as $slot) {
               echo "<div class='slot' style='background-color: " . $slot["color"] . ";'>";
               echo "<h3 style='color: black';>" . $slot["start_time"] . " - " . $slot["end_time"] . "</h3>";
               if ($slot["is_booked"] != 0) {
                  if ($slot["assigned_to"] == $_SESSION['id']) {
                     echo "<p style='color: black';>booked by you</p>";
                     echo "<a href='cancel_booking.php?slot_id=" . $slot["id"] . "' class='btn'>cancel</a>";
                  } else {
                     echo "<p style='color: black';>booked by another user</p>";
                  }
               } else {
                  echo "<a href='book_slot.php?slot_id=" . $slot["id"] . "' class='btn'>book</a>";
               }
               echo "</div>";
            }
         ?>
      </div>
         
      <a href="user_menu.php" class="btn">Back to User Menu</a>
      <a href="logout.php" class="btn">logout</a>
   </div>

</div>

</body>
</html>