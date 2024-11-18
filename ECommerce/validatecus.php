<?php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the values from the form
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['mail']);
    $address = mysqli_real_escape_string($conn, $_POST['ad']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $password = mysqli_real_escape_string($conn, $_POST['pw']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_pw']);

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
    } else {
        // Encrypt the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // SQL query to insert the data into the customers table
        $sql = "INSERT INTO customer (name, email, address, phone, password) 
                VALUES ('$name', '$email', '$address', '$contact', '$hashed_password')";

if ($conn->query($sql) === TRUE) {
    // Get the user ID after insertion
    $user_id = $conn->insert_id;

    // Store the user ID and name in the session
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $name;

    // Redirect to the homepage
    header("Location:index.php");
    exit();
}
    else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    
    $conn->close();
} 
}else {
    echo "Invalid request method!";
}
?>
