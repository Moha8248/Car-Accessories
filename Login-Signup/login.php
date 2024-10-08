<?php
session_start();  // Start the session

// Database connection details (update with your credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_accessories";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted form data
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL query to check user credentials
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user record
        $user = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $user['password'])) {  // Assuming the password is hashed
            // Login successful, set session variables
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_id'] = $user['id']; // Save the user's ID to session
            $_SESSION['loggedin'] = true;

            // Redirect based on email
            if ($email === 'admin@gmail.com') {
                header("Location: ../Admin/Dashboard.php"); 
            } else {
                header("Location: ../index.html"); 
            }
            exit;
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that email address.";
    }

    $stmt->close();
}

$conn->close();
?>
