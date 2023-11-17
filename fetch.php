<?php
    $servername = "217.119.143.121";
    $username = "pyro";
    $password = "L8#->0f[3:K9";
    $dbname = "paymenter";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $product_id = 47;

    $sql = "SELECT order_id, user_id FROM order_products 
    WHERE product_id = $product_id";

    $result = $conn->query($sql);
    $users = array();
    if ($result->num_rows > 0) {
        $order_ids = array();
        foreach($result as $row) {
            $order_ids[] = $row['order_id'];
        }
        foreach($order_ids as $order_id) {
            $sql = "SELECT user_id FROM orders 
            WHERE id = $order_id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                foreach($result as $row) {
                    $users[] = $row['user_id'];
                }
            }
        }
    }
?>