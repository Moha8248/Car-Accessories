<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Car Accessories Shop - Admin Dashboard</title>
  <link rel="stylesheet" href="Dashboard.css">
  <style>
    /* Basic styles for the print button */
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

    /* Print-specific styles */
    @media print {
      /* Hide everything by default during print */
      body * {
        visibility: hidden;
      }

      /* Only show the recent orders table */
      .recent-orders, .recent-orders * {
        visibility: visible;
      }

      /* Ensure the table is positioned correctly during print */
      .recent-orders {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
      }

      /* Hide the print button during printing */
      .print-btn {
        display: none;
      }
    }
  </style>
</head>
<body>
<?php
// Database connection
require '../db.php';

// Fetch customers count
$customersQuery = "SELECT COUNT(*) as customers FROM users WHERE users.username != 'Admin'";
$customersResult = $conn->query($customersQuery);
$totalCustomers = $customersResult->fetch_assoc()['customers'] ?? 0;

// Fetch total sales
$salesQuery = "SELECT SUM(amount) as total_sales FROM tbl_payment";
$salesResult = $conn->query($salesQuery);
$totalSales = $salesResult->fetch_assoc()['total_sales'] ?? 0;

// Fetch total products count
$productsQuery = "SELECT COUNT(*) as total_products FROM products"; // Assuming 'inventory' table contains products
$productsResult = $conn->query($productsQuery);
$totalProducts = $productsResult->fetch_assoc()['total_products'] ?? 0;

// Fetch recent payments (orders) from tbl_payment
$recentPaymentsQuery = "SELECT * FROM tbl_payment ORDER BY created_at DESC LIMIT 5"; // Fetch last 5 payments
$recentPaymentsResult = $conn->query($recentPaymentsQuery);
?>


  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Admin Dashboard</h2>
    <ul>
      <li><a href="#">Dashboard</a></li>
      <li><a href="inventory.php">Inventory</a></li>
      <li><a href="orders.php">Orders</a></li>
      <li><a href="customer.php">Customers</a></li>
      <li><a href="salesreport.php" >Sales Report</a></li> 
      <li><a href="view_contactmsg.php" >View Contact Messages</a></li>
      <li><a href="view_servicebooking.php" >View Service Booking</a></li> 
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content"> 
    <div class="header">
      <h1>Welcome, Admin</h1>
      <div class="logout">
        <a href="../Login-Signup/logout.php">Logout</a>
      </div>
    </div>

   <!-- Overview Cards -->
  <div class="overview">
    <div class="card">
      <h3>Total Sales</h3>
      <p>₹<?php echo number_format($totalSales, 2); ?></p>
    </div>
    <div class="card">
      <h3>Customers</h3>
      <p><?php echo $totalCustomers; ?> Registered</p>
    </div>
    <div class="card">
      <h3>Total Products</h3>
      <p><?php echo $totalProducts; ?> Available</p> <!-- Display the total number of products -->
    </div>
  </div>

    <!-- Recent Orders Table -->
    <div class="recent-orders">
      <h2>Recent Payments</h2>
      <table>
        <thead>
          <tr>
            <th>Payment ID</th>
            <th>User ID</th>
            <th>Date</th>
            <th>Status</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Loop through the recent payments and display them in the table
          while ($payment = $recentPaymentsResult->fetch_assoc()) {
            $paymentDate = date('Y-m-d H:i:s', strtotime($payment['created_at'])); // Format the date
            echo "<tr>
                    <td>#{$payment['id']}</td>
                    <td>{$payment['user_id']}</td>
                    <td>{$paymentDate}</td>
                    <td>{$payment['payment_status']}</td>
                    <td>₹{$payment['amount']}</td>
                  </tr>";
          }
          ?>
        </tbody>
      </table>

      <button class="print-btn" onclick="window.print()">Print</button>
    </div>
  </div>

</body>
</html>
