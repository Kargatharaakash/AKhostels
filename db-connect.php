<?php
/**
 * Hostel Management System Database Connection
 * Updated for Hostel Management System
 */

$servername = "localhost";
$username = "root";
$password = "";
$database = 'akhostels';

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

mysqli_select_db($conn, $database) or die( "Unable to select database");
