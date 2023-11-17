<?php

@include 'config.php';

session_start();

if(!isset($_SESSION['admin_name'])){
   header('location:index.php');
}

if(isset($_POST['submit'])){

    $id = $_POST['id'];
    $expiration = $_POST['expiration'];


    $select = " SELECT * FROM user_form WHERE id = '$id' ";

    $result = mysqli_query($conn, $select);

    if(mysqli_num_rows($result) > 0){

        $update = "UPDATE user_form SET expiration = '$expiration' WHERE id = '$id'";
        mysqli_query($conn, $update);
        header('location:users.php');

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register form</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
    <?php
        $user_id = $_GET['id'];
        if (!is_numeric($user_id)) {
            header('location:users.php');
        }
        $user_id = (int) $user_id;
        $sql = "SELECT * FROM user_form WHERE id = '$user_id'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        if (empty($user)) {
            header('location:users.php');
        }
        if ($user["user_type"] != "user") {
            header('location:users.php');
        }
        $email = $user["email"];
    ?>
      <h3>Change expiration date for <?php echo $email; ?></h3>
      <p>Current expiration date: <?php echo $user["expiration"]; ?></p>
      <?php
      if(isset($error)){
         foreach($error as $error){
            echo '<span class="error-msg">'.$error.'</span>';
         };
      };
      ?>
      <input type="text" name="expiration" placeholder="New Expiration date (DD/MM/YYYY)" class="form-control" required>
        <input type="hidden" name="id" value="<?php echo $user_id; ?>">
      <input type="submit" name="submit" value="Change Expiration" class="form-btn">
   </form>

</div>

</body>
</html>