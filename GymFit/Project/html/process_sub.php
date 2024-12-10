<?php
session_start();

if (!isset($_SESSION['member_id'])) {
    die("Please log in first.");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gymfit";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$table_creation_sql = "
    CREATE TABLE IF NOT EXISTS Subscriptions (
        subscription_id INT PRIMARY KEY AUTO_INCREMENT,
        member_id INT NOT NULL,
        membership_type VARCHAR(50) NOT NULL,
        start_date DATE NOT NULL,
        end_date DATE NOT NULL,
        status VARCHAR(20) DEFAULT 'Active',
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (member_id) REFERENCES Members(member_id)
    );
";

if (!$conn->query($table_creation_sql)) {
    die("Error creating Subscriptions table: " . $conn->error);
}

$member_id = $_SESSION['member_id'];
$plan = isset($_POST['plan']) ? $conn->real_escape_string($_POST['plan']) : 'Basic';

$start_date = date("Y-m-d");
$end_date = date("Y-m-d", strtotime("+1 month"));

$price = 0;
switch ($plan) {
    case 'Standard':
        $price = 49;
        break;
    case 'Platinum':
        $price = 69;
        break;
    default:
        $price = 29;
        $plan = 'Basic';
}

$sql = "INSERT INTO Subscriptions (member_id, membership_type, start_date, end_date, status, price)
        VALUES (?, ?, ?, ?, 'Active', ?)";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("isssd", $member_id, $plan, $start_date, $end_date, $price);

    if ($stmt->execute()) {
        echo "Subscription successful!";
        header("Location: /Gymfit/project/html/aftersignup/aftersignup.php");
        exit();
    } else {
        echo "Error inserting subscription: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}

$conn->close();
?>
