<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "car_accessories";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data and sanitize to avoid SQL injection
    $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
    $category = isset($_POST['category']) ? mysqli_real_escape_string($conn, $_POST['category']) : '';
    $price = isset($_POST['price']) ? mysqli_real_escape_string($conn, $_POST['price']) : '';
    $rating = isset($_POST['rating']) ? mysqli_real_escape_string($conn, $_POST['rating']) : '';
    $original_price = isset($_POST['original_price']) ? mysqli_real_escape_string($conn, $_POST['original_price']) : NULL;
    $discount_percentage = isset($_POST['discount_percentage']) ? mysqli_real_escape_string($conn, $_POST['discount_percentage']) : NULL;
    $description = isset($_POST['description']) ? mysqli_real_escape_string($conn, $_POST['description']) : NULL;
    $material = isset($_POST['material']) ? mysqli_real_escape_string($conn, $_POST['material']) : NULL;
    $care = isset($_POST['care']) ? mysqli_real_escape_string($conn, $_POST['care']) : NULL;
    $sold_by = isset($_POST['sold_by']) ? mysqli_real_escape_string($conn, $_POST['sold_by']) : NULL;
    $positive_feedback_percentage = isset($_POST['positive_feedback_percentage']) ? mysqli_real_escape_string($conn, $_POST['positive_feedback_percentage']) : NULL;
    $total_products = isset($_POST['total_products']) ? mysqli_real_escape_string($conn, $_POST['total_products']) : NULL;
    $warranty = isset($_POST['warranty']) ? mysqli_real_escape_string($conn, $_POST['warranty']) : NULL;

    // Retrieve image data
    if (isset($_FILES['image']['tmp_name'])) {
        $image = $_FILES['image']['tmp_name'];
        $imageData = addslashes(file_get_contents($image)); // Prepare image data
    } else {
        $imageData = NULL;
    }

    // Insert product data into the database
    $sql = "INSERT INTO products (name, category, price, rating, original_price, discount_percentage, description, material, care, sold_by, positive_feedback_percentage, total_products, warranty, image) 
            VALUES ('$name', '$category', '$price', '$rating', '$original_price', '$discount_percentage', '$description', '$material', '$care', '$sold_by', '$positive_feedback_percentage', '$total_products', '$warranty', '$imageData')";

    if ($conn->query($sql) === TRUE) {
        echo "New product added successfully!";
        echo '<br><a href="add_product.php"><button>Add Another Product</button></a>';
        echo '<br><a href="inventory.php"><button>View Inventory</button></a>';
        
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        echo '<br><a href="inventory.php"><button>Go TO Inventry</button></a>';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td {
            padding: 10px;
            vertical-align: top;
        }

        table td:first-child {
            text-align: right;
            padding-right: 15px;
            font-weight: bold;
        }

        table input, table select, table button, table textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        table button {
            background-color: #1abc9c;
            color: white;
            border: none;
            cursor: pointer;
        }

        table button:hover {
            background-color: #16a085;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }

        .form-actions {
            text-align: center;
            padding-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Add New Product</h2>
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <table>
            <tr>
                <td><label for="name">Product Name</label></td>
                <td><input type="text" id="name" name="name" required></td>
            </tr>
            <tr>
                <td><label for="category">Category</label></td>
                <td>
                    <select id="category" name="category" required>
                        <option value="Car Electronics">Car Electronics</option>
                        <option value="Interior Accessories">Interior Accessories</option>
                        <option value="Exterior Accessories">Exterior Accessories</option>
                        <option value="Car Care">Car Care</option>
                        <option value="Performance Parts">Performance Parts</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="price">Price ($)</label></td>
                <td><input type="number" step="0.01" id="price" name="price" required></td>
            </tr>
            <tr>
                <td><label for="original_price">Original Price ($)</label></td>
                <td><input type="number" step="0.01" id="original_price" name="original_price" required></td>
            </tr>
            <tr>
                <td><label for="discount_percentage">Discount (%)</label></td>
                <td><input type="number" id="discount_percentage" name="discount_percentage" required></td>
            </tr>
            <tr>
                <td><label for="rating">Rating (1-5)</label></td>
                <td>
                    <select id="rating" name="rating" required>
                        <option value="1">1 Star</option>
                        <option value="2">2 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="5">5 Stars</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label for="description">Description</label></td>
                <td><textarea id="description" name="description" rows="4"></textarea></td>
            </tr>
            <tr>
                <td><label for="material">Material</label></td>
                <td><input type="text" id="material" name="material"></td>
            </tr>
            <tr>
                <td><label for="care">Care Instructions</label></td>
                <td><input type="text" id="care" name="care"></td>
            </tr>
            <tr>
                <td><label for="sold_by">Sold By</label></td>
                <td><input type="text" id="sold_by" name="sold_by"></td>
            </tr>
            <tr>
                <td><label for="positive_feedback_percentage">Positive Feedback (%)</label></td>
                <td><input type="number" id="positive_feedback_percentage" name="positive_feedback_percentage"></td>
            </tr>
            <tr>
                <td><label for="total_products">Total Products Sold</label></td>
                <td><input type="number" id="total_products" name="total_products"></td>
            </tr>
            <tr>
                <td><label for="warranty">Warranty</label></td>
                <td><input type="text" id="warranty" name="warranty"></td>
            </tr>
            <tr>
                <td><label for="image">Upload Image</label></td>
                <td><input type="file" id="image" name="image" required></td>
            </tr>
            <tr class="form-actions">
                <td colspan="2">
                    <button type="submit">Add Product</button>
                </td>
            </tr>
        </table>
    </form>
</div>

</body>
</html>
