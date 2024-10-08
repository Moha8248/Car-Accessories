<?php
session_start();
include '../db.php'; // Ensure your DB connection file is included

// Handle search functionality
$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

// Fetch products from the database
$sql = "SELECT * FROM products WHERE name LIKE '%" . $conn->real_escape_string($searchQuery) . "%' ORDER BY category";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Accessories Shop</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
       body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }
/* Navbar */
.navbar {
    background-color: #3a6ea5;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 50px;
    color: #ffffff;
}
.navbar .logo {
    font-size: 24px;
    font-weight: bold;
    color: #ffd700;
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
    color: #ffd700; /* Change color on hover */
}
.navbar ul li a.active {
    border-bottom: 2px solid #ffd700;
}
.navbar .search-bar {
    flex-grow: 1;
    margin: 0 20px;
}
.navbar .search-bar input[type="text"] {
    width: 80%;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #ccc;
}
.navbar .search-bar button {
    padding: 10px;
    background-color: #1abc9c;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.icons {
    display: flex;
    align-items: center;
}
.icons a {
    color: #ffffff;
    margin-left: 15px; /* Add spacing between icons */
    text-decoration: none;
}
.icons a:hover {
    color: #ffd700; /* Change color on hover */
}

        /* Container for products */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .categories {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        .category-title {
            width: 100%;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .card {
            background-color: white;
            width: 200px;
            margin: 10px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .card img {
            width: 100%;
            height: auto;
            border-radius: 4px;
        }
        .details {
            margin-top: 10px;
        }
        .details .title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }
        .details .rating i {
            color: #f39c12;
        }
        .details .price {
            margin-top: 10px;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }
        .add-to-cart {
            margin-top: 10px;
            padding: 8px 12px;
            background-color: #1abc9c;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-transform: uppercase;
        }
        .add-to-cart:hover {
            background-color: #16a085;
        }
    </style>
</head>
<body>
    <!-- Start Header/Navigation -->
    <div class="navbar">
        <div class="logo">AutoGear</div>
        <ul>
            <li><a href="../index.html">Home</a></li>
            <li><a class="active" href="#">Shop</a></li>
            <li><a href="../aboutus.html">About Us</a></li>
            <li><a href="../services.php">Services</a></li>
            <li><a href="../contactus.php">Contact Us</a></li>
        </ul>

        <!-- Search Bar in Navbar -->
        <div class="search-bar">
            <form action="view_product.php" method="GET">
                <input type="text" name="search" placeholder="Search for car accessories..." value="<?php echo htmlspecialchars($searchQuery); ?>"/>
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="icons">
            <a href="../redirect.php" title="Profile"><i class="fas fa-user"></i></a>
            <a href="cart.php" title="Shopping Cart"><i class="fas fa-shopping-cart"></i></a>
            <a href="delivery_status.php" title="Delivery Status">
          <i class="fas fa-truck"></i>
        </a>
            <a href="../Login-Signup/logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
    <!-- End Header/Navigation -->

    <div class="container">
        <?php
        if ($result->num_rows > 0) {
            $currentCategory = "";
            while ($row = $result->fetch_assoc()) {
                // Show category heading if it's a new category
                if ($currentCategory !== $row['category']) {
                    if ($currentCategory !== "") {
                        echo "</div>"; // Close previous category div
                    }
                    $currentCategory = $row['category'];
                    echo "<div class='category-title'>" . htmlspecialchars($currentCategory) . "</div>";
                    echo "<div class='categories'>";
                }

                // Display each product card wrapped in a link to product_details.php
                echo "<div class='card'>
                        <a href='product_details.php?product_id=" . htmlspecialchars($row['id']) . "'>
                            <img src='data:image/jpeg;base64," . base64_encode($row['image']) . "' alt='" . htmlspecialchars($row['name']) . "'/>
                            <div class='details'>
                                <div class='title'>" . htmlspecialchars($row['name']) . "</div>
                                <div class='rating'>";

                // Display the product's star rating
                for ($i = 0; $i < $row['rating']; $i++) {
                    echo "<i class='fas fa-star'></i>";
                }

                echo "</div>
                      <div class='price'>₹" . htmlspecialchars($row['price']) . "</div>
                      </div>
                    </a>";

                // Add to Cart form
                echo "<form method='post' action='add_to_cart.php'>
                        <input type='hidden' name='product_id' value='" . htmlspecialchars($row['id']) . "'>
                        <input type='number' name='quantity' value='1' min='1' max='100'>
                        <button type='submit' class='add-to-cart'>Add to Cart</button>
                      </form>
                    </div>";
            }
            echo "</div>"; // Close the last category div
        } else {
            echo "<p>No products found.</p>";
        }

        $conn->close();
        ?>
    </div>

</body>
</html>
