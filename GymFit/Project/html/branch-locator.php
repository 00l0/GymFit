<?php
session_start();

// Get the selected plan from the query parameters
$plan = isset($_GET['plan']) ? htmlspecialchars($_GET['plan']) : 'Basic';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymFit Locator</title>
    <link rel="stylesheet" href="../css/branch-locator.css?ver=<?php echo filemtime('../css/branch-locator.css'); ?>">
    <link rel="stylesheet" href="../css/reset.css?ver=<?php echo filemtime('../css/branch-locator.css'); ?>">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <!-- Return Button (Top Left) -->
    <button id="returnButton" class="return-btn">
        <i class='bx bx-left-arrow-alt'></i>
        Back
    </button>

    <header>
        <h1>GymFit Locator</h1>
        <p>Select your subscription option below</p>
    </header>

    <div class="locator-container">
        <div class="subscription-options">
            <label>
                <input type="radio" name="subscription" value="all" onclick="toggleSubscriptionOption()" />
                Subscribe to All Gyms
            </label>
            <label>
                <input type="radio" name="subscription" value="one" checked onclick="toggleSubscriptionOption()" />
                Subscribe to One Gym
            </label>
        </div>

        <div id="payment-button-container" style="display: none;">
            <a class="payment-btn" href="payment-page.php?plan=<?php echo urlencode($plan); ?>&branch=All%20Gyms">Continue to Payment</a>
        </div>

        <div id="gym-selection-container">
            <div class="map" style="width: 100%">
                <iframe
                    title="gymfit"
                    width="100%"
                    height="300"
                    src="https://maps.google.com/maps?width=400&amp;height=200&amp;hl=en&amp;q=24.67461050866685%2046.71747207629064+(GymFit)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
            </div>

            <div class="gymfit-list">
                <div class="gymfit-card">
                    <h3>GymFit - Al Olaya</h3>
                    <p>123 Example Street, Riyadh</p>
                    <p><strong>Distance:</strong> 2.5 km</p>
                    <br>
                    <a href="payment-page.php?plan=<?php echo urlencode($plan); ?>&branch=GymFit%20-%20Al%20Olaya" class="details-btn">
                        Continue to Payment
                    </a>
                </div>
                <br><br>
                <div style="width: 100%">
                    <iframe
                        title="gymfit"
                        width="100%"
                        height="300"
                        src="https://maps.google.com/maps?width=100%25&amp;height=300&amp;hl=en&amp;q=24.745979,%2046.807398+(My%20Business%20Name)&amp;t=&amp;z=14&amp;ie=UTF8&amp;iwloc=B&amp;output=embed"></iframe>
                </div>

                <div class="gymfit-card">
                    <h3>GymFit - King Fahd Road</h3>
                    <p>456 Another Street, Riyadh</p>
                    <p><strong>Distance:</strong> 5.1 km</p>
                    <br>
                    <a href="payment-page.php?plan=<?php echo urlencode($plan); ?>&branch=GymFit%20-%20King%20Fahd%20Road" class="details-btn">
                        Continue to Payment
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSubscriptionOption() {
            const allGymsSelected = document.querySelector('input[name="subscription"][value="all"]').checked;
            const paymentButton = document.getElementById("payment-button-container");
            const gymSelection = document.getElementById("gym-selection-container");

            if (allGymsSelected) {
                paymentButton.style.display = "block";
                gymSelection.style.display = "none";
            } else {
                paymentButton.style.display = "none";
                gymSelection.style.display = "block";
            }
        }

        // Return button logic (go back to the previous page)
        document.getElementById("returnButton").addEventListener("click", function() {
            window.history.back();
        });
    </script>

    <span class="fade1 fade"></span>
    <span class="fade2 fade"></span>
    <span class="fade3 fade"></span>
    <span class="fade4 fade"></span>
</body>

</html>