<?php
session_start();

if (!isset($_SESSION['member_type']) || !in_array($_SESSION['member_type'], ['admin', 'branch1', 'branch2'])) {
    echo "Unauthorized access.";
    exit();
}

$member_type = $_SESSION['member_type'];

$branch_name = '';
if ($member_type == 'branch1') {
    $branch_name = 'GymFit - Al Olaya';
} elseif ($member_type == 'branch2') {
    $branch_name = 'GymFit - King Fahd Road';
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gymfit";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $member_id = intval($_GET['id']);

    if ($member_type == 'admin') {
        $delete_sql = "DELETE FROM Members WHERE member_id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $member_id);
    } else {
        $check_sql = "
            SELECT s.branch_name
            FROM Subscriptions s
            WHERE s.member_id = ? AND s.branch_name = ?
            LIMIT 1
        ";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("is", $member_id, $branch_name);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $delete_sql = "DELETE FROM Members WHERE member_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $member_id);
        } else {
            echo "Unauthorized access.";
            exit();
        }

        $check_stmt->close();
    }

    if ($stmt->execute()) {
        $_SESSION['message'] = 'Member deleted successfully.';
    } else {
        $_SESSION['error'] = 'Failed to delete member: ' . $conn->error;
    }

    $stmt->close();

    $section = isset($_GET['section']) ? $_GET['section'] : 'user-management';
    header("Location: admin.php?section={$section}");
    exit();

} else {
    echo "Invalid request.";
    exit();
}

$conn->close();
?>
