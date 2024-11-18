<?php
session_start();
include "db.php";

$user_id = $_POST['user_id'];
$total_amount = $_POST['total_amount'];
$cart_id = $_POST['cart_id'];
if ($payment_successful) {
    $order_query = "
        INSERT INTO orders (user_id, total_amount, order_date)
        VALUES (?, ?, NOW())
    ";
    $stmt = $conn->prepare($order_query);
    $stmt->bind_param("ii", $user_id, $total_amount);
    $stmt->execute();
    $clear_cart_query = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($clear_cart_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: thank_you.php");
} else {
    echo "Payment failed. Please try again.";
}
?>
