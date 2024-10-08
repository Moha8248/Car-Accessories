<?php
session_start();
include '../db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login-Signup/login.php"); // Redirect to login page
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Handle profile creation if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $image = null;

    // Validate phone number (10 digits)
    if (!preg_match('/^\d{10}$/', $phone)) {
        echo json_encode(['status' => 'error', 'message' => 'Phone number must be exactly 10 digits.']);
        exit();
    }

    // Validate date of birth (must be at least 15 years old)
    $dob = new DateTime($date_of_birth);
    $today = new DateTime();
    $age = $today->diff($dob)->y;
    if ($age < 15) {
        echo json_encode(['status' => 'error', 'message' => 'You must be at least 15 years old.']);
        exit();
    }

    // Check if the user uploaded an image
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }

    // Insert the data into the database
    $insert_sql = "INSERT INTO user_profiles (user_id, image, address, phone, date_of_birth) 
                   VALUES (?, ?, ?, ?, ?) 
                   ON DUPLICATE KEY UPDATE address = ?, phone = ?, date_of_birth = ?";
    $insert_stmt = $conn->prepare($insert_sql);

    if ($image) {
        // If the image is provided
        $insert_stmt->bind_param("isssssss", $user_id, $image, $address, $phone, $date_of_birth, $address, $phone, $date_of_birth);
    } else {
        // If no image is provided
        $insert_stmt->bind_param("issssss", $user_id, $address, $phone, $date_of_birth, $address, $phone, $date_of_birth);
    }

    if ($insert_stmt->execute()) {
        echo '<script type="text/javascript">';
        echo 'window.location.href = "login.html";'; // Redirect to login.html on success
        echo '</script>';
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error creating profile. Please try again.']);
    }

    $conn->close();
    exit(); // Exit after processing the form
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f5;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007AFF;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #007AFF;
        }
        .form-group input,
        .form-group textarea {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group textarea {
            resize: vertical;
            height: 80px;
        }
        .form-group input[type="file"] {
            padding: 3px;
        }
        .alert {
            margin: 10px 0;
            text-align: center;
            display: none;
        }
        .error {
            color: red;
        }
        .message {
            color: green;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #007AFF;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Create Your Profile</h2>

    <div class="alert" id="alert"></div>

    <form id="profileForm" enctype="multipart/form-data">
        <div class="form-group">
            <label for="image">Profile Image</label>
            <input type="file" name="image" id="image" accept="image/*">
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea name="address" id="address" required></textarea>
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" required>
        </div>

        <div class="form-group">
            <label for="date_of_birth">Date of Birth</label>
            <input type="date" name="date_of_birth" id="date_of_birth" required>
        </div>

        <button type="button" id="submitButton">Create Profile</button>
    </form>
</div>

<script>
    document.getElementById('submitButton').addEventListener('click', function () {
        const formData = new FormData(document.getElementById('profileForm'));
        const phone = document.getElementById('phone').value;
        const address = document.getElementById('address').value;
        const dateOfBirth = document.getElementById('date_of_birth').value;
        const dob = new Date(dateOfBirth);
        const today = new Date();
        let age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
            age--;
        }

        // Validate phone (must be 10 digits)
        if (!/^\d{10}$/.test(phone)) {
            showAlert('Phone number must be exactly 10 digits.', 'error');
            return;
        }

        // Validate address (must have at least two lines)
        if (address.split('\n').length < 2) {
            showAlert('Address must have at least two lines.', 'error');
            return;
        }

        // Validate age (must be 15+ years)
        if (age < 15) {
            showAlert('You must be at least 15 years old.', 'error');
            return;
        }

        // Send data to the server via AJAX
        fetch('profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            // Handle redirection from PHP
            if (data.includes('window.location.href')) {
                window.location.href = 'login.html';
            } else {
                const response = JSON.parse(data);
                if (response.status === 'success') {
                    showAlert(response.message, 'message');
                    document.getElementById('profileForm').reset();
                } else {
                    showAlert(response.message, 'error');
                }
            }
        })
        .catch((error) => {
            showAlert('Error submitting form. Please try again.', 'error');
        });
    });

    function showAlert(message, type) {
        const alertDiv = document.getElementById('alert');
        alertDiv.textContent = message;
        alertDiv.className = `alert ${type}`;
        alertDiv.style.display = 'block';
        setTimeout(() => {
            alertDiv.style.display = 'none';
        }, 3000);
    }
</script>

</body>
</html>
