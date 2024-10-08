<?php
// Start session to access user ID
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to book services.");
}

// Database connection
require'db.php';
// Get current user ID
$user_id = $_SESSION['user_id'];

// Insert Booking
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service = $_POST['service'];
    $date = $_POST['date'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, service, booking_date) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $service, $date);

    if ($stmt->execute()) {
        echo "<script>alert('New booking created successfully');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Fetch User's Bookings
$bookings = [];
$sql = "SELECT * FROM bookings WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - MOHAN CAR ACCESSORIES</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Root variables for color scheme and styles */
        :root {
            --primary-color: #007bff;
            --secondary-color: #ffc107;
            --background-color: #f8f9fa;
            --text-color: #333;
            --button-hover-color: #0056b3;
            --font-family: "Roboto", sans-serif;
            --border-radius: 12px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: var(--font-family);
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        .navbar {
            background-color: var(--primary-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .navbar .logo {
            font-size: 28px;
            font-weight: 700;
            color: var(--secondary-color);
            transition: color 0.3s ease-in-out;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            margin: 0;
            padding: 0;
        }

        .navbar ul li {
            margin: 0 20px;
            position: relative;
        }

        .navbar ul li a {
            text-decoration: none;
            color: #ffffff;
            font-size: 16px;
            font-weight: 500;
            padding: 5px;
            transition: color 0.3s ease-in-out;
        }

        .navbar ul li a:hover {
            color: var(--secondary-color);
        }

        header {
            background-color: var(--primary-color);
            color: #fff;
            text-align: center;
            padding: 40px 20px;
        }

        .container {
            width: 80%;
            margin: auto;
            padding: 20px;
        }

        .content-section {
            background-color: #fff;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .service-icons {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-top: 20px;
        }

        .service-box {
            flex: 0 1 calc(33% - 20px);
            background-color: #fff;
            margin: 10px;
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s;
        }

        .service-box:hover {
            transform: translateY(-5px);
        }

        .service-box h3 {
            margin: 0;
            color: var(--primary-color);
        }

        .booking-form {
            margin: 40px 0;
            padding: 30px;
            background-color: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .booking-form h3 {
            margin-bottom: 20px;
        }

        .booking-form label {
            display: block;
            margin: 10px 0 5px;
        }

        .booking-form select,
        .booking-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .booking-form button {
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .booking-form button:hover {
            background-color: var(--button-hover-color);
        }

        .booked-services {
            margin-top: 40px;
            background-color: #fff;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
        }

        .booked-services table {
            width: 100%;
            border-collapse: collapse;
        }

        .booked-services th, .booked-services td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .booked-services th {
            background-color: var(--primary-color);
            color: #fff;
        }

        footer {
            background-color: var(--primary-color);
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-top: 30px;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .service-box {
                flex: 0 1 calc(50% - 20px);
            }

            .navbar ul {
                flex-direction: column;
            }

            .navbar ul li {
                margin: 5px 0;
            }
        }

        @media (max-width: 480px) {
            .service-box {
                flex: 0 1 100%;
            }
        }
    </style>
</head>
<body>

<!-- Start Header/Navigation -->
<div class="navbar">
    <div class="logo">MOHAN CAR ACCESSORIES</div>
    <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="User/view_products.php">Shop</a></li>
        <li><a href="aboutus.html">About Us</a></li>
        <li><a class="active" href="#">Services</a></li>
        <li><a href="contactus.php">Contact Us</a></li>
    </ul>
    <div class="icons">
        <a href="redirect.php" title="Profile"><i class="fas fa-user"></i></a>
        <a href="User/cart.php" title="Shopping Cart"><i class="fas fa-shopping-cart"></i></a>
        <a href="Login-Signup/logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
    </div>
</div>
<!-- End Header/Navigation -->

<header>
    <h1>Our Services</h1>
</header>

<div class="container">
    <div class="content-section">
        <h2>What We Offer</h2>
        <div class="service-icons">
            <div class="service-box">
                <h3>Engine Repair</h3>
                <p>Comprehensive engine diagnostics and repairs.</p>
            </div>
            <div class="service-box">
                <h3>Oil Change</h3>
                <p>Quick and efficient oil change services.</p>
            </div>
            <div class="service-box">
                <h3>Tire Rotation</h3>
                <p>Ensure even wear on your tires with regular rotation.</p>
            </div>
            <div class="service-box">
                <h3>Brake Inspection</h3>
                <p>Thorough brake checks to ensure your safety.</p>
            </div>
            <div class="service-box">
                <h3>Lighting Upgrades</h3>
                <p>Upgrade your vehicle's lighting for better visibility.</p>
            </div>
            <div class="service-box">
                <h3>Routine Maintenance</h3>
                <p>Keep your vehicle running smoothly with regular maintenance.</p>
            </div>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="booking-form">
        <h3>Book a Service</h3>
        <form method="POST" action="">
            <label for="service">Select Service:</label>
            <select id="service" name="service" required>
                <option value="Engine Repair">Engine Repair</option>
                <option value="Oil Change">Oil Change</option>
                <option value="Tire Rotation">Tire Rotation</option>
                <option value="Brake Inspection">Brake Inspection</option>
                <option value="Lighting Upgrades">Lighting Upgrades</option>
                <option value="Routine Maintenance">Routine Maintenance</option>
                <option value="Custom Installation">Custom Installation</option>
            </select>

            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" required>

            <button type="submit">Book Service</button>
        </form>
    </div>

    <!-- Display User's Booked Services -->
    <div class="booked-services">
        <h3>Your Booked Services</h3>
        <?php if (count($bookings) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Service</th>
                        <th>Booking Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['service']); ?></td>
                            <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no booked services.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; 2024 MOHAN CAR ACCESSORIES. All Rights Reserved.</p>
</footer>

<!-- Font Awesome for Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
    // Add your JavaScript here if needed
</script>

</body>
</html>
