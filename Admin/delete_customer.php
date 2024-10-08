<?php
// Database connection
require '../db.php';

// Check if 'customer_id' is provided
if (isset($_GET['customer_id'])) {
    $customerId = $_GET['customer_id'];

    // Delete from the user_profiles table (if profile data exists)
    $deleteProfileQuery = "DELETE FROM user_profiles WHERE user_id = ?";
    $stmtProfile = $conn->prepare($deleteProfileQuery);
    $stmtProfile->bind_param("i", $customerId);
    $stmtProfile->execute();

    // Delete from the users table
    $deleteUserQuery = "DELETE FROM users WHERE id = ?";
    $stmtUser = $conn->prepare($deleteUserQuery);
    $stmtUser->bind_param("i", $customerId);

    if ($stmtUser->execute()) {
        // Redirect back to customers page after successful deletion
        header("Location: customers.php?message=Customer+Deleted+Successfully");
        exit();
    } else {
        echo "Error deleting customer: " . $conn->error;
    }
} else {
    echo "Customer ID not provided.";
}

$conn->close();
?>
