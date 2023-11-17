<?php



@include 'config.php';



session_start();



if(isset($_POST['submit'])){



   $name = mysqli_real_escape_string($conn, $_POST['name']);

   $email = mysqli_real_escape_string($conn, $_POST['email']);

   $pass = md5($_POST['password']);

   $cpass = md5($_POST['cpassword']);

   $user_type = $_POST['user_type'];

   $select = " SELECT * FROM user_form WHERE email = '$email' && password = '$pass' ";

   $result = mysqli_query($conn, $select);

   if(mysqli_num_rows($result) > 0){

      $row = mysqli_fetch_array($result);
     
     $_SESSION['id'] = $row['id'];

      if($row['user_type'] == 'admin'){

         $_SESSION['admin_name'] = $row['name'];

         header('location:admin_page.php');

      }elseif($row['user_type'] == 'user'){

         $_SESSION['user_name'] = $row['name'];

         header('location:user_menu.php');


      }

   }else{

      $error[] = 'incorrect email or password!';
   }

};

?>



<!DOCTYPE html>

<html lang="en">
<!--Start of Tawk.to Script-->
<script type="text/javascript" defer>

function addBorderColor(input) {
   console.log(input)
 input.style.borderColor = "red";
}

function removeBorderColor(input) {
 input.style.borderColor = "";
}


var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/643aae784247f20fefebcf6b/1gu2jj2f4';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->

<head>

   <meta charset="UTF-8">

   <meta http-equiv="X-UA-Compatible" content="IE=edge">

   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <title>Login Form</title>



   <!-- custom css file link  -->

   <link rel="stylesheet" href="css/style.css">

</head>

<body>
<div class="picture-container">
   <img src="/img/background.jpg" alt="">
   <p>test</p>
</div>

<!-- <div class="container-picture">
   
</div> -->


<div class="form-container">
   


   <form action="" method="post">

      <h3>Login to gpu.xeroz.tech</h3>

      <?php

      if(isset($error)){

         foreach($error as $error){

            echo '<span class="error-msg">'.$error.'</span>';

         };

      };

      ?>
      <div class="form">
      
      <input type="email" id="inputField" onfocus="addBorderColor(this)" onblur="removeBorderColor(this)" required placeholder="Enter your email.">
      <input type="password" name="password" required placeholder="enter your password">

      <input type="submit" name="submit" value="login now" class="form-btn">

      <p>Buy a GPU Ticket in <a href="https://xeroz.tech/gpu-pc.html">SHOP</a></p>
      </div>

   </form>

  

      .
   </div>

</div>




</body>

</html>