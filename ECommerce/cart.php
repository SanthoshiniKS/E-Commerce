<?php
session_start();
include "db.php";

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Mock user ID (replace with session-based user ID)
$user_id = $_SESSION['user_id'];

// Fetch cart items for the logged-in user
$cart_query = "
    SELECT c.id, p.name, p.image_url, c.quantity, c.subtotal, p.price
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

// Calculate total bill
$total_bill = 0;
foreach ($cart_items as $item) {
    $total_bill += $item['subtotal'];
}

// Handle quantity update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_quantity'])) {
    $cart_id = $_POST['cart_id'];
    $new_quantity = $_POST['quantity'];
    $update_query = "
        UPDATE cart 
        SET quantity = ?, subtotal = (SELECT price FROM products WHERE id = (SELECT product_id FROM cart WHERE id = ?)) * ?
        WHERE id = ?
    ";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("iiii", $new_quantity, $cart_id, $new_quantity, $cart_id);
    $stmt->execute();
    header("Location: cart.php"); // Redirect to refresh cart
}

// Handle item removal
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_item'])) {
    $cart_id = $_POST['cart_id'];
    $remove_query = "DELETE FROM cart WHERE id = ?";
    $stmt = $conn->prepare($remove_query);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    header("Location: cart.php"); // Redirect to refresh cart
}

// Handle item purchase (buying the item)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buy_item'])) {
    $cart_id = $_POST['cart_id'];

    // Here, you would typically move the cart item to an order table
    // Example: Add the cart item to the orders table
    $buy_query = "
        INSERT INTO orders (user_id, product_id, quantity, price, subtotal, order_date)
        SELECT c.user_id, c.product_id, c.quantity, p.price, c.subtotal, NOW()
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.id = ?
    ";

    $stmt = $conn->prepare($buy_query);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();

    // Remove item from the cart after purchase
    $remove_query = "DELETE FROM cart WHERE id = ?";
    $stmt = $conn->prepare($remove_query);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();

    header("Location: cart.php"); // Redirect to refresh cart
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset(); // Unset all session variables
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login page
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link rel="stylesheet" href="cart.css">
    <link rel="stylesheet" href="styles.css">
    <script>
        function confirmRemove(cart_id) {
            if (confirm("Are you sure you want to remove this item from the cart?")) {
                document.getElementById('remove_form_' + cart_id).submit();
            }
        }

        function updateQuantity(cart_id, price) {
            const newQuantity = prompt("Enter new quantity:", 1);
            if (newQuantity && !isNaN(newQuantity) && newQuantity > 0) {
                document.getElementById('update_quantity_' + cart_id).value = newQuantity;
                document.getElementById('update_form_' + cart_id).submit();
            }
        }
    </script>
</head>
<body>
    <header>
        
    <nav class="navbar">
    <div class="logo">E-Store</div>
        <div class="search-bar">
            <input type="text" placeholder="Search products...">
            <button>Search</button>
        </div>
        <a href="index.php">Home</a></li>
       <a href="products.php">Products</a></li>
        <a href="cart.php">My Cart</a></li>

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
                // If the user is not logged in, show the 'Account' button
                echo "<button onclick=\"window.location.href='login.html'\">Account</button>";
            }
            ?>
    </nav>
    </header>

    <center>
        <h1>My Cart</h1></center>
    </header>

    <div class="cart-container">
        <?php if (!empty($cart_items)) { ?>
            <?php foreach ($cart_items as $item) { 
                $image_url = !empty($item['image_url']) ? 'ProductImages/' . $item['image_url'] : 'default-product.jpg';
                ?>

                <div class="cart-item">
                    <img src="<?= htmlspecialchars($image_url) ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                    <div class="cart-item-details">
                        <h2><?= htmlspecialchars($item['name']); ?></h2>
                        <p>Price: Rs.<?= htmlspecialchars($item['price']); ?></p>
                        <p>Quantity: <?= htmlspecialchars($item['quantity']); ?></p>
                        <p>Subtotal: Rs.<?= htmlspecialchars($item['subtotal']); ?></p>
                    </div>
                    <div class="cart-item-actions">
                        <form id="update_form_<?= $item['id'] ?>" method="POST">
                            <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="update_quantity" value="1">
                            <input type="hidden" id="update_quantity_<?= $item['id'] ?>" name="quantity" value="<?= $item['quantity'] ?>">
                        </form>
                        <button onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['price'] ?>)">Update Quantity</button>

                        <form id="remove_form_<?= $item['id'] ?>" method="POST">
                            <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="remove_item" value="1">
                        </form>
                        <button onclick="confirmRemove(<?= $item['id'] ?>)">Remove Item</button>
                <form id="buy_form_<?= $item['id'] ?>" method="GET" action="payment.php">
                    <input type="hidden" name="cart_id" value="<?= $item['id'] ?>">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">
                    <input type="hidden" name="total_amount" value="<?= $total_bill ?>">
                </form>
                <button onclick="document.getElementById('buy_form_<?= $item['id'] ?>').submit()">Buy</button>

                    </div>
                </div>
            <?php } ?>
            <div class="total-bill">
                Total Bill: Rs.<?= htmlspecialchars($total_bill); ?>
            </div>
        <?php } else { ?>
            <p>Your cart is empty.</p>
        <?php } ?>
    </div>
</body>
</html>
