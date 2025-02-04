<?php
$servername = "localhost";
$db_username = "root";
$db_passw = "";
$dbname = "db_pwl05";
// Create connection
$conn = new mysqli($servername, $db_username, $db_passw, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
