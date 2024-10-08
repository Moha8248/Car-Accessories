<?php
session_start();
include '../db.php';

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch cart items for the user, including product images
$sql = "SELECT products.id, products.name, products.price, products.image, cart_items.quantity 
        FROM cart_items 
        JOIN products ON cart_items.product_id = products.id 
        WHERE cart_items.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f8f8;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #3a6ea5;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        img {
            width: 50px; /* Adjust size as needed */
            height: auto;
            border-radius: 4px;
        }
        .buttons {
            display: flex;
            justify-content: flex-end;
            margin: 20px 0;
        }
        .button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
            font-size: 16px;
        }
        .buy-button {
            background-color: #1abc9c;
            color: white;
            transition: background-color 0.3s;
        }
        .buy-button:hover {
            background-color: #16a085;
        }
        .remove-button {
            background-color: #e74c3c;
            color: white;
            transition: background-color 0.3s;
        }
        .remove-button:hover {
            background-color: #c0392b;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>

<h2>Your Cart</h2>

<?php if (isset($_SESSION['success_message'])): ?>
    <div class="message success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error_message'])): ?>
    <div class="message error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Image</th>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $total_price = 0;
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><img src='data:image/jpeg;base64," . base64_encode($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "' /></td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>₹" . htmlspecialchars($row['price']) . "</td>";
            echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
            echo "<td>
                    <form method='post' action='remove_from_cart.php'>
                        <input type='hidden' name='product_id' value='" . htmlspecialchars($row['id']) . "'>
                        <button type='submit' class='button remove-button'><i class='fas fa-trash'></i> Remove</button>
                    </form>
                  </td>";
            echo "</tr>";
            $total_price += $row['price'] * $row['quantity'];
        }
        ?>
    </tbody>
</table>

<h3>Total Price: ₹<?php echo $total_price; ?></h3>

<div class="buttons">
    <form method='post' action='process_shipping.php'>
        <button type='submit' class='button buy-button'><i class='fas fa-credit-card'></i> Buy Now</button>
    </form>
</div>

</body>
</html>
