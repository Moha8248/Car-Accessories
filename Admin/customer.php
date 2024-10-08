<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Car Accessories Shop - Customers</title>
  <link rel="stylesheet" href="Dashboard.css"> <!-- Use the same Dashboard CSS for consistency -->
</head>
<body>

  <?php
        require '../db.php';

  // Fetch customer data along with profiles
  $customersQuery = "
    SELECT users.id, users.username, users.email, users.created_at, 
           user_profiles.address, user_profiles.phone, user_profiles.date_of_birth 
    FROM users
    LEFT JOIN user_profiles ON users.id = user_profiles.user_id 
    WHERE users.username != 'Admin'
    ORDER BY users.created_at DESC"; // Fetching all customers except admin
  $customersResult = $conn->query($customersQuery);
  ?>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Admin Dashboard</h2>
    <ul>
      <li><a href="Dashboard.php">Dashboard</a></li>
      <li><a href="inventory.php">Inventory</a></li>
      <li><a href="orders.php">Orders</a></li>
      <li><a href="#" class="active">Customers</a></li> <!-- Active Customers Page -->
      <li><a href="salesreport.php" >Sales Report</a></li> 
      <li><a href="view_contactmsg.php" >View Contact Messages</a></li>
      <li><a href="view_servicebooking.php" >View Service Booking</a></li> 
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <div class="header">
      <h1>Customers</h1>
      <div class="logout">
        <a href="../Login-Signup/logout.php">Logout</a>
      </div>
    </div>

    <!-- Customers Table -->
    <div class="recent-customers">
      <h2>All Customers</h2>
      <table>
        <thead>
          <tr>
            <th>Customer ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Address</th>
            <th>Phone</th>
            <th>Date of Birth</th>
            <th>Created At</th> <!-- New Column for created_at -->
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Check if customers exist
          if ($customersResult->num_rows > 0) {
            // Loop through each customer and display in the table
            while ($customer = $customersResult->fetch_assoc()) {
              echo "<tr>
                      <td>#{$customer['id']}</td>
                      <td>{$customer['username']}</td>
                      <td>{$customer['email']}</td>
                      <td>{$customer['address']}</td>
                      <td>{$customer['phone']}</td>
                      <td>{$customer['date_of_birth']}</td>
                      <td>{$customer['created_at']}</td>
                      <td>
                        <a href='delete_customer.php?customer_id={$customer['id']}' class='delete-btn'>Delete</a>
                      </td>
                    </tr>";
            }
          } else {
            echo "<tr><td colspan='8'>No customers found.</td></tr>"; // Updated colspan
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
