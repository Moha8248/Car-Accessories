<?php
session_start();
require '../db.php'; // Ensure this is your correct database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id']; // Ensure the user is logged in
    
    // Check if all required fields are set
    $name = isset($_POST['name']) ? $_POST['name'] : null;
    $address = isset($_POST['address']) ? $_POST['address'] : null;
    $phone = isset($_POST['phone']) ? $_POST['phone'] : null;
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;

    // Ensure all required fields are filled before proceeding
    if ($name && $address && $phone && $payment_method) {
        // Store the shipping details in session to access them later
        $_SESSION['shipping_details'] = [
            'name' => $name,
            'address' => $address,
            'phone' => $phone,
            'payment_method' => $payment_method
        ];

        // Redirect to payment page based on the payment method
        if ($payment_method == 'online') {
            // Redirect with GET parameters
            header("Location: checkout.php?name=" . urlencode($name) . "&address=" . urlencode($address) . "&phone=" . urlencode($phone) . "&payment_method=" . urlencode($payment_method));
            exit();
        } elseif ($payment_method == 'cod') {
            // Redirect with GET parameters
            header("Location: checkout_cod.php?name=" . urlencode($name) . "&address=" . urlencode($address) . "&phone=" . urlencode($phone) . "&payment_method=" . urlencode($payment_method));
            exit();
        }
        
    } else {
        // Handle the error if some fields are missing (optional)
        echo "All fields are required!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        /* General Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f8;
            padding: 20px;
            margin: 0;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            margin-top: 15px;
        }

        input[type="text"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="tel"]:focus,
        select:focus {
            border-color: #1abc9c;
            outline: none;
            box-shadow: 0 0 8px rgba(26, 188, 156, 0.3);
        }

        .btn {
            background-color: #1abc9c;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #16a085;
        }

        .btn:focus {
            outline: none;
        }

        .radio-group {
            display: flex;
            justify-content: space-around;
            margin: 10px 0;
        }

        .radio-group label {
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Checkout</h2>
    <form action="" method="POST">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required placeholder="Enter your full name">

        <label for="address">Shipping Address</label>
        <input type="text" id="address" name="address" required placeholder="Enter your shipping address">

        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number">

        <label for="payment_method">Payment Method</label>
        <div class="radio-group">
            <label><input type="radio" name="payment_method" value="online" required> Online Payment</label>
            <label><input type="radio" name="payment_method" value="cod" required> Cash on Delivery</label>
        </div>

        <button type="submit" class="btn">Proceed to Payment</button>
    </form>
</div>

</body>
</html>
