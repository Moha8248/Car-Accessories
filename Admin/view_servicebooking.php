<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - View Service Booking Details</title>
  <link rel="stylesheet" href="Dashboard.css"> <!-- Use the same Dashboard CSS for consistency -->
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 20px;
    }
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

    .filter-form input, .filter-form button {
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

    .sidebar {
      width: 200px;
      float: left;
    }

    .main-content {
      margin-left: 220px; /* Adjust for sidebar width */
    }

    .container {
      max-width: 1200px;
      margin: auto;
    }

    h1 {
      color: #3a6ea5;
    }
    tr:hover {
      background-color: #f1f1f1;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Admin Dashboard</h2>
    <ul>
      <li><a href="Dashboard.php">Dashboard</a></li>
      <li><a href="inventory.php">Inventory</a></li>
      <li><a href="orders.php">Orders</a></li>
      <li><a href="customer.php">Customers</a></li>
      <li><a href="salesreport.php">Sales Report</a></li>
      <li><a href="view_contactmsg.php">View Contact Messages</a></li>
      <li><a href="#" class="active">View Service Bookings</a></li> 
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="header">
      <h1>Service Booking Details</h1>
      <div class="logout">
        <a href="../Login-Signup/logout.php">Logout</a>
      </div>
    </div>

    <!-- Filter Form -->
    <div class="filter-form">
      <form method="GET" action="">
          <label for="start_date">Start Date:</label>
          <input type="date" id="start_date" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">

          <label for="end_date">End Date:</label>
          <input type="date" id="end_date" name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">

          <button type="submit">Apply Filter</button>
      </form>
    </div>

    <!-- Service Booking Table -->
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User ID</th>
                    <th>Username</th> <!-- Added Username Column -->
                    <th>Service</th>
                    <th>Booking Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require '../db.php';
                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                // Initialize filter variables
                $startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
                $endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

                // Query to get all service bookings with optional date filtering
                // Adjusting to join with users table for username
                $sql = "SELECT b.id AS booking_id, b.user_id, u.username, b.service, b.booking_date 
                        FROM bookings b
                        JOIN users u ON b.user_id = u.id"; // Ensure this matches your users table structure

                // Apply date filter if provided
                if (!empty($startDate) && !empty($endDate)) {
                    $sql .= " WHERE b.booking_date BETWEEN '$startDate' AND '$endDate'";
                }

                $sql .= " ORDER BY b.booking_date DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) :
                    while ($row = $result->fetch_assoc()) :
                        ?>
                        <tr>
                            <td><?= $row['booking_id']; ?></td>
                            <td><?= $row['user_id']; ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td> <!-- Display Username -->
                            <td><?= htmlspecialchars($row['service']); ?></td>
                            <td><?= htmlspecialchars($row['booking_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5">No service bookings found.</td> <!-- Updated colspan to match new table structure -->
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Print Button -->
        <button class="print-btn" onclick="window.print()">Print Report</button>
    </div>
  </div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
