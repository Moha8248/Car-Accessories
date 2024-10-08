<?php
require '../db.php';

// Check if 'product_id' is provided
if (isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];

    // Prepare and execute the delete query
    $deleteProductQuery = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($deleteProductQuery);
    $stmt->bind_param("i", $productId);

    if ($stmt->execute()) {
        // Redirect back to products page after successful deletion
        header("Location: inventory.php?message=Product+Deleted+Successfully");
        exit();
    } else {
        echo "Error deleting product: " . $conn->error;
    }
} else {
    echo "Product ID not provided.";
}

$conn->close();
?>
