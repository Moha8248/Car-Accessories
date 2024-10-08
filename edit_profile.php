<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: Login-Signup/login.php"); // Redirect to login page
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch user profile details
$sql = "SELECT up.image, up.address, up.phone, up.date_of_birth 
        FROM user_profiles up 
        WHERE up.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $date_of_birth = trim($_POST['date_of_birth']);
    $image = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }
// Update profile information
$update_sql = "INSERT INTO user_profiles (user_id, image, address, phone, date_of_birth) 
               VALUES (?, ?, ?, ?, ?) 
               ON DUPLICATE KEY UPDATE image = ?, address = ?, phone = ?, date_of_birth = ?";
$update_stmt = $conn->prepare($update_sql);

// Bind parameters
$update_stmt->bind_param("issssssss", 
    $user_id, 
    $image, 
    $address, 
    $phone, 
    $date_of_birth, 
    $image, 
    $address, 
    $phone, 
    $date_of_birth
);

if ($update_stmt->execute()) {
    $_SESSION['success_message'] = "Profile updated successfully.";
    header("Location: profile.php"); // Redirect to view profile
    exit();
} else {
    $_SESSION['error_message'] = "Error updating profile. Please try again.";
}


}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="date"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #1abc9c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #16a085;
        }
        .message {
            color: green;
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
        img {
            max-width: 100%;
            height: auto;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Profile</h2>

    <?php if (isset($_SESSION['success_message'])): ?>
        <p class="message"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="image">Profile Image</label>
            <input type="file" name="image" id="image" accept="image/*">
            <?php if (!empty($profile['image'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($profile['image']); ?>" alt="Profile Image"/>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" name="address" id="address" value="<?php echo isset($profile['address']) ? htmlspecialchars($profile['address']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" value="<?php echo isset($profile['phone']) ? htmlspecialchars($profile['phone']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label for="date_of_birth">Date of Birth</label>
            <input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo isset($profile['date_of_birth']) ? $profile['date_of_birth'] : ''; ?>" required>
        </div>

        <button type="submit">Update Profile</button>
    </form>
</div>

</body>
</html>
