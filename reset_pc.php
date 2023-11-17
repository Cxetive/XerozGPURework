<?php

@include 'config.php';


    include_once("functions/server_functions.php");
    reset_pc_servers('timeslots', 'hetzner', 1, $conn);
    reset_pc_servers('free_tier_timeslows', 'own', 2, $conn);
    return "ok";
?>