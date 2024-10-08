<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Car Accessories Shop - Admin Dashboard</title>
  <style>
    /* General Styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Arial', sans-serif;
    }

    body {
        display: flex;
        min-height: 100vh;
        background-color: #f4f4f9;
        color: #333;
    }

    h1, h2 {
        margin-bottom: 20px;
    }

    .container {
        padding: 20px;
        width: 100%;
        margin: 20px auto;
    }

    a {
        text-decoration: none;
        color: #fff;
    }

    /* Sidebar Styles */
    .sidebar {
        width: 250px;
        background-color: #2c3e50;
        padding: 20px;
        color: #fff;
        position: fixed;
        height: 100%;
        left: 0;
        top: 0;
    }

    .sidebar h2 {
        margin-bottom: 40px;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
    }

    .sidebar ul {
        list-style: none;
        padding-left: 0;
    }

    .sidebar ul li {
        margin-bottom: 20px;
    }

    .sidebar ul li a {
        color: #dcdde1;
        font-size: 18px;
        transition: 0.3s;
        display: block;
        padding: 10px;
    }

    .sidebar ul li a:hover {
        background-color: #576574;
        border-radius: 5px;
    }

    /* Main Content Styles */
    .main-content {
        margin-left: 250px;
        padding: 20px;
        width: calc(100% - 250px);
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .header h1 {
        font-size: 30px;
        color: #1e272e;
    }

    .logout a {
        padding: 10px 20px;
        background-color: #ff4757;
        color: #fff;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .logout a:hover {
        background-color: #e84118;
    }

    /* Table Styles */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    table th, table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #f1f1f1;
    }

    table th {
        background-color: #273c75;
        color: #fff;
        text-transform: uppercase;
    }

    table tr:hover {
        background-color: #f1f1f1;
    }

    table td img {
        border-radius: 5px;
        object-fit: cover;
    }

    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tbody tr:nth-child(odd) {
        background-color: #fff;
    }

    tbody tr:hover {
        background-color: #e1e1e1;
    }

    tbody td {
        font-size: 16px;
    }

    td:first-child, th:first-child {
        text-align: center;
    }

    td:last-child, th:last-child {
        text-align: center;
    }

    /* Button Styles */

    .btn {
        padding: 8px 12px;
        border: none;
        color: #fff;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        margin: 5px;
    }

    .btn-add {
        background-color: #27ae60;
    }

    .btn-edit {
        margin: 5px;
        background-color: #f39c12;
    }

    .btn-delete {
        margin: 5px;
        background-color: #e74c3c;
    }

    /* Print Styles */
    @media print {
        /* Hide unnecessary elements during printing */
        body * {
            visibility: hidden;
        }

        /* Show only the table and the content within the container */
        .container, .container * {
            visibility: visible;
        }

        .container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        /* Hide the print button */
        .print-btn {
            display: none;
        }

        /* Hide sidebar and headers */
        .sidebar, .header {
            display: none;
        }
    }

    /* Responsive Design */
    @media screen and (max-width: 768px) {
        .sidebar {
            width: 200px;
        }

        .main-content {
            margin-left: 200px;
            width: calc(100% - 200px);
        }

        table th, table td {
            font-size: 14px;
            padding: 10px;
        }

        .sidebar h2 {
            font-size: 20px;
        }

        .sidebar ul li a {
            font-size: 16px;
        }
    }

    @media screen and (max-width: 480px) {
        .sidebar {
            width: 100px;
        }

        .main-content {
            margin-left: 100px;
            width: calc(100% - 100px);
        }

        .sidebar h2 {
            font-size: 16px;
        }

        .sidebar ul li a {
            font-size: 14px;
        }

        .header h1 {
            font-size: 24px;
        }

        table th, table td {
            font-size: 12px;
        }
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

    <?php
   require '../db.php';
    // Fetch inventory data
    $inventoryQuery = "SELECT * FROM products"; // Replace 'products' with your table name
    $inventoryResult = $conn->query($inventoryQuery);
    ?>

    <div class="container">
        <h1>Inventory</h1>

        <!-- Add New Product Button -->
        <a href="add_product.php" class="btn btn-add">Add New Product</a>

        <!-- Print Button -->
        <button class="btn btn-add print-btn" onclick="window.print()">Print Table</button>

        <!-- Inventory Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Rating</th>
                    <th>Original Price</th>
                    <th>Discount (%)</th>
                    <th>Description</th>
                    <th>Material</th>
                    <th>Care</th>
                    <th>Sold By</th>
                    <th>Feedback (%)</th>
                    <th>Total Products</th>
                    <th>Warranty</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Check if any products are returned
                if ($inventoryResult->num_rows > 0) {
                    // Loop through each product and display in table rows
                    while ($row = $inventoryResult->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['category']}</td>";
                        echo "<td>{$row['price']}</td>";
                        echo "<td>{$row['rating']}</td>";
                        echo "<td>{$row['original_price']}</td>";
                        echo "<td>{$row['discount_percentage']}</td>";
                        echo "<td>{$row['description']}</td>";
                        echo "<td>{$row['material']}</td>";
                        echo "<td>{$row['care']}</td>";
                        echo "<td>{$row['sold_by']}</td>";
                        echo "<td>{$row['positive_feedback_percentage']}</td>";
                        echo "<td>{$row['total_products']}</td>";
                        echo "<td>{$row['warranty']}</td>";

                        // For images, if stored as BLOB in the database
                        if ($row['image']) {
                            echo "<td><img src='data:image/jpeg;base64," . base64_encode($row['image']) . "' alt='Product Image' style='width:100px;height:100px;'/></td>";
                        } else {
                            echo "<td>No Image</td>";
                        }

                        // Edit and Delete Buttons
                        echo "<td>
                                <a href='edit_product.php?id={$row['id']}' class='btn btn-edit'>Edit</a>
                                <a href='delete_product.php?id={$row['id']}' class='btn btn-delete' onclick='return confirm(\"Are you sure you want to delete this product?\");'>Delete</a>
                              </td>";

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='16'>No products found in inventory.</td></tr>";
                }
                ?>
            </tbody>
        </table>
     
    </div>
  </div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
