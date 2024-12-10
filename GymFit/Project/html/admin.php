<?php
session_start();

if (!isset($_SESSION['member_type']) || !in_array($_SESSION['member_type'], ['admin', 'branch1', 'branch2'])) {
    header("Location: ../login.php");
    exit();
}

$member_type = $_SESSION['member_type'];

$active_section = isset($_GET['section']) ? $_GET['section'] : 'user-management';

$message = '';
$message_type = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = 'success';
    unset($_SESSION['message']);
} elseif (isset($_SESSION['error'])) {
    $message = $_SESSION['error'];
    $message_type = 'error';
    unset($_SESSION['error']);
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gymfit";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$branch_name = '';
if ($member_type == 'branch1') {
    $branch_name = 'GymFit - Al Olaya';
} elseif ($member_type == 'branch2') {
    $branch_name = 'GymFit - King Fahd Road';
}

if ($member_type == 'admin') {
    $user_sql = "
        SELECT 
            m.member_id, 
            m.first_name, 
            m.last_name, 
            m.email, 
            m.phone, 
            m.join_date, 
            m.status, 
            m.member_type,
            COALESCE(s.branch_name, 'N/A') AS branch_name
        FROM 
            Members m
        LEFT JOIN (
            SELECT 
                member_id, 
                branch_name,
                ROW_NUMBER() OVER (PARTITION BY member_id ORDER BY start_date DESC) as rn
            FROM 
                Subscriptions
        ) s ON m.member_id = s.member_id AND s.rn = 1
    ";
} else {
    $user_sql = "
        SELECT DISTINCT
            m.member_id, 
            m.first_name, 
            m.last_name, 
            m.email, 
            m.phone, 
            m.join_date, 
            m.status, 
            m.member_type,
            s.branch_name AS branch_name
        FROM 
            Members m
        INNER JOIN 
            Subscriptions s ON m.member_id = s.member_id
        WHERE 
            s.branch_name = '$branch_name' OR s.branch_name = 'All Gyms'
    ";
}

$user_result = $conn->query($user_sql);

if ($member_type == 'admin') {
    $sub_sql = "
        SELECT 
            s.subscription_id, 
            s.member_id, 
            CONCAT(m.first_name, ' ', m.last_name) AS member_name, 
            s.membership_type, 
            s.start_date, 
            s.end_date, 
            s.status, 
            s.price, 
            s.branch_name 
        FROM 
            Subscriptions s 
        JOIN 
            Members m 
        ON 
            s.member_id = m.member_id
    ";
} else {
    $sub_sql = "
        SELECT 
            s.subscription_id, 
            s.member_id, 
            CONCAT(m.first_name, ' ', m.last_name) AS member_name, 
            s.membership_type, 
            s.start_date, 
            s.end_date, 
            s.status, 
            s.price, 
            s.branch_name 
        FROM 
            Subscriptions s 
        JOIN 
            Members m 
        ON 
            s.member_id = m.member_id
        WHERE 
            s.branch_name = '$branch_name' OR s.branch_name = 'All Gyms'
    ";
}

$sub_result = $conn->query($sub_sql);

if ($member_type == 'admin') {
    $billing_sql = "
        SELECT 
            b.transaction_id, 
            b.member_id, 
            CONCAT(m.first_name, ' ', m.last_name) AS member_name, 
            b.membership_type, 
            b.amount, 
            b.payment_date, 
            b.branch_name 
        FROM 
            Billing b 
        JOIN 
            Members m 
        ON 
            b.member_id = m.member_id
    ";
} else {
    $billing_sql = "
        SELECT 
            b.transaction_id, 
            b.member_id, 
            CONCAT(m.first_name, ' ', m.last_name) AS member_name, 
            b.membership_type, 
            b.amount, 
            b.payment_date, 
            b.branch_name 
        FROM 
            Billing b 
        JOIN 
            Members m 
        ON 
            b.member_id = m.member_id
        WHERE 
            b.branch_name = '$branch_name' OR b.branch_name = 'All Gyms'
    ";
}

$billing_result = $conn->query($billing_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel - GymFit</title>
    <link rel="stylesheet" href="../css/admin.css?v=<?php echo time(); ?>">
</head>

<body>
    <div class="dashboard-container">
        <h1>Admin Panel</h1>
        <?php
        if ($member_type == 'admin') {
            echo '<h2><center>Welcome, Super Admin</center></h2>';
        } elseif ($member_type == 'branch1') {
            echo '<h2><center>Welcome, Al Olaya Branch Admin</center></h2>';
        } elseif ($member_type == 'branch2') {
            echo '<h2><center>Welcome, King Fahd Branch Admin</center></h2>';
        }
        ?>
        <div class="dashboard-tabs">
            <button onclick="showSection('user-management')" id="tab-user-management">User Management</button>
            <button onclick="showSection('subscription-management')" id="tab-subscription-management">Subscription Management</button>
            <button onclick="showSection('financial-management')" id="tab-financial-management">Financial Management</button>
            <button onclick="signOut()" class="logout-btn">Logout</button>
        </div>

        <div class="search-container" style="display: none;" id="search-container">
            <input type="text" id="search-id" placeholder="Search by ID..." oninput="searchById()" />
        </div>

        <?php if (!empty($message)): ?>
            <div class="message <?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div id="user-management" class="dashboard-section">
            <h2>User Management</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Join Date</th>
                    <th>Status</th>
                    <th>Member Type</th>
                    <th>Branch</th>
                    <th class="actions-header">Actions</th>
                </tr>
                <?php while ($row = $user_result->fetch_assoc()): ?>
                    <tr id="member-row-<?= htmlspecialchars($row['member_id']) ?>" class="user-row">
                        <td><?= htmlspecialchars($row['member_id']) ?></td>
                        <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['phone']) ?></td>
                        <td><?= htmlspecialchars($row['join_date']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['member_type']) ?></td>
                        <td><?= htmlspecialchars($row['branch_name']) ?></td>
                        <td id="action-<?= htmlspecialchars($row['member_id']) ?>" class="actions-cell">
                            <div class="action-buttons">
                                <?php
                                $can_edit = false;
                                if ($member_type == 'admin') {
                                    $can_edit = true;
                                } elseif (($member_type == 'branch1' || $member_type == 'branch2') &&
                                    ($row['branch_name'] == $branch_name || $row['branch_name'] == 'All Gyms')
                                ) {
                                    $can_edit = true;
                                }
                                ?>
                                <?php if ($can_edit): ?>
                                    <a class="edit" href="edit_user.php?id=<?= $row['member_id'] ?>">Edit</a>
                                    <a class="del" href="#" onclick="confirmDelete(<?= $row['member_id'] ?>)">Delete</a>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div id="subscription-management" class="dashboard-section" style="display: none;">
            <h2>Subscription Management</h2>
            <table>
                <tr>
                    <th>Subscription ID</th>
                    <th>Member ID</th>
                    <th>Member Name</th>
                    <th>Membership Type</th>
                    <th>Branch Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Status</th>
                    <th>Price</th>
                </tr>
                <?php while ($row = $sub_result->fetch_assoc()): ?>
                    <tr id="subscription-row-<?= htmlspecialchars($row['subscription_id']) ?>" class="subscription-row">
                        <td><?= htmlspecialchars($row['subscription_id']) ?></td>
                        <td><?= htmlspecialchars($row['member_id']) ?></td>
                        <td><?= htmlspecialchars($row['member_name']) ?></td>
                        <td><?= htmlspecialchars($row['membership_type']) ?></td>
                        <td><?= htmlspecialchars($row['branch_name']) ?></td>
                        <td><?= htmlspecialchars($row['start_date']) ?></td>
                        <td><?= htmlspecialchars($row['end_date']) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= htmlspecialchars($row['price']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

        <div id="financial-management" class="dashboard-section" style="display: none;">
            <h2>Financial Management</h2>
            <table>
                <tr>
                    <th>Transaction ID</th>
                    <th>Member Name</th>
                    <th>Membership Type</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                    <th>Branch Name</th>
                </tr>
                <?php while ($row = $billing_result->fetch_assoc()): ?>
                    <tr id="billing-row-<?= htmlspecialchars($row['transaction_id']) ?>" class="billing-row">
                        <td><?= htmlspecialchars($row['transaction_id']) ?></td>
                        <td><?= htmlspecialchars($row['member_name']) ?></td>
                        <td><?= htmlspecialchars($row['membership_type']) ?></td>
                        <td><?= htmlspecialchars($row['amount']) ?></td>
                        <td><?= htmlspecialchars($row['payment_date']) ?></td>
                        <td><?= htmlspecialchars($row['branch_name']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>

    </div>
    <script>
    function showSection(section) {
        // Hide all sections
        document.querySelectorAll('.dashboard-section').forEach(function (el) {
            el.style.display = 'none';
        });
        
        // Show the selected section
        document.getElementById(section).style.display = 'block';

        // Control visibility of the search bar based on the section
        const searchContainer = document.getElementById('search-container');
        
        // Only show search bar on the 'user-management' section
        if (section === 'user-management') {
            searchContainer.style.display = 'flex';  // Show the search bar for user management
        } else {
            searchContainer.style.display = 'none';   // Hide the search bar for subscription and financial management
        }

        // Add border-bottom to the active tab and remove from others
        const tabs = document.querySelectorAll('.dashboard-tabs button');
        tabs.forEach(tab => {
            tab.classList.remove('active-tab');
        });

        // Add 'active-tab' class to the clicked tab
        document.getElementById('tab-' + section).classList.add('active-tab');
    }

    function signOut() {
        window.location.href = '../html/logout.php';
    }
</script>


</body>

</html>

<?php
$conn->close();
?>