<?php
// Database connection details
$host = "localhost";   // Your database host (e.g., localhost)
$user = "root";        // Your database username
$pass = "";            // Your database password
$dbname = "car_accessories"; // Your database name

// Establish a database connection
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Check if the connection was successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the product ID from the query string (if it exists)
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Fetch product details if the product ID is provided and valid
if ($product_id > 0) {
    $query = "SELECT * FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    } else {
        $product = null; // Product not found
    }
} else {
    $product = null; // Invalid product ID
}

// Close the database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Navbar */
        .navbar {
            background-color: #2c3e50;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
            color: #f39c12;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .navbar ul li {
            margin: 0 20px;
        }

        .navbar ul li a {
            text-decoration: none;
            color: #ffffff;
            font-size: 16px;
            transition: color 0.3s;
        }

        .navbar ul li a:hover {
            color: #f39c12;
        }

        .navbar .icons {
            font-size: 22px;
            display: flex;
            align-items: center;
        }

        .navbar .icons i {
            font-size: 20px;
            margin-left: 20px;
            color: #f39c12;
            transition: color 0.3s;
        }

        .navbar .icons i:hover {
            color: #ffffff;
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            transition: box-shadow 0.3s;
        }

        .container:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .product {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }

        /* Product Image */
        .product .image {
            flex: 1;
            max-width: 500px;
        }

        .product .image img {
            width: 100%;
            border-radius: 10px;
            transition: transform 0.3s ease-in-out;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product .image img:hover {
            transform: scale(1.05);
        }

        /* Product Details */
        .product .details {
            flex: 2;
        }

        .product .details h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .product .details .price {
            font-size: 28px;
            font-weight: 700;
            color: #e74c3c;
            margin-bottom: 10px;
        }

        .product .details .price .original-price {
            text-decoration: line-through;
            font-size: 18px;
            color: #95a5a6;
            margin-right: 15px;
        }

        .product .details .price .discount {
            font-size: 16px;
            color: #27ae60;
        }

        .product .buttons {
            display: flex;
            gap: 20px;
            margin: 30px 0;
        }

        .product .buttons .button {
            padding: 12px 20px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            text-transform: uppercase;
            font-weight: 500;
        }

        .product .buttons .wishlist {
            background-color: #bdc3c7;
            color: #333;
        }

        .product .buttons .wishlist:hover {
            background-color: #95a5a6;
        }

        .product .buttons .add-to-cart {
            background-color: #27ae60;
            color: #ffffff;
            transition: background-color 0.3s;
        }

        .product .buttons .add-to-cart:hover {
            background-color: #219150;
        }

        .product .details .product-details h2 {
            font-size: 22px;
            font-weight: 600;
            margin-top: 40px;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .product .details .product-details p {
            font-size: 16px;
            color: #7f8c8d;
            line-height: 1.6;
        }

        /* Sold By Section */
        .sold-by {
            margin-top: 30px;
            background-color: #f8f8f8;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .sold-by h2 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .sold-by p {
            font-size: 16px;
            color: #7f8c8d;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .navbar {
                padding: 15px 20px;
            }

            .navbar ul li {
                margin: 0 10px;
            }

            .container {
                padding: 15px;
            }

            .product {
                flex-direction: column;
            }

            .product .image, .product .details {
                flex: 1;
                max-width: 100%;
            }

            .product .details h1 {
                font-size: 26px;
            }

            .product .details .price {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <!-- Your HTML structure here -->
    <div class="navbar">
        <div class="logo">MOHAN CAR ACCESSORIES</div>
        <ul>
            <li><a href="../index.html">Home</a></li>
            <li><a class="active" href="#">Shop</a></li>
            <li><a href="../aboutus.html">About Us</a></li>
            <li><a href="../services.html">Services</a></li>
            <li><a href="../contactus.html">Contact Us</a></li>
        </ul>
    </div>

    <div class="container">
        <div class="product">
            <!-- Check if the product exists before rendering details -->
            <?php if ($product): ?>
                <div class="image">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                </div>

                <div class="details">
                    <h1><?php echo htmlspecialchars($product['name']); ?></h1>

                    <div class="price">
                        <span class="original-price">₹<?php echo number_format($product['original_price'], 2); ?></span>
                        <span class="discount">(<?php echo $product['discount_percentage']; ?>% off)</span>
                        <span>₹<?php echo number_format($product['price'], 2); ?></span>
                    </div>
                        <form method="post" action="add_to_cart.php">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <input type="number" name="quantity" value="1" min="1" max="100" style="width: 60px; margin-right: 10px;">
                            <button type="submit" class="button add-to-cart">Add to Cart</button>
                        </form>
                    </div>

                    <div class="product-details">
                        <h2>PRODUCT DETAILS</h2>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <h2>MATERIAL & CARE</h2>
                        <p><?php echo htmlspecialchars($product['material']); ?></p>
                        <p><?php echo htmlspecialchars($product['care']); ?></p>
                    </div>

                    <div class="sold-by">
                        <h2>SOLD BY</h2>
                        <p>Sold By: <?php echo htmlspecialchars($product['sold_by']); ?></p>
                        <p>Positive Feedbacks: <?php echo isset($product['positive_feedback_percentage']) ? $product['positive_feedback_percentage'] . '% Positive Feedback' : 'No feedback available'; ?></p>
                        <p>Total Products Sales: <?php echo number_format($product['total_products']); ?> + Products</p>
                        <p>Warranty: <?php echo htmlspecialchars($product['warranty']); ?></p>
                    </div>
                </div>
            <?php else: ?>
                <h1>Product Not Found</h1>
                <p>We're sorry, but the product you're looking for cannot be found. Please try again later or browse other products.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
