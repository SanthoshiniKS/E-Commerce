<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['mail'];
    $password = $_POST['pw'];

    $query = "SELECT password FROM customer WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($stored_hash);
    $stmt->fetch();

    if (password_verify($password, $stored_hash)) {
        $_SESSION['user_name'] = $username; 
        echo "Login successful!";
        header("Location: index.php");  
    } else {
        // Password is incorrect
        echo "Invalid login credentials!";
    }
}
?>