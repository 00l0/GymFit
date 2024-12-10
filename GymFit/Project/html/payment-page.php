<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Membership Payment</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../css/reset.css" />
    <link rel="stylesheet" href="../css/payment-page.css?v=<?php echo time(); ?>" />
</head>

<body>
    <?php
    session_start();
    $success_message = "";
    $error_message = "";

    $plan = isset($_GET['plan']) ? htmlspecialchars($_GET['plan']) : 'Basic';
    $branch = isset($_GET['branch']) ? htmlspecialchars($_GET['branch']) : 'Unknown';

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
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['member_id'])) {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "gymfit";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $subscriptions_table_sql = "
        CREATE TABLE IF NOT EXISTS Subscriptions (
            subscription_id INT PRIMARY KEY AUTO_INCREMENT,
            member_id INT NOT NULL,
            membership_type VARCHAR(50) NOT NULL,
            branch_name VARCHAR(255) DEFAULT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            status VARCHAR(20) DEFAULT 'Active',
            price DECIMAL(10, 2) NOT NULL,
            FOREIGN KEY (member_id) REFERENCES Members(member_id)
        );
    ";
        $conn->query($subscriptions_table_sql);

        $billing_table_sql = "
        CREATE TABLE IF NOT EXISTS Billing (
            transaction_id INT PRIMARY KEY AUTO_INCREMENT,
            member_id INT NOT NULL,
            membership_type VARCHAR(50) NOT NULL,
            branch_name VARCHAR(255) DEFAULT NULL,
            amount DECIMAL(10, 2) NOT NULL,
            payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (member_id) REFERENCES Members(member_id)
        );
    ";
        $conn->query($billing_table_sql);

        $member_id = $_SESSION['member_id'];

        $check_subscription_sql = "
        SELECT membership_type, branch_name 
        FROM Subscriptions 
        WHERE member_id = '$member_id' AND status = 'Active'";
        $result = $conn->query($check_subscription_sql);

        $start_date = date("Y-m-d");
        $end_date = date("Y-m-d", strtotime("+1 month"));

        if ($result->num_rows > 0) {
            $current_subscription = $result->fetch_assoc();
            $current_plan = $current_subscription['membership_type'];
            $current_branch = $current_subscription['branch_name'];

            if (($current_plan === 'Standard' && $plan === 'Basic') ||
                ($current_plan === 'Platinum' && ($plan === 'Standard' || $plan === 'Basic'))
            ) {
                $error_message = "Downgrading your subscription is not allowed. <a href='aftersignup/modify-sub.php'>Go back and choose a different subscription</a>";
            } elseif ($current_branch === 'All Gyms' && $branch !== 'All Gyms') {
                $error_message = "You cannot modify your subscription from 'All Gyms' to a single branch. <a href='modify-sub.php'>Go back and choose a different subscription</a>";
            } else {
                $update_subscription_sql = "
                UPDATE Subscriptions 
                SET membership_type = '$plan', branch_name = '$branch', start_date = '$start_date', end_date = '$end_date', price = '$price' 
                WHERE member_id = '$member_id' AND status = 'Active'";
                if ($conn->query($update_subscription_sql) === TRUE) {
                    $success_message = "Subscription updated successfully!";
                } else {
                    $error_message = "Failed to update subscription: " . $conn->error;
                }
            }
        } else {
            $insert_subscription_sql = "
            INSERT INTO Subscriptions (member_id, membership_type, branch_name, start_date, end_date, status, price)
            VALUES ('$member_id', '$plan', '$branch', '$start_date', '$end_date', 'Active', '$price')";
            if ($conn->query($insert_subscription_sql) === TRUE) {
                $success_message = "New subscription created successfully!";
            } else {
                $error_message = "Failed to create subscription: " . $conn->error;
            }
        }

        if (empty($error_message)) {
            $billing_sql = "
            INSERT INTO Billing (member_id, membership_type, branch_name, amount)
            VALUES ('$member_id', '$plan', '$branch', '$price')";
            if ($conn->query($billing_sql) === FALSE) {
                $error_message .= " Error recording transaction: " . $conn->error;
            }
        }

        if (empty($error_message)) {
            $update_member_type_sql = "UPDATE Members SET status = 'Active', member_type = 'subscribed' WHERE member_id = '$member_id'";
            if ($conn->query($update_member_type_sql) === FALSE) {
                $error_message .= " Error updating member type: " . $conn->error;
            }
        }

        $conn->close();
    }
    ?>


    <div class="payment-card">
        <div class="header">
            <h1><i class='bx bx-lock-alt'></i>Payment</h1>
            <p>Selected Plan: <strong><?php echo $plan; ?></strong> - <strong>$<?php echo $price; ?>/month</strong></p>
            <p>Selected Branch: <strong><?php echo $branch; ?></strong></p>
        </div>
        <form action="payment-page.php?plan=<?php echo $plan; ?>&branch=<?php echo urlencode($branch); ?>" method="POST">
            <div class="inputs">
                <div class="input">
                    <input type="text" placeholder=" " id="name" required>
                    <label for="name">Cardholder Name</label>
                </div>
                <div class="input">
                    <input type="text" placeholder=" " id="number" required>
                    <label for="number">Card Number</label>
                    <i class='bx bx-credit-card card'></i>
                </div>
                <div class="expire-cvv">
                    <div class="input">
                        <input type="text" placeholder=" " id="expire" required>
                        <label for="expire" class="expire-date">Expire Date</label>
                    </div>
                    <div class="input">
                        <input type="text" placeholder=" " id="cvv" required>
                        <label class="cvv" for="cvv">CVV</label>
                    </div>
                </div>
                <div class="sub-btn">
                    <button type="submit">Subscribe</button>
                </div>
                <div class="cancel-btn">
                    <button type="button" id="cancelButton">Cancel</button>
                </div>

            </div>
        </form>

        <?php if ($success_message): ?>
            <div class="success-message" style="color: green; margin-top: 20px;">
                <span style="color:white; font-size:14px; text-align:center;"><?php echo $success_message; ?></span>
                <a href="aftersignup/aftersignup.php" style="color:#39ff14;">Go to Dashboard</a>
            </div>
        <?php elseif ($error_message): ?>
            <div class="error-message" style="color: red; margin-top: 20px;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById("cancelButton").addEventListener("click", function() {
            window.history.back();
        });
    </script>

</body>

</html>