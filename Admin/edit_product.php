<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_accessories"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if product ID is provided in the URL
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product data based on ID
    $sql = "SELECT * FROM products WHERE id = $product_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Get product data
        $product = $result->fetch_assoc();
    } else {
        echo "No product found!";
        exit;
    }
} else {
    echo "Invalid product ID!";
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $rating = $_POST['rating'];
    $original_price = $_POST['original_price'];
    $discount_percentage = $_POST['discount_percentage'];
    $description = $_POST['description'];
    $material = $_POST['material'];
    $care = $_POST['care'];
    $sold_by = $_POST['sold_by'];
    $positive_feedback_percentage = $_POST['positive_feedback_percentage'];
    $total_products = $_POST['total_products'];
    $warranty = $_POST['warranty'];

    // Image handling
    if (!empty($_FILES['image']['name'])) {
        $image = addslashes(file_get_contents($_FILES['image']['tmp_name']));
        $image_sql = ", image='$image'";
    } else {
        $image_sql = '';
    }

    // Update the product in the database
    $update_sql = "UPDATE products SET 
                    name='$name', 
                    category='$category', 
                    price='$price', 
                    rating='$rating', 
                    original_price='$original_price',
                    discount_percentage='$discount_percentage',
                    description='$description',
                    material='$material',
                    care='$care',
                    sold_by='$sold_by',
                    positive_feedback_percentage='$positive_feedback_percentage',
                    total_products='$total_products',
                    warranty='$warranty'
                    $image_sql
                  WHERE id=$product_id";

    if ($conn->query($update_sql) === TRUE) {
        echo "Product updated successfully!";
        // Redirect to inventory page after successful update
        header('Location: inventory.php');
        exit;
    } else {
        echo "Error updating product: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            padding: 20px;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .form-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group input[type="file"] {
            padding: 5px;
        }
        .form-group textarea {
            resize: vertical;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: #27ae60;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group button:hover {
            background-color: #218c54;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1>Edit Product</h1>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Product Name</label>
            <input type="text" name="name" id="name" value="<?php echo $product['name']; ?>" required>
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <input type="text" name="category" id="category" value="<?php echo $product['category']; ?>" required>
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="text" name="price" id="price" value="<?php echo $product['price']; ?>" required>
        </div>
        <div class="form-group">
            <label for="rating">Rating</label>
            <input type="text" name="rating" id="rating" value="<?php echo $product['rating']; ?>" required>
        </div>
        <div class="form-group">
            <label for="original_price">Original Price</label>
            <input type="text" name="original_price" id="original_price" value="<?php echo $product['original_price']; ?>" required>
        </div>
        <div class="form-group">
            <label for="discount_percentage">Discount Percentage</label>
            <input type="text" name="discount_percentage" id="discount_percentage" value="<?php echo $product['discount_percentage']; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4" required><?php echo $product['description']; ?></textarea>
        </div>
        <div class="form-group">
            <label for="material">Material</label>
            <input type="text" name="material" id="material" value="<?php echo $product['material']; ?>" required>
        </div>
        <div class="form-group">
            <label for="care">Care Instructions</label>
            <input type="text" name="care" id="care" value="<?php echo $product['care']; ?>" required>
        </div>
        <div class="form-group">
            <label for="sold_by">Sold By</label>
            <input type="text" name="sold_by" id="sold_by" value="<?php echo $product['sold_by']; ?>" required>
        </div>
        <div class="form-group">
            <label for="positive_feedback_percentage">Positive Feedback Percentage</label>
            <input type="text" name="positive_feedback_percentage" id="positive_feedback_percentage" value="<?php echo $product['positive_feedback_percentage']; ?>" required>
        </div>
        <div class="form-group">
            <label for="total_products">Total Products</label>
            <input type="text" name="total_products" id="total_products" value="<?php echo $product['total_products']; ?>" required>
        </div>
        <div class="form-group">
            <label for="warranty">Warranty</label>
            <input type="text" name="warranty" id="warranty" value="<?php echo $product['warranty']; ?>" required>
        </div>
        <div class="form-group">
            <label for="image">Product Image (Optional)</label>
            <input type="file" name="image" id="image">
        </div>
        <div class="form-group">
            <button type="submit">Update Product</button>
        </div>
    </form>
</div>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
