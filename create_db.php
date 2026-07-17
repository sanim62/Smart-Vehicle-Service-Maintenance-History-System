<?php
$link = mysqli_connect('127.0.0.1', 'root', '');
if (!$link) {
    die('Connect Error: ' . mysqli_connect_error());
}
if (mysqli_query($link, 'CREATE DATABASE IF NOT EXISTS vehicle_service_db')) {
    echo "Database created successfully\n";
} else {
    echo 'Error: ' . mysqli_error($link) . "\n";
}
mysqli_close($link);
