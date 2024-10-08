<?php
session_start();
require '../db.php'; // Include your database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login-Signup/login.php");
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Retrieve shipping details
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'N/A';
$address = isset($_GET['address']) ? htmlspecialchars($_GET['address']) : 'N/A';
$phone = isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : 'N/A';
$payment_method = isset($_GET['payment_method']) ? htmlspecialchars($_GET['payment_method']) : 'COD'; // Default to COD

// Set shipping and billing addresses
$shipping_address = $address; // Assuming shipping address is the same as provided address
$billing_address = $address;   // Assuming billing address is the same as provided address

$order_date = new DateTime();
$order_date_formatted = $order_date->format('Y-m-d H:i:s'); // Current date and time
$delivery_status = "Processing";
$payment_status = "Pending"; // For COD, payment is pending until delivery

// Calculate total amount by summing cart items
$cart_query = $conn->prepare("SELECT SUM(products.price * cart_items.quantity) AS total_amount FROM cart_items 
                              JOIN products ON cart_items.product_id = products.id 
                              WHERE cart_items.user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_result = $cart_query->get_result();
$cart_row = $cart_result->fetch_assoc();

// Check if the total amount is NULL or 0
$total_amount = $cart_row['total_amount'] ?? 0; // Default to 0 if NULL

if ($total_amount <= 0) {
    // No items in cart or total is 0
    echo "No items found in your cart. Please add items to your cart before checking out.";
    exit();
}

// Prepare to insert order details into the orders table
$stmt1 = $conn->prepare("INSERT INTO orders(`user_id`, `customer_name`, `order_date`, `delivery_status`, `total_amount`, `payment_method`, `payment_status`, `shipping_address`, `billing_address`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Bind parameters, including customer name, order date, etc.
$stmt1->bind_param("issssssss", $user_id, $name, $order_date_formatted, $delivery_status, $total_amount, $payment_method, $payment_status, $shipping_address, $billing_address);

if ($stmt1->execute()) {
    // Get the last inserted order ID
    $order_id = $stmt1->insert_id;

    // Retrieve all cart items for the user
    $cart_items_query = $conn->prepare("SELECT cart_items.*, products.price AS product_price FROM cart_items 
                                        JOIN products ON cart_items.product_id = products.id 
                                        WHERE cart_items.user_id = ?");
    $cart_items_query->bind_param("i", $user_id);
    $cart_items_query->execute();
    $cart_items_result = $cart_items_query->get_result();

    // Insert each cart item into the order_items table
    $insert_item_stmt = $conn->prepare("INSERT INTO order_items (`order_id`, `product_id`, `quantity`, `price`) VALUES (?, ?, ?, ?)");

    while ($row = $cart_items_result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        $price = $row['product_price']; // Use product price from products table

        // Bind the order ID, product ID, quantity, and price for each cart item
        $insert_item_stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);

        if (!$insert_item_stmt->execute()) {
            echo "Error inserting order item: " . $insert_item_stmt->error;
        }
    }

    // Close the item insertion statement
    $insert_item_stmt->close();

    // Remove items from the cart after successful order
    $delete_cart_stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $delete_cart_stmt->bind_param("i", $user_id);
    if (!$delete_cart_stmt->execute()) {
        echo "Error removing items from cart: " . $delete_cart_stmt->error;
    }

    // Set a success message in the session
    $_SESSION['success_message'] = "Thank you for your order! Your payment will be collected upon delivery.";
} else {
    echo "Error inserting order details: " . $stmt1->error;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Successful</title>
</head>
<body>
    <h2>Order Successful!</h2>
    <p>Thank you for your order. Your payment will be collected upon delivery (Cash on Delivery).</p>
    <a href="../index.html">Continue Shopping</a>
</body>
</html>
