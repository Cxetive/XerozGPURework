<?php
    ini_set('display_errors', '0'); 
@include 'config.php';

session_start();

if(!isset($_SESSION['admin_name'])){
   header('location:login_form.php');
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
      <h3>hi, <span>admin</span></h3>
      <h1>welcome <span><?php echo $_SESSION['admin_name'] ?></span></h1>
      
      <!-- hroizontal divs containing available timeslots to book -->
      <?php
         if (isset($message)) {
            echo "<p style='color: green; background-color: #1f364d; padding: 10px; border-radius: 5px;'>" . $message . "</p>";
         }
         if (isset($error_message)) {
            echo "<p style='color: red; background-color: #1f364d; padding: 10px; border-radius: 5px;'>" . $error_message . "</p>";
         }
      ?>
      <div class="timeslot">
         <?php
            $today = date("d/m/Y");
            $sql = "SELECT * FROM timeslots WHERE day = '$today'";
            $result = mysqli_query($conn, $sql);
            $slots = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
            mysqli_close($conn);
            foreach ($slots as $slot) {
               echo "<div class='slot' style='background-color: " . $slot["color"] . ";'>";
               echo "<h3 style='color: black'>" . $slot["start_time"] . " - " . $slot["end_time"] . "</h3>";
               if ($slot["is_booked"] != 0) {
                    echo "<p style='color: black'>booked</p>";
                    $user_id = $slot["assigned_to"];
                    @include 'config.php';
                    $sql = "SELECT * FROM user_form WHERE id = '$user_id'";
                    $result = mysqli_query($conn, $sql);
                    $user = mysqli_fetch_assoc($result);
                    mysqli_free_result($result);
                    echo "<p style='color: black'>booked by: " . $user["email"] . "</p>";
                    echo "<a href='send_credentials.php?timeslot_id=" . $slot["id"] . "' class='btn'>Send Credentials to User</a>";
               } else {
                  echo "<p style='color: black'>Not booked</p>";
               }
               echo "</div>";
            }
         ?>
      </div>

      <a href="logout.php" class="btn">logout</a>
   </div>

</div>

</body>
</html>