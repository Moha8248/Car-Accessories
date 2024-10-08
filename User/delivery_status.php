<?php
session_start();
require '../db.php'; // Ensure your DB connection is included

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login-Signup/login.php"); // Redirect to login if not logged in
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch the user's orders and their delivery statuses along with product image, quantity, price, and payment status
$sql = "SELECT orders.order_id, orders.delivery_status, orders.payment_status, products.name AS product_name, products.image AS product_image, order_items.quantity, order_items.price
        FROM orders 
        JOIN order_items ON orders.order_id = order_items.order_id 
        JOIN products ON order_items.product_id = products.id 
        WHERE orders.user_id = ? 
        ORDER BY orders.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // Bind the user ID
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Status</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #3a6ea5;
            color: white;
        }
        .status {
            font-weight: bold;
            text-transform: uppercase;
        }
        .status.processing {
            color: #f39c12;
        }
        .status.shipped {
            color: #3498db;
        }
        .status.delivered {
            color: #27ae60;
        }
        .no-orders {
            text-align: center;
            font-size: 18px;
        }
        .product-image {
            width: 80px;
            height: auto;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Your Order Status</h2>

        <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Paid</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                    <td><img src="../images/<?php echo htmlspecialchars($row['product_image']); ?>" alt="Product Image" class="product-image"></td>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($row['price'], 2)); ?></td>
                    <td class="status <?php echo strtolower($row['delivery_status']); ?>">
                        <?php echo htmlspecialchars($row['delivery_status']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($row['payment_status'] == 'paid' ? 'Paid' : 'Not Paid'); ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="no-orders">You have no orders yet.</p>
        <?php endif; ?>

        <p><a href="../index.html">Back to Shop</a></p>
    </div>

</body>
</html>

<?php
// Close the connection
$conn->close();
?>
