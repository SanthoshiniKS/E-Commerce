<?php
session_start();
include "db.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the user details
$user_id = $_SESSION['user_id'];

// Query to fetch user details
$user_query = "SELECT name, email, phone, address FROM customer WHERE cid = ?";
$stmt = $conn->prepare($user_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $phone, $address);
$stmt->fetch();
$stmt->close();

// Fetch data from the GET parameters for cart details
$cart_id = $_GET['cart_id'];
$total_amount = $_GET['total_amount'];

// Fetch the cart items for the user
$cart_query = "
    SELECT c.id, p.name, p.image_url, c.quantity, c.subtotal, p.price
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ? AND c.id = ?
";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("ii", $user_id, $cart_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="payment.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body> 
<header class="header">
    <nav class="navbar">
        <a href="index.php">Home</a>
        <a href="products.php">Products</a>
        <a href="cart.php">Your Cart</a>
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
                </div>";}else {
                echo "<button onclick=\"window.location.href='login.html'\">Account</button>";
            }
            ?>
            <a href="?logout=true">Logout</a></li>
    
    </nav>
        </header>
    <header>
        <h1>Payment Details</h1>
        <a href="cart.php">Back to Cart</a>
    </header>

    <div class="payment-container">
        <h2>Your Account Details</h2>
        <form method="POST" action="updateuser.php">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($name); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email); ?>" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone); ?>" required>
            </div>
            <div class="form-group">
                <label for="address">Shipping Address</label>
                <textarea id="address" name="address" required><?= htmlspecialchars($address); ?></textarea>
            </div>
            <button type="submit">Update Details</button>
        </form>
        
        <h2>Your Cart Items</h2>
        <?php if (!empty($cart_items)) { ?>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item) { 
                        $image_url = !empty($item['image_url']) ? 'ProductImages/' . $item['image_url'] : 'default-product.jpg';
                    ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($item['name']); ?>" width="50"> <?= htmlspecialchars($item['name']); ?></td>
                        <td>Rs. <?= htmlspecialchars($item['price']); ?></td>
                        <td><?= htmlspecialchars($item['quantity']); ?></td>
                        <td>Rs. <?= htmlspecialchars($item['subtotal']); ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <div class="total">
                <p><strong>Total Amount to Pay: Rs. <?= htmlspecialchars($total_amount); ?></strong></p>
            </div>
            <form method="POST" action="process_payment.php">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <input type="hidden" name="total_amount" value="<?= $total_amount ?>">
                <input type="hidden" name="cart_id" value="<?= $cart_id ?>">
                <button type="submit">Proceed to Payment</button>
            </form>
        <?php } else { ?>
            <p>Your cart is empty.</p>
        <?php } ?>
    </div>
</body>
</html>
