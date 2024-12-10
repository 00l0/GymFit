<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gymfit";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$create_table_sql = "
CREATE TABLE IF NOT EXISTS Members (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(191) NOT NULL UNIQUE,
    phone VARCHAR(50),
    join_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    password VARCHAR(255) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'inactive',
    member_type ENUM('admin', 'branch1', 'branch2', 'subscribed', 'guest') DEFAULT 'guest'
);
";

if (!$conn->query($create_table_sql)) {
    die("Error creating table: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("INSERT INTO Members (first_name, last_name, email, phone, password, member_type) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password_plain = $_POST['password'];
    $member_type = 'guest'; 

    $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

    $email_check_sql = "SELECT email FROM Members WHERE email = ?";
    $email_stmt = $conn->prepare($email_check_sql);
    if (!$email_stmt) {
        die("Error preparing email check statement: " . $conn->error);
    }
    $email_stmt->bind_param("s", $email);
    $email_stmt->execute();
    $email_stmt->store_result();

    if ($email_stmt->num_rows > 0) {
        $_SESSION['error'] = "This email is already registered. Please log in or use a different email.";
        header("Location: home-page.php"); 
        exit();
    }

    $email_stmt->close();

    $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $password_hashed, $member_type);

    if ($stmt->execute()) {
        $member_id = $stmt->insert_id;

        $_SESSION['member_id'] = $member_id;
        $_SESSION['member_name'] = $first_name . ' ' . $last_name;
        $_SESSION['login_email'] = $email;
        $_SESSION['member_type'] = $member_type;

        header("Location: aftersignup/aftersignup.php");
        exit();
    } else {
        $_SESSION['error'] = "An error occurred: " . $stmt->error;
        header("Location: home-page.php");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
