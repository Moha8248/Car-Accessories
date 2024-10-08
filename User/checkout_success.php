<?php
session_start();
require '../db.php'; // Include your database connection
require_once 'stripe-php-master/init.php'; // Include Stripe PHP library

\Stripe\Stripe::setApiKey('sk_test_51Q2Zi0Lzf1Nkvl1Jkp5MvnrDv7FyVnDJSMmPrs0GAPxYwGaLcbJRsSSzO3ZtZXJUldwAmzOUso5QaGWfhusOeaKt00gSVTacvk'); // Replace with your Stripe secret key

if (!isset($_GET['session_id'])) {
    header("Location: checkout.php"); // Redirect if no session_id
    exit();
}

$session_id = $_GET['session_id'];
$session = \Stripe\Checkout\Session::retrieve($session_id);

// Ensure user is logged in
$user_id = $_SESSION['user_id']; // Ensure this is set

// Retrieve shipping details
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'N/A';
$address = isset($_GET['address']) ? htmlspecialchars($_GET['address']) : 'N/A';
$phone = isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : 'N/A';
$payment_method = isset($_GET['payment_method']) ? htmlspecialchars($_GET['payment_method']) : 'N/A';

// Set shipping and billing addresses
$shipping_address = $address; // Assuming shipping address is the same as provided address
$billing_address = $address;   // Assuming billing address is the same as provided address

$order_date = new DateTime();
$order_date_formatted = $order_date->format('Y-m-d H:i:s'); // Current date and time
$delivery_status = "Processing";
$total_amount = $session->amount_total / 100; // Convert to rupees
$currency_code = $session->currency;
$txn_id = $session->id;
$payment_status = $session->payment_status;
$payment_response = json_encode($session); // Store the entire session response if needed

// Prepare and execute the payment details insertion
$stmt = $conn->prepare("INSERT INTO tbl_payment (`user_id`, `amount`, `currency_code`, `txn_id`, `payment_status`, `payment_response`) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("isssss", $user_id, $total_amount, $currency_code, $txn_id, $payment_status, $payment_response);

if (!$stmt->execute()) {
    echo "Error inserting payment details: " . $stmt->error;
}

// Prepare to insert order details into the orders table
$stmt1 = $conn->prepare("INSERT INTO orders(`user_id`, `customer_name`, `order_date`, `delivery_status`, `total_amount`, `payment_method`, `payment_status`, `shipping_address`, `billing_address`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Bind parameters, including customer_name
$stmt1->bind_param("issssssss", $user_id, $name, $order_date_formatted, $delivery_status, $total_amount, $payment_method, $payment_status, $shipping_address, $billing_address);

if ($stmt1->execute()) {
    // Get the last inserted order ID
    $order_id = $stmt1->insert_id;

    // Retrieve all cart items for the user
    $cart_query = $conn->prepare("SELECT cart_items.*, products.price AS product_price FROM cart_items 
                                  JOIN products ON cart_items.product_id = products.id 
                                  WHERE cart_items.user_id = ?");
    $cart_query->bind_param("i", $user_id);
    $cart_query->execute();
    $cart_result = $cart_query->get_result();

    // Insert each cart item into the order_items table
    $insert_item_stmt = $conn->prepare("INSERT INTO order_items (`order_id`, `product_id`, `quantity`, `price`) VALUES (?, ?, ?, ?)");

    while ($row = $cart_result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        // Use product price from the products table instead of the cart price (if missing in cart)
        $price = isset($row['price']) ? $row['price'] : $row['product_price'];

        // Bind the order ID, product ID, quantity, and price for each cart item
        $insert_item_stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);

        if (!$insert_item_stmt->execute()) {
            echo "Error inserting order item: " . $insert_item_stmt->error;
        }
    }

    // Close the statement
    $insert_item_stmt->close();

    // Remove items from the cart after a successful payment
    $delete_stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $delete_stmt->bind_param("i", $user_id);
    if (!$delete_stmt->execute()) {
        echo "Error removing items from cart: " . $delete_stmt->error;
    }

    // Set a success message in the session
    $_SESSION['success_message'] = "Thank you for your purchase!";
} else {
    echo "Error inserting order details: " . $stmt1->error;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful</title>
</head>
<body>
    <h2>Payment Successful!</h2>
    <p>Thank you for your purchase.</p>
    <a href="../index.html">Continue Shopping</a>
</body>
</html>
