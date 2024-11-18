<?php
session_start();
include "db.php";

if (isset($_POST['login-form'])) {
    $email = $_POST['mail'];
    $password = $_POST['pw'];

    if($email === "admin" && $password === "admin_!23"){
        header('Location: admin.php');
    }

    // Sanitize inputs to prevent SQL injection
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Fetch user data based on email
    $query = "SELECT * FROM customer WHERE email = '$email'"; 
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, start a session
            $_SESSION['user_id'] = $user['cid'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];  // Store the email in session

            // Redirect to the index page
            echo "<script>alert('Login successful! Welcome, {$user['email']}');</script>";
            echo "<script>window.location.href='index.php';</script>";
        } else {
            // Password is incorrect
            echo "<script>alert('Invalid email or password');</script>";
        }
    } else {
        // Email not found in the database
        echo "<script>alert('No account found with this email');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Store - Login</title>
    <link rel="stylesheet" href="styles.css">
    <link href="login.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <form action="login.php" method="post" id="login-form">
    <div class="head">LOGIN</div>
    <div class="input-group">
        <label>Email</label>
        <input type="email" name="mail" required>
    </div>
    <div class="input-group">
        <label>Password</label>
        <input type="password" name="pw" required>
</div>
        <div class="buttons">
                <label>New User??</label>
                <a href="register.php" class="reg">Register</a>
                <button type="submit" name="login-form">Login</button>
        </div>
    </div>

        
    </form>
</div>
</body>
</html>
