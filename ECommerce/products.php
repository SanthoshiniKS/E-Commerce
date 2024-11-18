<?php
session_start();
include "db.php";

// Check session to maintain user login
if (!isset($_SESSION['user_name'])) {
    header("Location: login.html");
    exit();
}

// Base query to fetch all products
$conditions = [];
if (isset($_GET['category']) && $_GET['category'] != '') {
    $conditions[] = "category = '" . $conn->real_escape_string($_GET['category']) . "'";
}
if (isset($_GET['min_price']) && $_GET['min_price'] != '') {
    $conditions[] = "price >= " . (float)$_GET['min_price'];
}
if (isset($_GET['max_price']) && $_GET['max_price'] != '') {
    $conditions[] = "price <= " . (float)$_GET['max_price'];
}

// Construct query with filters
$condition_sql = count($conditions) > 0 ? "WHERE " . implode(" AND ", $conditions) : '';
$products_query = "SELECT id, name, image_url, price, category FROM products $condition_sql";
$products_result = $conn->query($products_query);

// Fetch unique categories for dropdown
$categories_query = "SELECT DISTINCT category FROM products";
$categories_result = $conn->query($categories_query);

$user_id = $_SESSION['user_id'] ?? 1;

if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, intval($_POST['quantity']));

    // Fetch product details
    $product_query = "SELECT price FROM products WHERE id = ?";
    $stmt = $conn->prepare($product_query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        $price = $product['price'];
        $subtotal = $price * $quantity;

        // Check if the product already exists in the user's cart
        $cart_query = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($cart_query);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $cart_result = $stmt->get_result();

        if ($cart_result->num_rows > 0) {
            // Update quantity and subtotal if the product is already in the cart
            $cart_item = $cart_result->fetch_assoc();
            $new_quantity = $cart_item['quantity'] + $quantity;
            $update_query = "UPDATE cart SET quantity = ?, subtotal = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("idi", $new_quantity, $subtotal, $cart_item['id']);
            $stmt->execute();
        } else {
            // Insert new product into the cart
            $insert_query = "INSERT INTO cart (user_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("iiid", $user_id, $product_id, $quantity, $subtotal);
            $stmt->execute();
        }
    }

    // Redirect to cart.php
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            padding: 15px 30px;
            background-color: #333;
            color: #fff;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 10px;
        }

        .navbar a:hover {
            text-decoration: underline;
        }
        /* Filters Section */
.filter-dropdown {
    display: flex;
    justify-content: space-between; /* Space between elements */
    align-items: center; /* Center vertically */
    padding: 20px; /* Add some padding for spacing */
    margin: 20px 0; /* Separate from other elements */
}

/* Amount Filter (left-aligned) */
.filter-dropdown .amount-filter {
    display: flex;
    flex-direction: row; /* Align input fields horizontally */
    gap: 10px; /* Add spacing between inputs */
}

/* Category Filter (right-aligned) */
.filter-dropdown .category-filter {
    margin-left: auto; /* Push the category filter to the right */
}

/* Shared Styles for Inputs and Buttons */
.filter-dropdown select,
.filter-dropdown input {
    padding: 10px;
    font-size: 16px;
}

.filter-dropdown button {
    padding: 10px 20px;
    background-color: #007BFF;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.filter-dropdown button:hover {
    background-color: #0056b3;
}

        /* Product Grid */
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        /* Product Card */
        .product-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            width: 300px;
            height: 500px;
            text-align: center;
            transition: transform 0.3s;
            box-sizing: border-box;
        }

        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .product-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 5px;
        }

        .product-card h3 {
            font-size: 20px;
            margin: 10px 0;
        }

        .product-card p {
            font-size: 18px;
            color: #333;
        }

        .product-card button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .product-card button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <header class="navbar">
        <div>Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?></div>
        <nav>
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
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <!-- Filter Dropdown -->
    <div class="filter-dropdown">
        
            <div class="amount-filter">
            <form method="GET" action="products.php">
                <input type="number" name="min_price" placeholder="Min Price" value="<?= isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                <input type="number" name="max_price" placeholder="Max Price" value="<?= isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                <button type="submit">Filter</button>
    </form>
            </div>

            <div class="category-filter">
            <form method="GET" action="products.php">
            <select name="category" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php while ($category = $categories_result->fetch_assoc()) { ?>
                    <option value="<?= htmlspecialchars($category['category']); ?>" 
                        <?= (isset($_GET['category']) && $_GET['category'] == $category['category']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($category['category']); ?>
                    </option>
                <?php } ?>
            </select>
    </div>
        </form>
    </div>

    <!-- Products Grid -->
    <div class="product-grid">
        <?php
        if ($products_result->num_rows > 0) {
            while ($product = $products_result->fetch_assoc()) {
                $image_url = !empty($product['image_url']) ? 'ProductImages/' . $product['image_url'] : 'default-product.jpg';
        ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($image_url); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
                    <h3><?= htmlspecialchars($product['name']); ?></h3>
                    <p>Price: Rs.<?= htmlspecialchars($product['price']); ?></p>
                    <form method="POST" action="products.php">
                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                        <input type="hidden" name="product_name" value="<?= htmlspecialchars($product['name']); ?>">
                        <input type="hidden" name="product_price" value="<?= $product['price']; ?>">
                        <button type="submit" name="add_to_cart">Add to Cart</button>
                    </form>
                </div>
        <?php
            }
        } else {
            echo "<p>No products available.</p>";
        }
        ?>
    </div>
</body>
</html>
