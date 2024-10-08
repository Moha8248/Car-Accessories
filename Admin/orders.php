<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Accessories Shop - Orders</title>
    <link rel="stylesheet" href="Dashboard.css"> <!-- Use the same Dashboard CSS for consistency -->
    <style>
        /* Additional Print Styles */
        @media print {
            body * {
                visibility: hidden;
            }

            .recent-orders, .recent-orders * {
                visibility: visible;
            }

            .recent-orders {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
            }

            .print-btn {
                display: none;
            }

            /* Optionally hide sidebar and header */
            .sidebar, .header {
                display: none;
            }
        }

        /* Add some basic styles for the print button and table */
        .print-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .print-btn:hover {
            background-color: #218838;
        }

        /* Table Styles */
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

        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form input, .filter-form select, .filter-form button {
            padding: 8px;
            margin-right: 10px;
            font-size: 14px;
        }

        .filter-form button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        .filter-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<?php
// Database connection
require '../db.php';

// Initialize variables for filter inputs
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$selectedProduct = isset($_GET['product_id']) ? $_GET['product_id'] : '';

// Fetch product names for dropdown
$productQuery = "SELECT id, name FROM products";
$productResult = $conn->query($productQuery);

// Build the query with optional filters
$ordersQuery = "SELECT orders.*, order_items.quantity, order_items.price, products.name AS product_name 
                FROM orders 
                LEFT JOIN order_items ON orders.order_id = order_items.order_id 
                LEFT JOIN products ON order_items.product_id = products.id
                WHERE orders.delivery_status != 'Delivered'";

// Apply date filter if provided
if (!empty($startDate) && !empty($endDate)) {
    $ordersQuery .= " AND orders.order_date BETWEEN '$startDate' AND '$endDate'";
}

// Apply product name filter if provided
if (!empty($selectedProduct)) {
    $ordersQuery .= " AND order_items.product_id = '$selectedProduct'";
}

// Order by date
$ordersQuery .= " ORDER BY orders.order_date DESC";
$ordersResult = $conn->query($ordersQuery);
?>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Admin Dashboard</h2>
    <ul>
        <li><a href="Dashboard.php">Dashboard</a></li>
        <li><a href="inventory.php">Inventory</a></li>
        <li><a href="#">Orders</a></li> <!-- Active Orders Page -->
        <li><a href="customer.php">Customers</a></li>
        <li><a href="salesreport.php" >Sales Report</a></li> 
        <li><a href="view_contactmsg.php" >View Contact Messages</a></li>
        <li><a href="view_servicebooking.php" >View Service Booking</a></li> 
    </ul>
</div>

<!-- Main Content -->
<div class="main-content">
    <div class="header">
        <h1>Orders</h1>
        <div class="logout">
            <a href="../Login-Signup/logout.php">Logout</a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="filter-form">
        <form method="GET" action="">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo $startDate; ?>" placeholder="Start Date">

            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" value="<?php echo $endDate; ?>" placeholder="End Date">
            
            <!-- Dropdown for Product Name -->
            <label for="product_id">Select Product:</label>
            <select id="product_id" name="product_id">
                <option value="">Select Product</option>
                <?php
                if ($productResult->num_rows > 0) {
                    while ($product = $productResult->fetch_assoc()) {
                        $selected = $selectedProduct == $product['id'] ? 'selected' : '';
                        echo "<option value='{$product['id']}' $selected>{$product['name']}</option>";
                    }
                }
                ?>
            </select>

            <button type="submit">Apply Filter</button>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="recent-orders">
        <h2>All Orders</h2>
        
        <!-- Print Button -->
        <button class="print-btn" onclick="window.print()">Print Orders</button>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Order Date</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Total Amount</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($ordersResult->num_rows > 0) {
                    while ($order = $ordersResult->fetch_assoc()) {
                        echo "<tr>
                                <td>#{$order['order_id']}</td>
                                <td>{$order['customer_name']}</td>
                                <td>{$order['order_date']}</td>
                                <td>{$order['product_name']}</td>
                                <td>{$order['quantity']}</td>
                                <td>₹{$order['price']}</td>
                                <td>{$order['delivery_status']}</td>
                                <td>₹{$order['total_amount']}</td>
                                <td>{$order['created_at']}</td>
                                <td>
                                    <a href='edit_order.php?order_id={$order['order_id']}' class='edit-btn'>Edit</a>
                                    <a href='delete_order.php?order_id={$order['order_id']}' class='delete-btn'>Delete</a>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No orders found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Close the database connection
$conn->close();
?>
</body>
</html>
