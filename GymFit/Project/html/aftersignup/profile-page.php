<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['member_type'])) {
    header("Location: /GymFit/Project/html/home-page.php"); 
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gymfit";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user information
$email = $_SESSION['login_email']; 
$sql = "SELECT first_name, last_name, email, phone FROM Members WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found!";
    exit();
}

// Ensure all fields have valid default values
$user['first_name'] = $user['first_name'] ?? '';
$user['last_name'] = $user['last_name'] ?? '';
$user['email'] = $user['email'] ?? '';
$user['phone'] = $user['phone'] ?? '';

// Handle form submission for profile update
$update_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = htmlspecialchars($_POST['first_name'] ?? '');
    $last_name = htmlspecialchars($_POST['last_name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $phone = htmlspecialchars($_POST['phone'] ?? '');

    // Split the full name into first and last name
    $name_parts = explode(' ', $first_name, 2);
    $first_name = $name_parts[0] ?? '';
    $last_name = $name_parts[1] ?? '';

    // Check if the email is already taken by another user
    $check_email_sql = "SELECT COUNT(*) AS email_count FROM Members WHERE email = ? AND email != ?";
    $check_email_stmt = $conn->prepare($check_email_sql);
    $check_email_stmt->bind_param("ss", $email, $_SESSION['login_email']);
    $check_email_stmt->execute();
    $check_email_result = $check_email_stmt->get_result();
    $email_check = $check_email_result->fetch_assoc();

    if ($email_check['email_count'] > 0) {
       
        $update_message = "The email address is already in use. Please use a different email.";
    } else {
        
        $update_sql = "UPDATE Members SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssss", $first_name, $last_name, $email, $phone, $_SESSION['login_email']);

        if ($update_stmt->execute()) {
            $_SESSION['login_email'] = $email; 
            header("Location: " . $_SERVER['PHP_SELF']); 
            exit();
        } else {
            $update_message = "Failed to update profile: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="/GymFit/Project/css/aftersignup/profile-page.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="/GymFit/Project/css/reset.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<header>
    <div class="btn">
        <a href="/GymFit/Project/html/aftersignup/aftersignup.php" class="return-btn">
            <i class='bx bx-left-arrow-alt'></i>
            Back
        </a>
    </div>
</header>

<main class="main-content">
    <div class="container">
        <div class="personal-info-section">
            <h2>Personal Information</h2>
            <?php if (!empty($update_message)): ?>
                <p class="update-message"><?= htmlspecialchars($update_message) ?></p>
            <?php endif; ?>
            <form id="update-form" action="" method="POST">
                <div class="personal-info-card">
                <div class="input-group"> 
                    <div class="info-row">
                        <label class="info-label">Name:</label>
                        <span class="info-value" id="name-value">
                            <?= htmlspecialchars(trim($user['first_name'] . ' ' . $user['last_name'])) ?>
                        </span>
                        <input type="text" name="first_name" id="name-input" value="<?= htmlspecialchars(trim($user['first_name'] . ' ' . $user['last_name'])) ?>" style="display: none;">
                    </div>
                    <div class="info-row">
                        <label class="info-label">Email:</label>
                        <span class="info-value" id="email-value">
                            <?= htmlspecialchars($user['email']) ?>
                        </span>
                        <input type="email" name="email" id="email-input" value="<?= htmlspecialchars($user['email']) ?>" style="display: none;">
                    </div>
                    <div class="info-row">
                        <label class="info-label">Phone Number:</label>
                        <span class="info-value" id="phone-value">
                            <?= htmlspecialchars($user['phone']) ?>
                        </span>
                        <input type="text" name="phone" id="phone-input" value="<?= htmlspecialchars($user['phone']) ?>" style="display: none;">
                    </div>
                    </div>
                </div>
                <button type="button" id="edit-button" class="edit-button">Update Information</button>
                <button type="submit" id="confirm-button" class="edit-button" style="display: none;">Confirm Changes</button>
            </form>
        </div>
    </div>
</main>

<span class="fade1 fade"></span>
<span class="fade2 fade"></span>
<span class="fade3 fade"></span>
<span class="fade4 fade"></span>

<script>
    const editButton = document.getElementById('edit-button');
    const confirmButton = document.getElementById('confirm-button');
    const inputs = document.querySelectorAll('input');
    const values = document.querySelectorAll('.info-value');

    editButton.addEventListener('click', () => {
        values.forEach(value => value.style.display = 'none');
        inputs.forEach(input => input.style.display = 'inline-block');
        editButton.style.display = 'none';
        confirmButton.style.display = 'inline-block';
    });
</script>
</body>
</html>
