<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit();
}

// Database connection
require '../db.php';

// Get the user ID from the session
$userId = $_SESSION['user_id'];

// Handle the form submission for updating the profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated profile data from the form
    $profileId = $_POST['profile_id'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $dateOfBirth = $_POST['date_of_birth'];

    // Update query
    $sql = "UPDATE user_profiles SET address = ?, phone = ?, date_of_birth = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiii", $address, $phone, $dateOfBirth, $profileId, $userId);

    if ($stmt->execute()) {
        // Successfully updated
        header("Location: view_profile.php"); // Redirect back to the profile page to show updated data
        exit();
    } else {
        // Error occurred
        echo "Error updating profile: " . $stmt->error;
    }
}

// Fetch the logged-in user's profile using their user ID
$sql = "SELECT u.username, u.email, up.id as profile_id, up.image, up.address, up.phone, up.date_of_birth 
        FROM users u 
        JOIN user_profiles up ON u.id = up.user_id 
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userProfile = $result->fetch_assoc();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f5;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #007AFF;
            color: white;
        }
        .header .left, .header .right {
            font-size: 17px;
            cursor: pointer;
            transition: color 0.3s;
        }
        .header .left:hover, .header .right:hover {
            color: #e0e0e0;
        }
        .header .center {
            font-size: 20px;
            font-weight: bold;
        }
        .profile-photo {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px 0;
        }
        .profile-photo img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #007AFF;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .profile-photo a {
            color: #007AFF;
            text-decoration: none;
            margin-top: 10px;
            transition: color 0.3s;
        }
        .profile-photo a:hover {
            color: #0056b3;
        }
        .form {
            padding: 0 15px;
        }
        .form .form-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #ccc;
            background-color: #ffffff;
            transition: background-color 0.3s;
        }
        .form .form-group:hover {
            background-color: #f1f1f1;
        }
        .form .form-group:last-child {
            border-bottom: none;
        }
        .form .form-group label {
            font-size: 18px;
            color: #007AFF;
        }
        .form .form-group .value {
            font-size: 18px;
            color: #333;
        }
        .links {
            padding: 15px;
            border-top: 1px solid #ccc;
        }
        .links a {
            display: block;
            color: #007AFF;
            text-decoration: none;
            padding: 10px 0;
            font-size: 17px;
            transition: color 0.3s;
        }
        .links a:hover {
            color: #0056b3;
        }
        /* Modal styles */
        #editModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background: #fff;
            margin: 15% auto;
            padding: 20px;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        .modal-content h2 {
            margin: 0;
            padding-bottom: 10px;
            color: #007AFF;
        }
        .modal-content label {
            font-weight: bold;
        }
        .modal-content input {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .modal-content input[type="submit"] {
            background: #007AFF;
            color: white;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .modal-content input[type="submit"]:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="center">View Profile</div>
        <div class="right" onclick="document.getElementById('editModal').style.display='block';">Edit</div>
    </div>

    <div class="profile-photo">
        <img src='data:image/jpeg;base64,<?php echo base64_encode($userProfile['image']); ?>' alt="Profile Photo" />
    </div>

    <div class="form">
        <div class="form-group">
            <label>Name</label>
            <div class="value"><?php echo $userProfile['username']; ?></div>
        </div>
        <div class="form-group">
            <label>Email</label>
            <div class="value"><?php echo $userProfile['email']; ?></div>
        </div>
        <div class="form-group">
            <label>Address</label>
            <div class="value"><?php echo $userProfile['address']; ?></div>
        </div>
        <div class="form-group">
            <label>Phone</label>
            <div class="value"><?php echo $userProfile['phone']; ?></div>
        </div>
        <div class="form-group">
            <label>Date of Birth</label>
            <div class="value"><?php echo $userProfile['date_of_birth']; ?></div>
        </div>
    </div>

    <div class="links">
        <a href="#" onclick="document.getElementById('editModal').style.display='block';">Edit Profile</a>
    </div>

    <!-- Edit Modal -->
    <div id="editModal">
        <div class="modal-content">
            <span style="cursor:pointer; float:right;" onclick="document.getElementById('editModal').style.display='none';">&times;</span>
            <h2>Edit Profile</h2>
            <form method="post" action="">
                <input type="hidden" name="profile_id" value="<?php echo $userProfile['profile_id']; ?>">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo $userProfile['address']; ?>" required>
                <label for="phone">Phone:</label>
                <input type="text" id="phone" name="phone" value="<?php echo $userProfile['phone']; ?>" required>
                <label for="date_of_birth">Date of Birth:</label>
                <input type="date" id="date_of_birth" name="date_of_birth" value="<?php echo $userProfile['date_of_birth']; ?>" required>
                <input type="submit" value="Update">
            </form>
        </div>
    </div>

    <script>
        // Close the modal if clicked outside of it
        window.onclick = function(event) {
            var modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
