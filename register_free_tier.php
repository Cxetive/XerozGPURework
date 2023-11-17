<?php

@include 'config.php';

session_start();

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = md5($_POST['password']);
   $cpass = md5($_POST['cpassword']);

   $select = " SELECT * FROM user_form WHERE email = '$email'";

   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){

        $error[] = "Email already exists. Please try again.";
        exit();

      }
     
   else{
        if ($pass != $cpass) {
             $error[] = "Passwords do not match. Please try again.";
                exit();
        }

        $sql = "INSERT INTO user_form(name, email, password, user_type, is_free_tier, expiration) VALUES('$name', '$email', '$pass', 'user', 1, '01/01/2050')";
    
        if(mysqli_query($conn, $sql)){
             $message = "Registration successful. Please login to continue.";
             // redirect to index.php
                header('location:index.php');
        }else{
             $error[] = "Registration failed. Please try again.";
        }
   }

};
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Xeroz.Tech GPU Panel</h3>
      <?php
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>
      <input type="text" name="name" required placeholder="enter your name">
      <input type="email" name="email" required placeholder="enter your email">
      <input type="password" name="password" required placeholder="enter your password">
        <input type="password" name="cpassword" required placeholder="confirm your password">
      <input type="submit" name="submit" value="register now" class="form-btn">
      <p>Buy a GPU Ticket in <a href="register_form.php">SHOP</a></p>
   </form>

</div>

</body>
</html>