<?php
// Start the session
session_start();

// Database connection parameters
require'db.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in the session
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO contact_messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $name, $email, $subject, $message);

    // Execute the statement
    if ($stmt->execute()) {
        $success_message = "Message sent successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us - MOHAN CAR ACCESSORIES</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #3a6ea5;
            --secondary-color: #ffd700;
            --background-color: #f4f4f4;
            --text-color: #333;
            --font-family: "Roboto", sans-serif;
            --border-radius: 8px;
        }

        body {
            font-family: var(--font-family);
            margin: 0;
            padding: 0;
            background-color: var(--background-color);
            color: var(--text-color);
        }

        /* Navbar */
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

        .navbar .logo:hover {
            color: #fff;
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
            font-weight: 500;
            padding: 5px;
            transition: color 0.3s ease-in-out;
        }

        .navbar ul li a.active {
            border-bottom: 2px solid var(--secondary-color);
        }

        .navbar ul li a:hover {
            color: var(--secondary-color);
        }

        .icons i {
            color: #fff;
            transition: color 0.3s ease-in-out;
        }

        .icons i:hover {
            color: var(--secondary-color);
        }

        .navbar .icons {
            font-size: 22px;
            display: flex;
            align-items: center;
        }

        .navbar .icons i {
            font-size: 20px;
            margin-left: 20px;
            color: var(--secondary-color);
        }

        /* Header */
        header {
            background-color: var(--primary-color);
            color: #fff;
            text-align: center;
            padding: 40px 20px;
            position: relative;
            overflow: hidden;
        }

        header h1 {
            font-size: 36px;
            font-weight: 700;
            margin: 0;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            font-size: 28px;
            color: var(--primary-color);
            position: relative;
        }

        h2::after {
            content: "";
            display: block;
            width: 50px;
            height: 4px;
            background-color: var(--secondary-color);
            margin-top: 8px;
        }

        .content-section {
            background-color: #fff;
            padding: 30px;
            margin-bottom: 30px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .contact-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 12px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: var(--border-radius);
            font-size: 16px;
        }

        .contact-form textarea {
            resize: vertical;
        }

        .contact-form button {
            grid-column: span 2;
            padding: 15px;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: var(--border-radius);
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease-in-out;
        }

        .contact-form button:hover {
            background-color: var(--secondary-color);
            color: #333;
        }

        .contact-info {
            margin-top: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .info-box {
            background-color: #fff;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .info-box i {
            font-size: 40px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .info-box p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
        }

        /* Footer */
        footer {
            background-color: var(--primary-color);
            color: #fff;
            text-align: center;
            padding: 20px;
            margin-top: 30px;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        footer p {
            margin: 0;
            font-size: 14px;
        }

        /* Media Queries */
        @media (max-width: 768px) {
            .contact-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Start Header/Navigation -->
    <div class="navbar">
        <div class="logo">MOHAN CAR ACCESSORIES</div>
        <ul>
            <li><a href="index.html"> Home </a></li>
            <li><a href="User/view_products.php"> Shop </a></li>
            <li><a href="aboutus.html"> About Us </a></li>
            <li><a href="services.php"> Services </a></li>
            <li><a class="active" href="#"> Contact Us </a></li>
        </ul>
        <div class="icons">
            <a href="redirect.php" title="Profile"><i class="fas fa-user"></i></a>
            <a href="User/cart.php" title="Shopping Cart"><i class="fas fa-shopping-cart"></i></a>
            <a href="Login-Signup/logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
        </div>
    </div>
    <!-- End Header/Navigation -->

    <header>
        <h1>Contact Us</h1>
    </header>

    <div class="container">
        <div class="content-section">
            <h2>Get In Touch</h2>
            <p>We're here to help you! Fill out the form below to send us a message.</p>
            
            <?php if (isset($success_message)) : ?>
                <div style="color: green;"><?= $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)) : ?>
                <div style="color: red;"><?= $error_message; ?></div>
            <?php endif; ?>

            <form action="" method="POST" class="contact-form">
                <input type="text" name="name" placeholder="Your Name" required />
                <input type="email" name="email" placeholder="Your Email" required />
                <input type="text" name="subject" placeholder="Subject" required />
                <textarea name="message" rows="6" placeholder="Your Message" required></textarea>
                <button type="submit">Send Message</button>
            </form>
        </div>

        <div class="content-section contact-info">
            <div class="info-box">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Address</h3>
                <p>1/108 MOHAN CAR ACCESSORIES KKP, Karur, Tamilnadu 621313</p>
            </div>
            <div class="info-box">
                <i class="fas fa-phone"></i>
                <h3>Phone</h3>
                <p>+91 8248661097</p>
            </div>
            <div class="info-box">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <p>support@mohancaraccessories.com</p>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 MOHAN CAR ACCESSORIES. All rights reserved.</p>
    </footer>
</body>
</html>
