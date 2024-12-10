<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['member_type']) || !isset($_SESSION['member_id'])) {
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

// Fetch subscription details for the member
$member_id = $_SESSION['member_id'];
$subscription_details = null;

$sql = "
    SELECT membership_type AS plan_type, start_date, end_date, price, branch_name 
    FROM subscriptions 
    WHERE member_id = ? AND status = 'Active' AND end_date >= CURDATE()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
$subscription_details = $result->fetch_assoc();

// Update session member_type based on subscription status
if ($subscription_details) {
    $_SESSION['member_type'] = 'subscribed';
} else {
    $_SESSION['member_type'] = 'guest';
}

$conn->close();

// Determine Google Maps URL based on branch name
$branch_name = $subscription_details['branch_name'] ?? 'Not specified';
$google_map_url = "";
if ($branch_name === 'GymFit - Al Olaya') {
    $google_map_url = "https://maps.google.com/maps?width=400&amp;height=200&amp;hl=en&amp;q=24.67461050866685%2046.71747207629064+(GymFit)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed";
} elseif ($branch_name === 'GymFit - King Fahd Road') {
    $google_map_url = "https://maps.google.com/maps?width=100%25&amp;height=300&amp;hl=en&amp;q=24.745979,%2046.807398+(My%20Business%20Name)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed";
} elseif ($branch_name === 'All Gyms') {
    $google_map_url = [
        "https://maps.google.com/maps?width=400&amp;height=200&amp;hl=en&amp;q=24.67461050866685%2046.71747207629064+(GymFit)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed",
        "https://maps.google.com/maps?width=100%25&amp;height=300&amp;hl=en&amp;q=24.745979,%2046.807398+(My%20Business%20Name)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"
    ];
}




// Determine the label and URL for the subscription action
if ($_SESSION['member_type'] === 'guest') {
    $subscription_action_label = 'Subscribe';
    $subscription_action_url = '/GymFit/Project/html/aftersignup/modify-sub.php'; 
} else {
    $subscription_action_label = 'Modify Subscription';
    $subscription_action_url = '/GymFit/Project/html/aftersignup/modify-sub.php';
}


?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Welcome Aboard Hub</title>
    <link rel="stylesheet" href="/GymFit/Project/css/aftersignup/aftersignup.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="/GymFit/Project/css/reset.css" />
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="/GymFit/Project/images/logo.png" alt="Logo" />
                </div>
                <nav class="main-nav">
                    <a href="#" id="logoutBtn" class="nav-link">
                        <i class="bx bx-log-out"></i> Logout
                    </a>
                </nav>

                <div class="logout-confirmation">
                    <h3>Are you sure you want to logout?</h3>
                    <div class="btns">
                        <a class="cansel">Cancel</a>
                        <a onclick="signOut()" href="/GymFit/Project/html/logout.php" class="logout-confirm" onclick="showLogoutConfirmation()">
                            <i class="bx bx-log-out"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="welcome-message">
                <h1>Welcome, <?= htmlspecialchars($_SESSION['member_name']); ?></h1>
                <p>Your membership number: <?= htmlspecialchars($_SESSION['member_id']); ?></p>
            </div>

            <div class="quick-actions">
                <a href="/GymFit/Project/html/aftersignup/start-traning.html" class="action-button secondary">
                    <i class="bx bx-dumbbell"></i>
                    Start Training Now
                </a>
                <a href="<?= htmlspecialchars($subscription_action_url); ?>" class="action-button secondary">
                    <i class="bx bx-cog"></i>
                    <?= htmlspecialchars($subscription_action_label); ?>
                </a>
                <a href="/GymFit/Project/html/aftersignup/profile-page.php" class="action-button secondary">
                    <i class="bx bx-user"></i>
                    Profile
                </a>
            </div>

            <div class="subscription-card">
                <?php if ($_SESSION['member_type'] === 'guest'): ?>
                    <div class="subscription-details">
                        <h3>Subscription Details</h3>
                        <p style="color: white; font-size: 14px; text-align:center;">You are currently a guest. Consider <a class="subscribe" href='modify-sub.php'>subscribing</a> to enjoy premium benefits!</p>
                    </div>
                <?php elseif ($subscription_details): ?>
                    <div class="subscription-details">
                        <h3>Subscription Details</h3>
                        <div class="info">
                            <p>Plan Type: <?= htmlspecialchars($subscription_details['plan_type']); ?></p>
                            <p>Start Date: <?= htmlspecialchars($subscription_details['start_date']); ?></p>
                            <p>End Date: <?= htmlspecialchars($subscription_details['end_date']); ?></p>
                            <p>Price: $<?= htmlspecialchars($subscription_details['price']); ?></p>
                            <p>Branch: <?= htmlspecialchars($branch_name); ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="subscription-details">
                        <h3>Subscription Details</h3>
                        <p>No subscription information found.</p>
                    </div>
                <?php endif; ?>

                <div class="gym-map">
                    <h3>Your gym location</h3>
                    <div class="map" style="width: 100%">
                        <?php if (is_array($google_map_url)): ?>
                            <?php foreach ($google_map_url as $url): ?>
                                <iframe title="gymfit" width="400" height="200" src="<?= $url ?>"></iframe>
                                <br><br>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <iframe title="gymfit" width="400" height="200" src="<?= $google_map_url ?>"></iframe>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer id="footer">
        <div class="top-footer">
            <div class="info">
                <a href="#"><i class="bx bx-envelope"></i>info@gymfit.com</a>
            </div>
            <div class="social-media">
                <a href="#"><i class="bx bxl-facebook"></i></a>
                <a href="#"><i class="bx bxl-instagram"></i></a>
                <a href="#"><i class="bx bxl-twitter"></i></a>
            </div>
        </div>
        <div class="bottom-footer">
            <div class="location">
                <a href="../locations.html">
                    <i class="bx bx-map"></i>
                    locations
                </a>
            </div>
            <div class="terms-privace">
                <a href="#">privacy policy</a>
                <a href="#">terms & conditions</a>
            </div>
        </div>
    </footer>
    <span class="fade1 fade"></span>
    <span class="fade2 fade"></span>
    <span class="fade3 fade"></span>
    <span class="fade4 fade"></span>
    <script>
        const logoutConfirmation = document.querySelector(".logout-confirmation");

        function showLogoutConfirmation(event) {
            event.preventDefault(); 
            logoutConfirmation.style.display = "flex"; 
        }

        function hideLogoutConfirmation() {
            logoutConfirmation.style.display = "none"; 
        }

        document.getElementById("logoutBtn").addEventListener("click", showLogoutConfirmation);

        document.querySelector(".cansel").addEventListener("click", hideLogoutConfirmation);

        function signOut() {
            location.href = '../signout.php';
        }
    </script>
</body>

</html>