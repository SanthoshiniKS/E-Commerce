<?php
if (isset($_POST['submit']) && isset($_FILES['my_image'])) {
    include "db.php";

    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Retrieve product details from the form
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);

    // Debugging: Print the received values (Remove in production)
    echo "Name: $name, Price: $price, Description: $description, Category: $category, Stock: $stock";

    // Retrieve image upload details
    $img_name = $_FILES['my_image']['name'];
    $img_size = $_FILES['my_image']['size'];
    $tmp_name = $_FILES['my_image']['tmp_name'];
    $error = $_FILES['my_image']['error'];

    // Check for file upload errors
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
                    // Insert product details into the database
                    $sql = "INSERT INTO products (name, price, image_url, description, category, stock) 
                            VALUES ('$name', '$price', '$new_img_name', '$description', '$category', '$stock')";

                    if (mysqli_query($conn, $sql)) {
                        // Redirect to the view page on success
                        echo "<script>alert('Product added successfully!'); window.location.href='admin.php';</script>";
                        exit();
                    } else {
                        // Database error
                        $em = "Database error: " . mysqli_error($conn);
                        echo "<script>alert('$em'); window.history.back();</script>";
                        exit();
                    }
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
    } else {
        $em = "Unknown file upload error occurred!";
        echo "<script>alert('$em'); window.history.back();</script>";
        exit();
    }
} else {
    $em = "Invalid form submission.";
    echo "<script>alert('$em'); window.history.back();</script>";
    exit();
}
