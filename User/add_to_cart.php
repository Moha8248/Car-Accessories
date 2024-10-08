<?php
session_start();
include '../db.php'; // Include your database connection

// Check if user is logged in (assuming you are using sessions for user login)
if (!isset($_SESSION['user_id'])) {
    echo 'Please log in to add items to the cart.';
    exit;
}

$user_id = $_SESSION['user_id'];  // Get user id from session
$product_id = $_POST['product_id']; // Get the product id from the form
$quantity = $_POST['quantity'];  // Get the quantity from the form

// Check if product is already in cart
$query = "SELECT * FROM cart_items WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Product is already in cart, update the quantity
    $update_query = "UPDATE cart_items SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param('iii', $quantity, $user_id, $product_id);
    $stmt_update->execute();
} else {
    // Insert new record into cart
    $insert_query = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param('iii', $user_id, $product_id, $quantity);
    $stmt_insert->execute();
}

header("Location: cart.php");  // Redirect to cart page after adding item
exit();
?>
