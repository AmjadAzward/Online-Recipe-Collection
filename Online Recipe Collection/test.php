<?php
$mysqli = new mysqli("localhost:3307", "root", "", "users");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
echo "Connected successfully";
?>