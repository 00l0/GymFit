<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gymfit";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['login_email'];
    $password = $_POST['login_password'];

    $stmt = $conn->prepare("SELECT * FROM Members WHERE email = ?");
    if (!$stmt) {
        die("Failed to prepare statement: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $member = $result->fetch_assoc();
        if (password_verify($password, $member['password'])) {
            $_SESSION['member_id'] = $member['member_id']; 
            $_SESSION['member_type'] = $member['member_type']; 
            $_SESSION['member_name'] = $member['first_name'] . ' ' . $member['last_name']; 
            $_SESSION['login_email'] = $member['email']; 

            if (
                $member['member_type'] == 'admin' ||
                $member['member_type'] == 'branch1' ||
                $member['member_type'] == 'branch2'
            ) {
                $_SESSION['admin_logged_in'] = true; 
                header("Location: admin.php");
            } else {
                header("Location: aftersignup/aftersignup.php");
            }
            exit();
        } else {
            $_SESSION['login_error'] = "Invalid password!";
            header("Location: home-page.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "No account found with this email!";
        header("Location: home-page.php");
        exit();
    }

    $stmt->close();
}

$conn->close();
?>
