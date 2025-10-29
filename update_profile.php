<?php
session_start();
require_once 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newEmail = $_POST['email'];
    $username = $_SESSION['username'];
    
    try {
        // Validate email format
        if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "Invalid email format";
            header('Location: profile.php');
            exit();
        }
        
        // Update the email in database
        $stmt = $pdo->prepare("UPDATE user SET email = ? WHERE username = ?");
        $stmt->execute([$newEmail, $username]);
        
        // Update session email
        $_SESSION['email'] = $newEmail;
        
        // Redirect with success message
        header('Location: profile.php?success=1');
        exit();
        
    } catch(PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    // If not POST request, redirect to profile
    header('Location: profile.php');
    exit();
}
?>