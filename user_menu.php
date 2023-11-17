<?php

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
   <title>user menu</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="container">

   <div class="content">
      <h3>hi, <span>user</span></h3>
      <h1>welcome <span><?php echo $_SESSION['user_name'] ?></span></h1>
      <p>User Panel</p>
      <a href="user_page.php" class="btn">Timeslot Booking</a>
      <a href="see_logins.php" class="btn">My current Session</a>
      <a href="mailto:support@xeroz.tech" class="btn">Support</a>
      <a href="https://xeroz-1.gitbook.io/xeroz-knowledgebase/v/gpupc/first-steps" class="btn">Documentation</a>
      <a href="logout.php" class="btn">logout</a>
   </div>

</div>

</body>
</html>