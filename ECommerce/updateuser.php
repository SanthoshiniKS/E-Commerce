<?php
session_start();
include "db.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];

// Update the user details in the database
$sql = "UPDATE customer SET name = ?, email = ?, phone = ?, address = ? WHERE cid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $name, $email, $phone, $address, $user_id);

if ($stmt->execute()) {
    // Redirect to the account page or show a success message
    header("Location: my_account.php?update=success");
} else {
    // Handle errors
    echo "Error updating record: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
