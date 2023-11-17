<?php
    @include 'config.php';

    session_start();

    $sql = "SELECT is_free_tier FROM user_form WHERE id = " . $_SESSION['id'];
    $result = mysqli_query($conn, $sql);
    $is_free_tier = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    if ($is_free_tier["is_free_tier"] == 1) {
        $timeslots_table = "free_tier_timeslots";
        // check if the user has 2 bookings in the last 4 days
        $sql = "SELECT * FROM books WHERE user_id = ? AND day >= ?";
        $stmt = mysqli_prepare($conn, $sql);
        $today = date("d/m/Y");
        $today = explode("/", $today);
        $today = $today[0] . $today[1] . $today[2];
        $today = (int) $today;
        $four_days_ago = date("d/m/Y", strtotime("-4 days"));
        $four_days_ago = explode("/", $four_days_ago);
        $four_days_ago = $four_days_ago[0] . $four_days_ago[1] . $four_days_ago[2];
        $four_days_ago = (int) $four_days_ago;
        mysqli_stmt_bind_param($stmt, 'ss', $_SESSION['id'], $four_days_ago);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $books = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        if (count($books) >= 2) {
            $error_message = "You have already booked 2 slots in the last 4 days!";
            include_once('user_page.php');
            exit();
        }
    } else {
        $timeslots_table = "timeslots";
    }

    if (!isset($_SESSION['user_name'])) {
        header('location:login_form.php');
    }

    $slot_id = $_GET['slot_id'];
    if (!is_numeric($slot_id)) {
        header('location:user_page.php');
    }

    $slot_id = (int) $slot_id;

    $sql = "SELECT * FROM $timeslots_table WHERE id = '$slot_id'";
    $result = mysqli_query($conn, $sql);
    $slot = mysqli_fetch_assoc($result);
    mysqli_free_result($result);

    if (empty($slot)) {
       header('location:user_page.php');
    }

    if ($slot["is_booked"] == 1) {
        $error_message = "This slot has already been booked!";
        include_once('user_page.php');
        exit();
    }

    include_once("functions/utils_functions.php");
    $start_time = $slot["start_time"];
    if (is_in_the_past($start_time)) {
        // check if day is today (day is a string in the format DD/MM/YYYY)
        $day = $slot["day"];
        $day = explode("/", $day);
        $day = $day[0] . $day[1] . $day[2];
        $day = (int) $day;
        $today = date("d/m/Y");
        $today = explode("/", $today);
        $today = $today[0] . $today[1] . $today[2];
        $today = (int) $today;
        if ($day <= $today) {
            $error_message = "This time slot is in the past!";
            include_once('user_page.php');
            exit();
        }
    }

    $sql = "SELECT * FROM books WHERE user_id = ? AND day = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $_SESSION['id'], $slot["day"]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $books = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
    if (!empty($books)) {
        $error_message = "You have already booked a slot for today!";
        include_once('user_page.php');
        exit();
    }

    $sql = "UPDATE $timeslots_table SET is_booked = 1, assigned_to = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $_SESSION['id'], $slot_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    
    $sql = "INSERT INTO books (user_id, color, day) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    $today = date("d/m/Y");
    mysqli_stmt_bind_param($stmt, 'sss', $_SESSION['id'], $slot["color"], $today);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    mysqli_close($conn);

    include_once('functions/utils_functions.php');

    send_discord_webhook_book($slot["day"], $slot["start_time"], $slot["end_time"], $_SESSION['user_name']);

    $message = "You have successfully booked a slot!";
    include_once('user_page.php');

?>