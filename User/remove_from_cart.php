<?php
session_start();
include '../db.php'; // Ensure your DB connection is included

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "You must be logged in to remove items from the cart.";
    header("Location: cart.php");
    exit;
}

// Get the user ID and product ID from the session and POST request
$user_id = $_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

// Prepare the SQL statement to delete the item from the cart
$sql = "DELETE FROM cart_items WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);

// Execute the statement and check for success
if ($stmt->execute()) {
    $_SESSION['success_message'] = "Item removed from cart successfully.";
} else {
    $_SESSION['error_message'] = "Error removing item from cart. Please try again.";
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Redirect back to the cart page
header("Location: cart.php");
exit;
?>
