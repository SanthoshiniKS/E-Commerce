<?php
session_start();
include "db.php";

/*if (isset($_POST['login'])) {
    $email = $_POST['mail'];
    $password = $_POST['pw'];

    // Sanitize inputs
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);
    if (password_verify($password, $user['password'])) {
    $query = "SELECT * FROM customer WHERE email = '$email' AND password = '$password'"; 
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        $_SESSION['user_id'] = $row['cid'];
        $_SESSION['user_name'] = $row['name'];
    }
    } else {
        // Invalid credentials, show an alert
        echo "<script>alert('Invalid email or password');</script>";
    }
}*/
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Store</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="logo">E-Store</div>
        <div class="search-bar">
            <input type="text" placeholder="Search products...">
            <button>Search</button>
        </div>
        <nav class="navbar">
            <a href="index.php">Home</a>
            <a href="products.php">Products</a>
            <a href="cart.php">Cart</a>
            
            <?php
            if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])) {
                echo "
                <div class='dropdown'>
                    <button class='dropbtn'>{$_SESSION['user_name']}</button>
                    <div class='dropdown-content'>
                        <a href='my_orders.php'>My Orders</a>
                        <a href='my_account.php'>My Account</a>
                        <a href='logout.php'>Logout</a>
                    </div>
                </div>";
            } else {
                // If the user is not logged in, show the 'Account' button
                echo "<button onclick=\"window.location.href='login.html'\">Account</button>";
            }
            ?>
        </nav>
    </header>

    <div id="slider">
        <figure>
            <img src="homeimg.jpg" height="500px">
            <img src="homeimg2.jpg" height="500px">
            <img src="homeimg3.jpg" height="500px">
        </figure>
    </div>
    
    <section class="hero">
        <button>Shop Now</button>
    </section>
    <section class="products">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <div class="product-card">
                <img src="ProductImages\compound effect.jpg" alt="Product 1">
                <h3>Product 1</h3>
                <p>Rs.200.00</p>
                <button>Add to Cart</button>
            </div>
            <div class="product-card">
                <img src="ProductImages\AI.jpg" alt="Product 2">
                <h3>Product 2</h3>
                <p>Rs.150.00</p>
                <button>Add to Cart</button>
            </div>
            <div class="product-card">
                <img src="ProductImages/formal.jpg" alt="Product 3">
                <h3>Product 3</h3>
                <p>Rs.400.00</p>
                <button>Add to Cart</button>
            </div>
        </div>
    </section>

    <footer class="footer">
        <p>&copy; 2024 E-Store. All rights reserved.</p>
        <div class="socials">
            <a href="#">Facebook</a>
            <a href="#">Twitter</a>
            <a href="#">Instagram</a>
        </div>
    </footer>

    <script>
document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.product-card button');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            alert('Product added to cart!');
        });
    });
});

    </script>
</body>
</html>
