<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - View Contact Messages</title>
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
      <li><a href="salesreport.php" >Sales Report</a></li> 
      <li><a href="#"class="active" >View Contact Messages</a></li> 
      <li><a href="view_servicebooking.php" >View Service Booking</a></li> 
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="header">
      <h1>Contact Messages</h1>
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

    <!-- Contact Messages Table -->
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Submitted On</th>
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

                // Query to get all contact messages along with the username, with optional date filtering
                $sql = "SELECT cm.id, cm.user_id, cm.name, cm.email, cm.subject, cm.message, cm.created_at, u.username 
                        FROM contact_messages cm
                        JOIN users u ON cm.user_id = u.id";

                // Apply date filter if provided
                if (!empty($startDate) && !empty($endDate)) {
                    $sql .= " WHERE cm.created_at BETWEEN '$startDate' AND '$endDate'";
                }

                $sql .= " ORDER BY cm.created_at DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) :
                    while ($row = $result->fetch_assoc()) :
                        ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= $row['user_id']; ?></td>
                            <td><?= htmlspecialchars($row['username']); ?></td>
                            <td><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><?= htmlspecialchars($row['subject']); ?></td>
                            <td><?= nl2br(htmlspecialchars($row['message'])); ?></td>
                            <td><?= $row['created_at']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="8">No messages found.</td>
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
