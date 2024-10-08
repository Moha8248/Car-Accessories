<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login-Signup/login.php"); // Redirect to login page
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch user profile details
$sql = "SELECT u.username, u.email, up.image, up.address, up.phone, up.date_of_birth 
        FROM users u 
        LEFT JOIN user_profiles up ON u.id = up.user_id 
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
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
        .profile-image {
            max-width: 100px;
            border-radius: 50%;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        .profile-detail {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #fafafa;
        }
        .button-group {
            text-align: center;
            margin-top: 20px;
        }
        a {
            padding: 10px 15px;
            background-color: #1abc9c;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a:hover {
            background-color: #16a085;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>User Profile</h2>
    
    <div class="form-group">
        <label for="image">Profile Image</label>
        <?php if (!empty($profile['image'])): ?>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($profile['image']); ?>" alt="Profile Image" class="profile-image"/>
        <?php else: ?>
            <p>No profile image uploaded.</p>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="username">Username</label>
        <div class="profile-detail"><?php echo htmlspecialchars($profile['username']); ?></div>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <div class="profile-detail"><?php echo htmlspecialchars($profile['email']); ?></div>
    </div>

    <div class="form-group">
        <label for="address">Address</label>
        <div class="profile-detail"><?php echo htmlspecialchars($profile['address']); ?></div>
    </div>

    <div class="form-group">
        <label for="phone">Phone</label>
        <div class="profile-detail"><?php echo htmlspecialchars($profile['phone']); ?></div>
    </div>

    <div class="form-group">
        <label for="date_of_birth">Date of Birth</label>
        <div class="profile-detail"><?php echo htmlspecialchars($profile['date_of_birth']); ?></div>
    </div>

    <div class="button-group">
        <a href="edit_profile.php">Edit Profile</a>
    </div>
</div>

</body>
</html>
