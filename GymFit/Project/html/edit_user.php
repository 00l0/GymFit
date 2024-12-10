<?php
session_start();

if (!isset($_SESSION['member_type']) || !in_array($_SESSION['member_type'], ['admin', 'branch1', 'branch2'])) {
    echo "Unauthorized access.";
    exit();
}

$member_type = $_SESSION['member_type'];

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
} else {
    echo "Invalid request.";
    exit();
}

$member_sql = "
    SELECT m.*, (
        SELECT s.branch_name
        FROM Subscriptions s
        WHERE s.member_id = m.member_id
        ORDER BY s.start_date DESC
        LIMIT 1
    ) AS branch_name
    FROM Members m
    WHERE m.member_id = ?
";
$stmt = $conn->prepare($member_sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$member_result = $stmt->get_result();

if ($member_result->num_rows == 0) {
    echo "Member not found.";
    exit();
}

$member = $member_result->fetch_assoc();

$user_branch_name = '';
if ($member_type == 'branch1') {
    $user_branch_name = 'GymFit - Al Olaya';
} elseif ($member_type == 'branch2') {
    $user_branch_name = 'GymFit - King Fahd Road';
}

$can_edit = false;

if ($member_type == 'admin') {
    $can_edit = true;
} elseif ($member_type == 'branch1' || $member_type == 'branch2') {
    if ($member['branch_name'] == $user_branch_name || $member['branch_name'] == 'All Gyms') {
        $can_edit = true;
    }
}

if (!$can_edit) {
    echo "Unauthorized access.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $member_type_input = $_POST['member_type'];
    $branch_input = $_POST['branch'];

    // Validation
    $errors = [];

    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }

    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        $email_check_sql = "SELECT member_id FROM Members WHERE email = ? AND member_id != ?";
        $email_stmt = $conn->prepare($email_check_sql);
        $email_stmt->bind_param("si", $email, $member_id);
        $email_stmt->execute();
        $email_result = $email_stmt->get_result();
        if ($email_result->num_rows > 0) {
            $errors[] = "Email is already in use by another member.";
        }
        $email_stmt->close();
    }

    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    }

    $allowed_member_types = ['guest', 'subscribed'];
    if ($member_type == 'admin') {
        $allowed_member_types = ['guest', 'subscribed', 'branch1', 'branch2', 'admin'];
    }

    if (!in_array($member_type_input, $allowed_member_types)) {
        $errors[] = "Invalid member type.";
    }

    $allowed_branches = ['GymFit - Al Olaya', 'GymFit - King Fahd Road', 'All Gyms'];
    if (!in_array($branch_input, $allowed_branches)) {
        $errors[] = "Invalid branch selection.";
    }

    if (empty($errors)) {
        $update_member_sql = "UPDATE Members SET first_name = ?, last_name = ?, email = ?, phone = ?, member_type = ? WHERE member_id = ?";
        $update_stmt = $conn->prepare($update_member_sql);
        $update_stmt->bind_param("sssssi", $first_name, $last_name, $email, $phone, $member_type_input, $member_id);
        $update_stmt->execute();
        $update_stmt->close();

        $update_sub_sql = "
            UPDATE Subscriptions
            SET branch_name = ?
            WHERE member_id = ?
            ORDER BY start_date DESC
            LIMIT 1
        ";
        $update_sub_stmt = $conn->prepare($update_sub_sql);
        $update_sub_stmt->bind_param("si", $branch_input, $member_id);
        $update_sub_stmt->execute();
        $update_sub_stmt->close();

        $_SESSION['message'] = 'Member updated successfully.';
        header("Location: admin.php?section=user-management");
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Member - GymFit</title>
    <link rel="stylesheet" href="../css/edit_user.css">
</head>
<body>
<div class="edit-member-container">
    <h1>Edit Member</h1>
    <?php if (!empty($errors)): ?>
        <div class="error-messages">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form action="edit_user.php?id=<?= $member_id ?>" method="POST">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" id="first_name" value="<?= htmlspecialchars($member['first_name']) ?>" required>

        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" id="last_name" value="<?= htmlspecialchars($member['last_name']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($member['email']) ?>" required>

        <label for="phone">Phone:</label>
        <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($member['phone']) ?>" required>

        <label>Join Date:</label>
        <input type="text" value="<?= htmlspecialchars($member['join_date']) ?>" disabled>

        <label for="member_type">Member Type:</label>
        <select name="member_type" id="member_type" required>
            <?php
            $member_types = ['guest' => 'Guest', 'subscribed' => 'Subscribed'];

            if ($member_type == 'admin') {
                $member_types['branch1'] = 'Branch1 Admin';
                $member_types['branch2'] = 'Branch2 Admin';
                $member_types['admin'] = 'Super Admin';
            }

            foreach ($member_types as $value => $label) {
                $selected = ($member['member_type'] == $value) ? 'selected' : '';
                echo "<option value=\"$value\" $selected>$label</option>";
            }
            ?>
        </select>

        <label for="branch">Branch:</label>
        <select name="branch" id="branch" required>
            <?php
            $branches = ['GymFit - Al Olaya', 'GymFit - King Fahd Road', 'All Gyms'];
            foreach ($branches as $branch_option) {
                $selected = ($member['branch_name'] == $branch_option) ? 'selected' : '';
                echo "<option value=\"$branch_option\" $selected>$branch_option</option>";
            }
            ?>
        </select>

        <button type="submit">Save Changes</button>
        <button type="button" onclick="window.location.href='admin.php?section=user-management'">Cancel</button>
    </form>
</div>
</body>
</html>
