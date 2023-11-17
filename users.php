<?php

@include 'config.php';

session_start();

if(!isset($_SESSION['admin_name'])){
   header('location:index.php');
}

$usersPerPage = 10; // Number of users to display per page
if (isset($_GET['page'])) {
    $currentPage = intval($_GET['page']);
} else {
    $currentPage = 1;
}

if (isset($_GET['search'])) {
    $search = $_GET['search'];
} else {
    $search = "";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Users List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #17273a;
            color: white;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #ffffff;
            background-color: #1f364d;
            padding: 20px;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #1f364d;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #364e6d;
        }

        th {
            background-color: #1f364d;
        }

        tr:nth-child(even) {
            background-color: #243b55;
        }

        tr:hover {
            background-color: #364e6d;
        }

        .button-container {
            text-align: center;
            margin-top: 20px;
        }

        .admin-button {
            background-color: #364e6d;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Users List</h1>

    <?php
       
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Conexión a la base de datos utilizando la configuración de config.php
    $conn = mysqli_connect('localhost','gpu_xeroz','rJiEdw!nJ2#JvmK7','gpu_xeroz');

    // Verificar la conexión
    if (!$conn) {
        die("Error en la conexión: " . mysqli_connect_error());
    }

    // Consulta SQL para seleccionar datos de la tabla user_form
    $offset = ($currentPage - 1) * $usersPerPage;

    if ($search != "") {
        $sql = "SELECT * FROM user_form WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR user_type LIKE '%$search%' LIMIT $offset, $usersPerPage";
    } else {
        $sql = "SELECT * FROM user_form LIMIT $offset, $usersPerPage";
    }
    $result = mysqli_query($conn, $sql);

    // calculate total users using count function
    if ($search != "") {
        $sql = "SELECT COUNT(*) AS total FROM user_form WHERE name LIKE '%$search%' OR email LIKE '%$search%' OR user_type LIKE '%$search%'";
    } else {
        $sql = "SELECT COUNT(*) AS total FROM user_form";
    }
    $totalUsers = mysqli_query($conn, $sql);
    $totalUsers = mysqli_fetch_assoc($totalUsers);
    $totalUsers = $totalUsers['total'];
    $totalPages = ceil($totalUsers / $usersPerPage);

    // search bar here
    echo "<form action='' method='get'>
            <input type='text' name='search' placeholder='Search by name, email or user type' value='" . $search . "'>
            <input type='submit' value='Search'>
        </form>";
    // button to remove search
    if ($search != "") {
        echo "<a href='users.php'><button class='admin-button'>Remove Search</button></a>";
    }

    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Expiration Date</th>
                    <th>Free Trial</th>
                    <th>Actions</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . $row["id"] . "</td>
                    <td>" . $row["name"] . "</td>
                    <td>" . $row["email"] . "</td>
                    <td>" . $row["user_type"] . "</td>
                ";
                if ($row["user_type"] == "user") {
                    echo "<td>" . $row["expiration"] . "</td>";
                    if ($row["is_free_tier"] == 1) {
                        echo "<td>Yes</td>";
                        echo "<td><a href='set_paid.php?id=" . $row["id"] . "'><button class='admin-button'>Set as Paid</button></a></td>";
                    } else {
                        echo "<td>No</td>";
                        echo "<td><a href='set_free.php?id=" . $row["id"] . "'><button class='admin-button'>Set as Free</button></a></td>";
                    }
                    echo "<td><a href='change_expiration.php?id=" . $row["id"] . "'><button class='admin-button'>Change Expiration</button></a></td>";
                }
                echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<br>No results found for your search.<br>";
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($conn);

    if ($currentPage > 1) {
        if ($search != "") {
            echo "<a href='users.php?page=" . ($currentPage - 1) . "&search=" . $search . "'>&laquo; Previous</a>";
        } else {
            echo "<a href='users.php?page=" . ($currentPage - 1) . "'>&laquo; Previous</a>";
        }
    }
    for ($i = 1; $i <= $totalPages; $i++) {
        if ($i == $currentPage) {
            // grey out current page
            echo "<a href='users.php?page=" . $i . "'><button class='admin-button' style='background-color: grey;'>" . $i . "</button></a>";
        } else {
            echo "<a href='users.php?page=" . $i . "'><button class='admin-button'>" . $i . "</button></a>";
        }
    }
    if ($currentPage < $totalPages) {
        if ($search != "") {
            echo "<a href='users.php?page=" . ($currentPage + 1) . "&search=" . $search . "'>Next &raquo;</a>";
        } else {
            echo "<a href='users.php?page=" . ($currentPage + 1) . "'>Next &raquo;</a>";
        }
    }

    ?>

    <div class="button-container">
        <a href="admin_page.php" class="admin-button">Go Back Admin Panel</a>
    </div>
</body>
</html>
