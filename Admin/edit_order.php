<?php
require '../db.php';

// Check if order_id is set in the URL
if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']); // Ensure order_id is an integer

    // Fetch the order details
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if order exists
    if ($result->num_rows === 1) {
        $order = $result->fetch_assoc();
    } else {
        die("Order not found.");
    }

    $stmt->close();
} else {
    die("Invalid order ID.");
}

// Update order on form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $user_id = intval($_POST['user_id']);
    $customer_name = htmlspecialchars($_POST['customer_name']);
    $order_date = $_POST['order_date']; // Consider sanitizing if needed
    $delivery_status = htmlspecialchars($_POST['delivery_status']);
    $total_amount = floatval($_POST['total_amount']);
    $payment_method = htmlspecialchars($_POST['payment_method']);
    $payment_status = htmlspecialchars($_POST['payment_status']);
    $shipping_address = htmlspecialchars($_POST['shipping_address']);
    $billing_address = htmlspecialchars($_POST['billing_address']);

    // Update the order in the database
    $stmt = $conn->prepare("UPDATE orders SET user_id = ?, customer_name = ?, order_date = ?, delivery_status = ?, total_amount = ?, payment_method = ?, payment_status = ?, shipping_address = ?, billing_address = ? WHERE order_id = ?");
    $stmt->bind_param("issssssssi", $user_id, $customer_name, $order_date, $delivery_status, $total_amount, $payment_method, $payment_status, $shipping_address, $billing_address, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order updated successfully!');</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link rel="stylesheet" href="Dashboard.css"> <!-- Link to your CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .main-content {
            max-width: 600px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="number"],
        input[type="datetime-local"],
        select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="main-content">
    <div class="header">
        <h1>Edit Order</h1>
    </div>

    <form method="POST" action="">
        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
        <label for="user_id">User ID:</label>
        <input type="number" name="user_id" value="<?php echo $order['user_id']; ?>" required><br>

        <label for="customer_name">Customer Name:</label>
        <input type="text" name="customer_name" value="<?php echo $order['customer_name']; ?>" required><br>

        <label for="order_date">Order Date:</label>
        <input type="datetime-local" name="order_date" value="<?php echo date('Y-m-d\TH:i', strtotime($order['order_date'])); ?>" required><br>

        <label for="delivery_status">Delivery Status:</label>
        <select name="delivery_status" required>
            <option value="Processing" <?php echo $order['delivery_status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
            <option value="Shipped" <?php echo $order['delivery_status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
            <option value="Delivered" <?php echo $order['delivery_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
        </select><br>

        <label for="total_amount">Total Amount:</label>
        <input type="number" step="0.01" name="total_amount" value="<?php echo $order['total_amount']; ?>" required><br>

        <label for="payment_method">Payment Method:</label>
        <input type="text" name="payment_method" value="<?php echo $order['payment_method']; ?>" required><br>

        <label for="payment_status">Payment Status:</label>
        <input type="text" name="payment_status" value="<?php echo $order['payment_status']; ?>" required><br>

        <label for="shipping_address">Shipping Address:</label>
        <input type="text" name="shipping_address" value="<?php echo $order['shipping_address']; ?>" required><br>

        <label for="billing_address">Billing Address:</label>
        <input type="text" name="billing_address" value="<?php echo $order['billing_address']; ?>" required><br>

        <input type="submit" value="Update Order">
    </form>
</div>

</body>
</html>
