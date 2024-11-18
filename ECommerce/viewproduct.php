<?php
include "db.php";
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo '<section class="products">';
    echo '<h2>Featured Products</h2>';
    echo '<div class="product-grid">';

    while($row = $result->fetch_assoc()) {
        echo '<div class="product-card">';
        
        $image_url = !empty($row['image_url']) ? 'ProductImages/' . $row['image_url'] : 'default-product.jpg';

        echo '<img src="' . $image_url . '" alt="' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '">';
        
        echo '<h3>' . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . '</h3>';
        
        echo '<p>$' . number_format($row['price'], 2) . '</p>';

        // Pass product ID or other unique identifier for the product to the redirect function
        echo '<button type="button" onClick="redirect(' . $row['id'] . ')">Edit details</button>';
        
        echo '</div>';
    }
    
    echo '</div>';
    echo '</section>';
} else {
    echo "<p>No products found.</p>";
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <script>
        function redirect(productId) {
            // Redirect to edit_product.php and pass the product ID as a query parameter
            window.location.href = "edit_product.php?id=" + productId;
        }
    </script>
</body>
</html>
