<?php
    @include 'config.php';
    @include 'timeslot.php';

    session_start();

    // prevent endpoint from being spammed
    if (isset($_SESSION["slots_made"])) {
        if (time() - $_SESSION["slots_made"] < 60 * 10) {
            exit();
        }
    }
    
    $_SESSION["slots_made"] = time();

    // today should be equal to a string of the current date in the format DD/MM/YYYY
    $today = date("d/m/Y");
    $tomorrow = date("d/m/Y", strtotime("+1 day"));
    $sql = "SELECT * FROM timeslots WHERE day = '$today'";
    $result = mysqli_query($conn, $sql);
    $slots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);
    mysqli_close($conn);
    // check if there is only 1 timeslot today
    if (empty($slots) || count($slots) <= 1) {
        // day of the week should be equal to a string of the current day of the week
        $day_of_the_week = date("l");
        // check if its monday to thursday
        if ($day_of_the_week == "Monday" || $day_of_the_week == "Tuesday" || $day_of_the_week == "Wednesday" || $day_of_the_week == "Thursday") {
            $timeslots = array();
            // id is irrelevant since it will be assigned by the database
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "03:00", "06:00", "green");
            $timeslots[] = $temp_timeslot;  
            $temp_timeslot = new TimeSlot(0, $today, "06:00", "09:00", "orange");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "09:00", "12:00", "cyan");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "12:00", "15:00", "blue");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "15:00", "18:00", "brown");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "18:00", "21:00", "orange");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "21:00", "24:00", "yellow");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $tomorrow, "00:00", "03:00", "yellow");
            $timeslots[] = $temp_timeslot;
        }
        if ($day_of_the_week == "Friday") {
            // id is irrelevant since it will be assigned by the database
            $temp_timeslot = new TimeSlot(0, $today, "03:00", "06:00", "green");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "06:00", "09:00", "orange");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "09:00", "12:00", "cyan");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "12:00", "15:00", "blue");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "15:00", "18:00", "brown");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "18:00", "21:00", "orange");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "21:00", "24:00", "yellow");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $tomorrow, "00:00", "03:00", "yellow");
            $timeslots[] = $temp_timeslot;
        }
        if ($day_of_the_week == "Saturday" || $day_of_the_week == "Sunday") {
            // id is irrelevant since it will be assigned by the database
            $temp_timeslot = new TimeSlot(0, $today, "03:00", "06:00", "green");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "06:00", "09:00", "orange");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "09:00", "12:00", "cyan");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "12:00", "15:00", "brown");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "15:00", "18:00", "orange");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "18:00", "21:00", "yellow");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $today, "21:00", "24:00", "yellow");
            $timeslots[] = $temp_timeslot;
            $temp_timeslot = new TimeSlot(0, $tomorrow, "00:00", "04:00", "yellow");
            $timeslots[] = $temp_timeslot;
        }
        foreach ($timeslots as $timeslot) {
            $day = $timeslot->day;
            $start_time = $timeslot->start_time;
            $end_time = $timeslot->end_time;
            $color = $timeslot->color;
            $sql = "INSERT INTO timeslots(day, start_time, end_time, color, is_booked, assigned_to) VALUES('$day', '$start_time', '$end_time', '$color', false, 0)";
            @include 'config.php';
            if (mysqli_query($conn, $sql)) {
                echo "success";
            } else {
                echo "error";
            }
        }

    }

    @include 'config.php';
    $sql = "SELECT * FROM free_tier_timeslots WHERE day = '$today'";
    $result = mysqli_query($conn, $sql);
    $slots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);

    if (empty($slots) || count($slots) <= 1) {
        $timeslots = array();

        $temp_timeslot = new TimeSlot(0, $today, "01:30", "03:00", "green");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "03:00", "04:30", "orange");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "04:30", "06:00", "cyan");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "06:00", "07:30", "blue");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "07:30", "09:00", "brown");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "09:00", "10:30", "orange");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "10:30", "12:00", "yellow");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "12:00", "13:30", "green");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "13:30", "15:00", "orange");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "15:00", "16:30", "cyan");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "16:30", "18:00", "blue");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "18:00", "19:30", "brown");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "19:30", "21:00", "orange");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "21:00", "22:30", "yellow");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $today, "22:30", "24:00", "green");
        $timeslots[] = $temp_timeslot;
        $temp_timeslot = new TimeSlot(0, $tomorrow, "00:00", "01:30", "orange");
        $timeslots[] = $temp_timeslot;

        foreach ($timeslots as $timeslot) {
            $day = $timeslot->day;
            $start_time = $timeslot->start_time;
            $end_time = $timeslot->end_time;
            $color = $timeslot->color;
            $sql = "INSERT INTO free_tier_timeslots(day, start_time, end_time, color, is_booked, assigned_to) VALUES('$day', '$start_time', '$end_time', '$color', false, 0)";
            @include 'config.php';
            if (mysqli_query($conn, $sql)) {
                echo "success";
            } else {
                echo "error";
            }
        }
    }
?>