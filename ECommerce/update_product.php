<?php
if (isset($_POST['submit'])) {
    include "db.php";

    // Retrieve product details from the form
    $product_id = $_GET['id'];  // Assuming product ID is passed via URL for editing
    if (empty($product_id)) {
        echo "Product ID is not provided!";
        exit();
    }

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);

    // Fetch the existing product details from the database
    $sql = "SELECT * FROM products WHERE id = '$product_id'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);

        // Check if the image has been changed
        if ($_FILES['my_image']['error'] === 0) {
            $img_name = $_FILES['my_image']['name'];
            $img_size = $_FILES['my_image']['size'];
            $tmp_name = $_FILES['my_image']['tmp_name'];
            $error = $_FILES['my_image']['error'];

            if ($error === 0) {
                // Check for file size limit
                if ($img_size > 125000) { // 125 KB
                    $em = "Sorry, your file is too large.";
                    echo "<script>alert('$em'); window.history.back();</script>";
                    exit();
                } else {
                    // Validate the file extension
                    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                    $img_ex_lc = strtolower($img_ex); // Convert to lowercase
                    $allowed_exs = array("jpg", "jpeg", "png");

                    if (in_array($img_ex_lc, $allowed_exs)) {
                        // Generate a unique name for the image
                        $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                        $img_upload_path = 'ProductImages/' . $new_img_name;

                        // Attempt to move the uploaded file
                        if (move_uploaded_file($tmp_name, $img_upload_path)) {
                            // Update image flag to true
                            $update_image = ", image_url = '$new_img_name'";
                        } else {
                            $em = "Failed to upload image.";
                            echo "<script>alert('$em'); window.history.back();</script>";
                            exit();
                        }
                    } else {
                        $em = "You can't upload files of this type. Allowed types: jpg, jpeg, png.";
                        echo "<script>alert('$em'); window.history.back();</script>";
                        exit();
                    }
                }
            }
        }

        // Prepare update SQL query
        $update_sql = "UPDATE products SET name = '$name', price = '$price', description = '$description', category = '$category', stock = '$stock' $update_image WHERE id = '$product_id'";

        if (mysqli_query($conn, $update_sql)) {
            echo "<script>alert('Product updated successfully!'); window.location.href='admin.php';</script>";
            exit();
        } else {
            $em = "Database error: " . mysqli_error($conn);
            echo "<script>alert('$em'); window.history.back();</script>";
            exit();
        }
    } else {
        echo "Product not found in the database.";
        exit();
    }
} else {
    $em = "Invalid form submission.";
    echo "<script>alert('$em'); window.history.back();</script>";
    exit();
}
?>
