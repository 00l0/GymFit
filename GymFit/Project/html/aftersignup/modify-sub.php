<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['member_type'])) {
    header("Location: /GymFit/Project/html/home-page.php"); // Redirect to home page if not logged in
    exit();
}

// Determine texts based on membership type
if ($_SESSION['member_type'] === 'guest') {
    $buttonText = "Subscribe";
    $headingText = "Subscribe";
    $sectionDescription = "Choose the appropriate plan and click Subscribe.";
} else {
    $buttonText = "Modify Subscription";
    $headingText = "Modify Subscription";
    $sectionDescription = "You can change your subscription details here. Choose the appropriate plan and click Save.";
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <!-- Your existing head content -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($headingText); ?></title>
    <link rel="stylesheet" href="/GymFit/Project/css/aftersignup/modify-sub.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="/GymFit/Project/css/reset.css">
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

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="subscription-edit-section">
                <h1 class="head"><?= htmlspecialchars($headingText); ?></h1>
                <p class="section-description"><?= htmlspecialchars($sectionDescription); ?></p>

                <div class="boxes">
                    <div class="box">
                        <div class="box-header">
                            <h2>Basic</h2>
                            <p>$29/month</p>
                        </div>
                        <div class="box-content">
                            <div class="content-title">
                                Access to essential gym facilities and select fitness classes.
                            </div>
                            <div class="content-features">
                                <a href="#"><i class='bx bx-check'></i>Full gym access</a>
                                <a href="#"><i class='bx bx-check'></i>2 Classes/Week</a>
                                <a href="#"><i class='bx bx-check'></i>Standard Locker Room Access</a>
                            </div>
                        </div>
                        <!-- Pass the plan as a query parameter -->
                        <a href="../branch-locator.php?plan=Basic">
                            <button class="BoxJoinNow-btn"><?= htmlspecialchars($buttonText); ?></button>
                        </a>
                    </div>

                    <div class="box">
                        <div class="box-header">
                            <h2>Standard</h2>
                            <p>$49/month</p>
                        </div>
                        <div class="box-content">
                            <div class="content-title">
                                Access to essential gym facilities and select fitness classes.
                            </div>
                            <div class="content-features">
                                <a href="#"><i class='bx bx-check'></i>Unlimited Classes</a>
                                <a href="#"><i class='bx bx-check'></i>2 Trainer Sessions/Month</a>
                                <a href="#"><i class='bx bx-check'></i>Premium Locker Room Access</a>
                            </div>
                        </div>
                        <a href="../branch-locator.php?plan=Standard">
                            <button class="BoxJoinNow-btn"><?= htmlspecialchars($buttonText); ?></button>
                        </a>
                    </div>

                    <div class="box">
                        <div class="box-header">
                            <h2>Platinum</h2>
                            <p>$69/month</p>
                        </div>
                        <div class="box-content">
                            <div class="content-title">
                                Access to essential gym facilities and select fitness classes.
                            </div>
                            <div class="content-features">
                                <a href="#"><i class='bx bx-check'></i>4 Trainer Sessions/Month</a>
                                <a href="#"><i class='bx bx-check'></i>Custom Nutrition Plan</a>
                                <a href="#"><i class='bx bx-check'></i>VIP Access & Sauna</a>
                            </div>
                        </div>
                        <a href="../branch-locator.php?plan=Platinum">
                            <button class="BoxJoinNow-btn"><?= htmlspecialchars($buttonText); ?></button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <span class="fade1 fade"></span>
    <span class="fade2 fade"></span>
    <span class="fade3 fade"></span>
    <span class="fade4 fade"></span>
</body>
</html>
