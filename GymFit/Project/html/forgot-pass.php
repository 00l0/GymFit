<?php
session_start();
$success_message = "";
$error_message = "";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "gymfit";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Get form data
    $email = $_POST['email'];
    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    // Check if new passwords match
    if ($new_pass !== $confirm_pass) {
        $error_message = "New password and confirm password do not match.";
    } else {
        // Retrieve the user from the database using prepared statements
        $stmt = $conn->prepare("SELECT member_id, password FROM Members WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $member_id = $user['member_id'];

            // Verify the old password
            if (password_verify($old_pass, $user['password'])) {
                // Check if the new password is the same as the old password
                if (password_verify($new_pass, $user['password'])) {
                    $error_message = "New password cannot be the same as the old password.";
                } else {
                    // Hash the new password
                    $hashed_new_pass = password_hash($new_pass, PASSWORD_DEFAULT);

                    // Update the password in the database using prepared statements
                    $update_stmt = $conn->prepare("UPDATE Members SET password = ? WHERE member_id = ?");
                    $update_stmt->bind_param("si", $hashed_new_pass, $member_id);

                    if ($update_stmt->execute()) {
                        $success_message = "Password reset successful!";
                    } else {
                        $error_message = "Error updating password: " . $conn->error;
                    }

                    $update_stmt->close();
                }
            } else {
                $error_message = "The old password is incorrect.";
            }
        } else {
            $error_message = "User not found.";
        }

        $stmt->close();
    }

    // Close connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>GymFit | Reset Password</title>
    <link rel="stylesheet" href="../css/forgot-pass.css">
    <!-- Add other necessary meta tags and links -->
</head>
<body>
    <div class="reset-password-container">
        <div class="reset-password-form">
            <h1>Reset Password</h1>
            <p>Enter your email, old password, and new password below.</p>

            <!-- Display success or error message -->
            <?php if ($success_message): ?>
                <div class="success-message" style="color: green; margin-bottom: 20px;">
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php elseif ($error_message): ?>
                <div class="error-message" style="color: red; margin-bottom: 20px;">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <form action="forgot-pass.php" method="POST">
                <div class="password-input">
                    <input type="email" name="email" id="email" placeholder="Email Address" required />
                </div>
                <div class="password-input">
                    <input type="password" name="old_pass" id="old_pass" placeholder="Old Password" required />
                </div>
                <div class="password-input">
                    <input type="password" name="new_pass" id="new_pass" placeholder="New Password" required />
                </div>
                <div class="password-input">
                    <input type="password" name="confirm_pass" id="confirm_pass" placeholder="Confirm Password" required />
                </div>
                <button type="submit" class="reset-password-btn">Reset Password</button>
            </form>
            <div class="back-to-login">
                <p><a href="/GymFit/project/html/home-page.php">Back to Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
