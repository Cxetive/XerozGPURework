<?php

    @include 'config.php';

    session_start();

    $sql = "SELECT is_free_tier FROM user_form WHERE id = " . $_SESSION['id'];
    $result = mysqli_query($conn, $sql);
    $is_free_tier = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    if ($is_free_tier["is_free_tier"] == 1) {
        $timeslots_table = "free_tier_timeslots";
    } else {
        $timeslots_table = "timeslots";
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

    $today = date("d/m/Y");

    $time = date("H:i");

    if ($slot["is_booked"] == 0) {
        header('location:user_page.php');
    }

    if ($slot["assigned_to"] != $_SESSION['id']) {
        header('location:user_page.php');
    }

    // compare time to start time, start time will be in the format HH:MM. Can't cancel booking if the slot is starting in less than 10 minutes
    $start_time = $slot["start_time"];
    $start_time = explode(":", $start_time);
    $start_time = (int) $start_time[0] * 60 + (int) $start_time[1];
    $time = explode(":", $time);
    $time = (int) $time[0] * 60 + (int) $time[1];
    if ($start_time - $time <= 10) {
        // check if day if <= today
        $day = $slot["day"];
        $day = explode("/", $day);
        $day = (int) $day[0] . $day[1] . $day[2];
        $today = explode("/", $today);
        $today = (int) $today[0] . $today[1] . $today[2];
        if ($day <= $today) {
            $error_message = "You can't cancel your booking if the slot is starting in less than 10 minutes!";
            include_once('user_page.php');
            exit();
        }
    }

    if ($slot["email_sent"] == 1) {
        $error_message = "You can't cancel your booking because the access email has already been sent!";
        include_once('user_page.php');
        exit();
    }

    $sql = "UPDATE $timeslots_table SET is_booked = 0, assigned_to = 0 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $slot_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM books WHERE user_id = ? AND day = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'ss', $_SESSION['id'], $today);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    mysqli_close($conn);

    include_once('functions/utils_functions.php');
    send_discord_webhook_cancel($slot["day"], $slot["start_time"], $slot["end_time"], $_SESSION['user_name']);

    $message = "You have successfully cancelled your booking!";
    include_once('user_page.php');
?>