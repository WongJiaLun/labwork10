<?php
session_start();
require_once 'db.php';

// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Fetch current user data
try {
    $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
    $stmt->execute([$_SESSION['username']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        session_destroy();
        header('Location: login.php');
        exit();
    }
} catch(PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Check for update success message
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        .profile-card { background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .info-group { margin-bottom: 15px; }
        .label { font-weight: bold; color: #333; }
        .value { color: #666; }
        .edit-btn { background: #28a745; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; display: inline-block; }
        .logout { color: #dc3545; text-decoration: none; margin-left: 20px; }
        .success { color: #28a745; background: #d4edda; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .edit-form { background: #f0f8ff; padding: 20px; border-radius: 8px; margin-top: 20px; }
        .form-group { margin-bottom: 15px; }
        input[type="email"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007bff; color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h2>User Profile</h2>
    
    <?php if ($success): ?>
        <div class="success">Profile updated successfully!</div>
    <?php endif; ?>
    
    <div class="profile-card">
        <h3>Current Profile Information</h3>
        <div class="info-group">
            <span class="label">Username:</span>
            <span class="value"><?php echo htmlspecialchars($user['username']); ?></span>
        </div>
        <div class="info-group">
            <span class="label">Email:</span>
            <span class="value"><?php echo htmlspecialchars($user['email']); ?></span>
        </div>
        <a href="#" onclick="toggleEditForm()" class="edit-btn">Edit Email</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>
    
    <div id="editForm" class="edit-form" style="display: none;">
        <h3>Edit Profile</h3>
        <form method="POST" action="update_profile.php">
            <div class="form-group">
                <label for="email">New Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <button type="submit">Update Profile</button>
            <button type="button" onclick="toggleEditForm()" style="background: #6c757d;">Cancel</button>
        </form>
    </div>
    
    <script>
        function toggleEditForm() {
            const form = document.getElementById('editForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>