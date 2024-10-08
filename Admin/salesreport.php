<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Car Accessories Shop - Sales Report</title>
  <link rel="stylesheet" href="Dashboard.css"> <!-- Use the same Dashboard CSS for consistency -->
  <style>
    /* Add some basic styles for the print button and table */
    .print-btn {
      margin-top: 20px;
      padding: 10px 20px;
      background-color: #28a745;
      color: white;
      border: none;
      cursor: pointer;
      font-size: 16px;
      border-radius: 4px; /* Rounded corners for the button */
    }

    .print-btn:hover {
      background-color: #218838;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }

    @media print {
      .print-btn, .sidebar, .header {
        display: none; /* Hide the print button, sidebar, and header when printing */
      }
      table {
        width: 100%;
        border: 1px solid black;
      }
      th, td {
        border: 1px solid black;
      }
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

  // Build the sales report query with optional filters
  $salesReportQuery = "SELECT orders.*, order_items.product_id, order_items.price, products.name AS product_name 
                        FROM orders 
                        LEFT JOIN order_items ON orders.order_id = order_items.order_id 
                        LEFT JOIN products ON order_items.product_id = products.id
                        WHERE orders.delivery_status = 'Delivered'";

  // Apply date filter if provided
  if (!empty($startDate) && !empty($endDate)) {
      $salesReportQuery .= " AND orders.order_date BETWEEN '$startDate' AND '$endDate'";
  }

  // Apply product name filter if provided
  if (!empty($selectedProduct)) {
      $salesReportQuery .= " AND order_items.product_id = '$selectedProduct'";
  }

  $salesReportQuery .= " ORDER BY orders.created_at DESC";
  $salesResult = $conn->query($salesReportQuery);
  ?>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Admin Dashboard</h2>
    <ul>
      <li><a href="Dashboard.php">Dashboard</a></li>
      <li><a href="inventory.php">Inventory</a></li>
      <li><a href="orders.php">Orders</a></li>
      <li><a href="customer.php">Customers</a></li>
      <li><a href="#" class="active">Sales Report</a></li> <!-- Active Sales Report Page -->
      <li><a href="view_contactmsg.php" >View Contact Messages</a></li> 
      <li><a href="view_servicebooking.php" >View Service Booking</a></li> 
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="header">
      <h1>Sales Report</h1>
      <div class="logout">
        <a href="../Login-Signup/logout.php">Logout</a>
      </div>
    </div>

    <!-- Filter Form -->
    <div class="filter-form">
      <form method="GET" action="">
          <label for="start_date">Start Date:</label>
          <input type="date" id="start_date" name="start_date" value="<?php echo $startDate; ?>">

          <label for="end_date">End Date:</label>
          <input type="date" id="end_date" name="end_date" value="<?php echo $endDate; ?>">

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

    <!-- Sales Report Table -->
    <div class="sales-report">
      <h2>Delivered Orders</h2>
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer Name</th>
            <th>Order Date</th>
            <th>Product Name</th> <!-- Added Product Name Column -->
            <th>Total Amount</th>
            <th>Delivery Status</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Check if delivered orders exist
          if ($salesResult->num_rows > 0) {
            // Loop through each delivered order and display in the table
            while ($order = $salesResult->fetch_assoc()) {
              echo "<tr>
                      <td>#{$order['order_id']}</td>
                      <td>{$order['customer_name']}</td>
                      <td>{$order['order_date']}</td>
                      <td>{$order['product_name']}</td> <!-- Display Product Name -->
                      <td>₹" . number_format($order['total_amount'], 2) . "</td>
                      <td>{$order['delivery_status']}</td>
                      <td>{$order['created_at']}</td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='7'>No delivered orders found.</td></tr>";
          }
          ?>
        </tbody>
      </table>

      <!-- Print Button -->
      <button class="print-btn" onclick="window.print()">Print Report</button>
    </div>
  </div>

</body>
</html>
