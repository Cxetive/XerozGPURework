<?php

$conn = mysqli_connect('localhost','root','','gpu_xeroz');

session_start();

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    $sql = "SELECT * FROM user_form WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    mysqli_close($conn);
    // check if expiration date has passed, it is a string in format DD/MM/YYYY
    $expiration_date = $user["expiration"];
    $expiration_date = explode("/", $expiration_date);
    $expiration_date = $expiration_date[2] . "-" . $expiration_date[1] . "-" . $expiration_date[0];
    $expiration_date = strtotime($expiration_date);
    $today = strtotime(date("Y-m-d"));
    if ($expiration_date < $today) {
        // delete user from database
        $sql = "DELETE FROM user_form WHERE id = $id";
        mysqli_query($conn, $sql);
        mysqli_close($conn);
        session_unset();
        header('location:login_form.php');
    }
}

?>