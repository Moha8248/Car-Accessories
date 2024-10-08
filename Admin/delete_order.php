<?php
// Database connection
require '../db.php';

// Check if 'order_id' is provided
if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    // Prepare and execute the delete query
    $deleteOrderQuery = "DELETE FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($deleteOrderQuery);
    $stmt->bind_param("i", $orderId);

    if ($stmt->execute()) {
        // Redirect back to orders page after successful deletion
        header("Location: orders.php?message=Order+Deleted+Successfully");
        exit();
    } else {
        echo "Error deleting order: " . $conn->error;
    }
} else {
    echo "Order ID not provided.";
}

$conn->close();
?>
