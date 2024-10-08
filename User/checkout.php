<?php
session_start();
require '../db.php'; // Make sure to include your database connection
require_once 'stripe-php-master/init.php'; // Include Stripe PHP library

// Initialize Stripe
\Stripe\Stripe::setApiKey('sk_test_51Q2Zi0Lzf1Nkvl1Jkp5MvnrDv7FyVnDJSMmPrs0GAPxYwGaLcbJRsSSzO3ZtZXJUldwAmzOUso5QaGWfhusOeaKt00gSVTacvk'); // Replace with your Stripe secret key

// Get the user ID from the session
$user_id = $_SESSION['user_id'];
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'N/A';
$address = isset($_GET['address']) ? htmlspecialchars($_GET['address']) : 'N/A';
$phone = isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : 'N/A';
$payment_method = isset($_GET['payment_method']) ? htmlspecialchars($_GET['payment_method']) : 'N/A';


// Fetch cart items for the user
$sql = "SELECT products.id, products.name, products.price, cart_items.quantity 
        FROM cart_items 
        JOIN products ON cart_items.product_id = products.id 
        WHERE cart_items.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total_price = 0;
$line_items = []; // To hold product line items for Stripe
while ($row = $result->fetch_assoc()) {
    $total_price += $row['price'] * $row['quantity'];
    $line_items[] = [
        'price_data' => [
            'currency' => 'inr', // Change according to your currency
            'product_data' => [
                'name' => $row['name'],
            ],
            'unit_amount' => $row['price'] * 100, // Amount in cents
        ],
        'quantity' => $row['quantity'],
    ];
}if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get shipping details from the session or POST request
    $name = $_SESSION['shipping_details']['name'];
    $address = $_SESSION['shipping_details']['address'];
    $phone = $_SESSION['shipping_details']['phone'];
    $payment_method = $_SESSION['shipping_details']['payment_method'];

    // Create a Checkout Session
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $line_items,
        'mode' => 'payment',
        'success_url' => 'http://localhost/Car-Accessories/User/checkout_success.php?session_id={CHECKOUT_SESSION_ID}' .
                         '&name=' . urlencode($name) .
                         '&address=' . urlencode($address) .
                         '&phone=' . urlencode($phone) .
                         '&payment_method=' . urlencode($payment_method), // Include the shipping details in the success URL
        'cancel_url' => 'http://localhost/Car-Accessories/User/checkout_cancel.php', // Change to your cancel URL
    ]);

    // Redirect to Stripe Checkout
    header("Location: " . $session->url);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        /* Add your styles here */
    </style>
</head>
<body>

<h2>Checkout</h2>

<h3>Total Price: ₹<?php echo $total_price; ?></h3>

<div class="buttons">
    <form method='post'>
        <button type='submit' class='button buy-button'><i class='fas fa-credit-card'></i> Buy Now</button>
    </form>
</div>

</body>
</html>
