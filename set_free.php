<?php

@include 'config.php';

session_start();

if(!isset($_SESSION['admin_name'])){
   header('location:index.php');
}

$id = $_GET['id'];

if (!is_numeric($id)) {
    header('location:users.php');
}

$id = (int) $id;

$sql = "SELECT * FROM user_form WHERE id = '$id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
mysqli_free_result($result);

if (empty($user)) {
    header('location:users.php');
}

$sql = "UPDATE user_form SET is_free_tier = 1 WHERE id = '$id'";
mysqli_query($conn, $sql);

mysqli_close($conn);

header('location:users.php');

?>
