<?php
$servername = "localhost";
$db_username = "worrisome-bird-cue";
$db_passw = "(uSYB)W2Fe3r(ij762";
$dbname = "worrisome_bird_cue_db";
// Create connection
$conn = new mysqli($servername, $db_username, $db_passw, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
