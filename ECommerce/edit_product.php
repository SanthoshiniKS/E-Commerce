<?php
include "db.php";

// Check if `id` is passed in the query string
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']); // Sanitize the input to prevent SQL injection
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();  // Fetch the product details
    } else {
        echo "<script>alert('Product not found!'); window.location.href = 'index.php';</script>";
        exit();
    }

    // Handle product image URL
    if (!empty($product['image_url']) && file_exists('ProductImages/' . $product['image_url'])) {
        $image_url = 'ProductImages/' . $product['image_url'];
    } else {
        $image_url = 'ProductImages/default-product.jpg';  // Use a default image if product doesn't have one
    }
} else {
    echo "<script>alert('No product ID provided!'); window.location.href = 'index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Product Details</title>
    <link rel="stylesheet" href="adminsite.css">
</head>
<body>
<div class="container">
    <form action="update_product.php?id=<?php echo $product['id']; ?>" method="post" enctype="multipart/form-data">
        <div class="head">PRODUCT DETAILS</div>
        
        <div class="input-group">
            <label>Product Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>
        
        <div class="input-group">
            <label>Existing Image:</label>
            <img src="<?php echo $image_url; ?>" alt="Product Image" style="max-width: 150px;">
        </div>
        
        <div class="input-group">
            <label>New Product Image:</label>
            <input type="file" name="my_image" accept="image/jpeg, image/png">
            <span>Allowed types: jpg, jpeg, png</span>
        </div>
        
        <div class="input-group">
            <label>Price:</label>
            <input type="text" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>
        
        <div class="input-group">
            <label>Description:</label>
            <textarea name="description" rows="10" cols="20"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>
        
        <div class="input-group">
            <label>Category:</label>
            <input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>
        </div>
        
        <div class="input-group">
            <label>Stock:</label>
            <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
        </div>
        
        <div class="buttons">
            <button type="submit" name="submit">Submit</button>
        </div>
    </form>
</div>
</body>
</html>
