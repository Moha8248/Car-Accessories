<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // If user is logged in, redirect to the profile page or dashboard
    header('Location: ../Login-Signup/view_profile.php'); // Adjust this to your profile page or wherever appropriate
} else {
    // If user is not logged in, redirect to the login page
    header('Location: /Login-Signup/login.html');
}
exit();
?>
