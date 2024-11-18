<?php
session_start();
include "db.php"; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id']; // Get logged-in user ID

// Fetch user details from the database
$query = "SELECT * FROM customer WHERE cid = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    // Get updated details from the form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Sanitize inputs
    $name = mysqli_real_escape_string($conn, $name);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);
    $address = mysqli_real_escape_string($conn, $address);

    // Update user details in the database
    $update_query = "UPDATE customer SET name='$name', email='$email', phone='$phone', address='$address' WHERE cid='$user_id'";

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Details updated successfully!');</script>";
        // Refresh the page to display updated details
        header("Location: my_account.php");
        exit();
    } else {
        echo "<script>alert('Error updating details. Please try again.');</script>";
    }
}

if (isset($_POST['delete_account'])) {
    // Delete user account from the database
    $delete_query = "DELETE FROM customer WHERE cid='$user_id'";

    if (mysqli_query($conn, $delete_query)) {
        // Destroy the session and redirect to login page
        session_destroy();
        header("Location: login.html");
        exit();
    } else {
        echo "<script>alert('Error deleting account. Please try again.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Store</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="my_account.css">
</head>
<body>
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

    <section class="account-section">
        <h2>My Account</h2>
        <form method="POST" action="my_account.php">
            <div class="account-detail">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" readonly>
            </div>

            <div class="account-detail">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" readonly>
            </div>

            <div class="account-detail">
                <label for="phone">Phone:</label>
                <input type="int" id="phone" name="phone" value="<?php echo $user['phone']; ?>" readonly>
            </div>

            <div class="account-detail">
                <label for="address">Address:</label>
                <textarea id="address" name="address" readonly><?php echo $user['address']; ?></textarea>
            </div>

            <div class="button-group">
                <button type="button" id="edit-btn">Edit</button>
                <button type="submit" name="update" id="update-btn" style="display: none;">Update Details</button>
                <button type="submit" name="delete_account" id="delete-btn" onclick="return confirmDelete()">Delete Account</button>
            </div>
        </form>
    </section>

    <footer class="footer">
        <p>&copy; 2024 E-Store. All rights reserved.</p>
    </footer>

    <script>
        // Enable fields for editing when the "Edit" button is clicked
        document.getElementById('edit-btn').addEventListener('click', function() {
            document.getElementById('name').readOnly = false;
            document.getElementById('email').readOnly = false;
            document.getElementById('phone').readOnly = false;
            document.getElementById('address').readOnly = false;

            // Hide Edit button and show Update button
            document.getElementById('edit-btn').style.display = 'none';
            document.getElementById('update-btn').style.display = 'inline-block';
        });

        // Function to confirm account deletion
        function confirmDelete() {
            return confirm("Are you sure you want to delete your account? This action is irreversible.");
        }
    </script>
</body>
</html>

