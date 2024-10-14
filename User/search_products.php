<?php
include '../db.php'; // Include your DB connection

$searchQuery = "";
if (isset($_GET['search'])) {
    $searchQuery = $_GET['search'];
}

$sql = "SELECT * FROM products WHERE name LIKE '%" . $conn->real_escape_string($searchQuery) . "%' OR category LIKE '%" . $conn->real_escape_string($searchQuery) . "%' ORDER BY category";
$result = $conn->query($sql);

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

        // Display each product card
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
    echo "<p>No products found. Try searching with different terms or check back later as we update our inventory frequently!</p>";
}

$conn->close();
?>
