<?php
    @include 'config.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $today = date("d/m/Y");
    $time = date("H:i");

    $sql = "SELECT * FROM timeslots WHERE day = '$today'";
    $result = mysqli_query($conn, $sql);
    $slots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);


    foreach ($slots as $closest_timeslot) {
        $slot_id = $closest_timeslot["id"];
        $sql = "SELECT * FROM timeslots WHERE id = '$slot_id'";
        $result = mysqli_query($conn, $sql);
        $slot = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        if ($slot["is_booked"] == 0) {
            continue;
        }

        if ($slot["email_sent"] == 1) {
            continue;
        }

        include_once("functions/utils_functions.php");
        $start_time = $slot["start_time"];
        if (!is_in_the_past($start_time)) {
            break;
        }

        $sql = "SELECT * FROM current_credentials WHERE id = 1";
        $result = mysqli_query($conn, $sql);
        $credentials = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        $prev_slot_id = $slot_id - 1;
        $sql = "SELECT * FROM timeslots WHERE id = '$prev_slot_id'";
        $result = mysqli_query($conn, $sql);
        $previous_slot = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        if ($previous_slot["server_reset"] == 0) {
            continue;
        }

        $ip = $credentials["ip"];
        $password = $credentials["password"];

        $message = "Dear Customer,<br><br>

We're delighted to announce that your Xeroz.Tech cloud gaming service is now active, and it's your turn to experience top-notch gaming excitement!<br><br>

Log In:<br><br>

In order to connect to your GPU-PC we advice to follow our guide, https://xeroz-1.gitbook.io/xeroz-knowledgebase/v/gpupc/first-steps<br><br>

IP: " . $ip . "<br>
Password: " . $password . "<br><br>

For any questions, technical challenges, or assistance, our dedicated support team is here to assist you. Please feel free to reach out to us at support@xeroz.tech, and we'll be swift in addressing your concerns.<br><br>

We are devoted to providing you with a premium gaming experience, and we highly value your input. Don't hesitate to share your thoughts and suggestions. Your gaming adventure is just about to unfold, and we're thrilled to be part of it.<br><br>

Thank you for choosing Xeroz.Tech as your cloud gaming service provider. Prepare for an immersive gaming experience filled with excitement and victories!<br><br>

Warm regards,<br><br>

Team Xeroz.Tech";

        $username = "gpu@xeroz.tech";
        $smtp_password = "DvJRAa2ZD";

        $smtp_host = "mail.xeroz.tech";
        $smtp_port = "587";

        $to_id = $slot["assigned_to"];
        $sql = "SELECT * FROM user_form WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $to_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        $to = $user["email"];


        $from = "gpu@xeroz.tech";
        $subject = "Xeroz GPU server access";

        $phpmailer = new PHPMailer(true);
        $phpmailer->isSMTP();
        $phpmailer->Host = $smtp_host;
        $phpmailer->SMTPAuth = true;
        $phpmailer->Username = $username;
        $phpmailer->Password = $smtp_password;
        $phpmailer->SMTPSecure = 'tls';
        $phpmailer->Port = $smtp_port;

        $phpmailer->setFrom($from, 'Xeroz.Tech');
        $phpmailer->addAddress($to);
        $phpmailer->Subject = $subject;
        $phpmailer->Body = $message;
        $phpmailer->isHTML(true);

        try {
            $phpmailer->send();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$phpmailer->ErrorInfo}";
        }

        $sql = "UPDATE timeslots SET email_sent = 1 WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $slot_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
    }

    // do the same for free_tier_timeslots
    $sql = "SELECT * FROM free_tier_timeslots WHERE day = '$today'";
    $result = mysqli_query($conn, $sql);
    $slots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_free_result($result);

    foreach ($slots as $closest_timeslot) {
        $slot_id = $closest_timeslot["id"];
        $sql = "SELECT * FROM free_tier_timeslots WHERE id = '$slot_id'";
        $result = mysqli_query($conn, $sql);
        $slot = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        if ($slot["is_booked"] == 0) {
            continue;
        }

        if ($slot["email_sent"] == 1) {
            continue;
        }

        $start_time = $slot["start_time"];
        $start_time = explode(":", $start_time);
        $start_time = (int) $start_time[0] * 60 + (int) $start_time[1];
        $time = explode(":", $time);
        $time = (int) $time[0] * 60 + (int) $time[1];
        if ($start_time > $time) {
            break;
        }

        $sql = "SELECT * FROM current_credentials WHERE id = 2";
        $result = mysqli_query($conn, $sql);
        $credentials = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        $prev_slot_id = $slot_id - 1;
        $sql = "SELECT * FROM free_tier_timeslots WHERE id = '$prev_slot_id'";
        $result = mysqli_query($conn, $sql);
        $previous_slot = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        if ($previous_slot["server_reset"] == 0) {
            continue;
        }

        $ip = $credentials["ip"];
        $password = $credentials["password"];

        $message = "Dear Customer,<br><br>

We're delighted to announce that your Xeroz.Tech cloud gaming service is now active, and it's your turn to experience top-notch gaming excitement!<br><br>

Log In:<br><br>

In order to connect to your GPU-PC we advice to follow our guide, https://xeroz-1.gitbook.io/xeroz-knowledgebase/v/gpupc/first-steps<br><br>

IP: " . $ip . "<br>
Password: " . $password . "<br><br>

For any questions, technical challenges, or assistance, our dedicated support team is here to assist you. Please feel free to reach out to us at support@xeroz.tech, and we'll be swift in addressing your concerns.<br><br>

We are devoted to providing you with a premium gaming experience, and we highly value your input. Don't hesitate to share your thoughts and suggestions. Your gaming adventure is just about to unfold, and we're thrilled to be part of it.<br><br>

Thank you for choosing Xeroz.Tech as your cloud gaming service provider. Prepare for an immersive gaming experience filled with excitement and victories!<br><br>

Warm regards,<br><br>

Team Xeroz.Tech";

    $username = "gpu@xeroz.tech";
    $smtp_password = "DvJRAa2ZD";

    $smtp_host = "mail.xeroz.tech";
    $smtp_port = "587";

    $to_id = $slot["assigned_to"];
    $sql = "SELECT * FROM user_form WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $to_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_free_result($result);
    $to = $user["email"];


    $from = "gpu@xeroz.tech";
    $subject = "Xeroz GPU server access";

    $phpmailer = new PHPMailer(true);
    $phpmailer->isSMTP();
    $phpmailer->Host = $smtp_host;
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = $username;
    $phpmailer->Password = $smtp_password;
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->Port = $smtp_port;

    $phpmailer->setFrom($from, 'Xeroz.Tech');
    $phpmailer->addAddress($to);
    $phpmailer->Subject = $subject;
    $phpmailer->Body = $message;
    $phpmailer->isHTML(true);

    try {
        $phpmailer->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$phpmailer->ErrorInfo}";
    }

    $sql = "UPDATE free_tier_timeslots SET email_sent = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $slot_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);


    }

    echo "done";
?>