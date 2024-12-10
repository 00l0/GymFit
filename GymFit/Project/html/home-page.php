<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GymFit</title>
    <link rel="stylesheet" href="../css/home-page.css">
    <link rel="stylesheet" href="../css/sign-up.css">
    <link rel="stylesheet" href="../css/reset.css">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=location_on" />
  </head>
  <body>
  <?php
    session_start();

    $login_error = '';
if (isset($_SESSION['login_error'])) {
    $login_error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}


    ?>
    <header id="home">
      <div class="logo">
        <img src="../images/logo.png" alt="" />
      </div>
      <div class="links">
        <a href="#home">home</a>
        <a href="#about">About</a>
        <a href="#plan">plans</a>
        <a href="#footer">Contact</a>
      </div>
      <div class="signup-login">
        <button class="login-btn">Log in</button>
        <button class="signup-btn">sign up</button>
      </div>
      <div class="menu">
        <i class='bx bx-menu-alt-right' ></i>
        <div class="menu-slider">
          <a class="menu-links" href="#home">home</a>
          <a class="menu-links" href="#about">About</a>
          <a class="menu-links" href="#plan">plans</a>
          <a class="menu-links" href="#footer">Contact</a>
          <div class="menu-logSign">
            <button class="btn-login">log in</button>
            <button class="btn-signup">sign up</button>
          </div>
        </div>
      </div>
    </header>

    <div class="signup-form">
      <form action="signup.php" method="post">
    <?php if (isset($_SESSION['error'])): ?>
      <div class="error-message">
          <?= htmlspecialchars($_SESSION['error']); ?>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
        <i class='bx bx-x'></i> 
        <h1>Sign Up</h1>
        <div class="input-group">
          <input type="text" id="first_name" name="first_name" placeholder=" " required />
          <label for="first_name">First Name</label>
        </div>
        <div class="input-group">
          <input type="text" id="last_name" name="last_name" placeholder=" " required />
          <label for="last_name">Last Name</label>
        </div>
        <div class="input-group">
          <input type="email" id="email" name="email" placeholder=" " required />
          <label for="email">Email</label>
        </div>
        <div class="input-group">
          <input type="phone" id="phone" name="phone" placeholder=" " required />
          <label for="phone">Phone</label>
        </div>
        <div class="input-group">
          <input type="password" id="password" name="password" placeholder=" " required />
          <label for="password">Password</label>
        </div>
        <div class="terms">
          <input type="checkbox" id="accept_terms" name="accept_terms" required />
          <span for="accept_terms">I accept the <a href="#" target="_blank">Terms and Conditions</a></span>
        </div>
        <button type="submit" class="submit-btn">Sign Up</button>
        <p class="haveAacc">Already have an account? <span class="show-login-form">Log In</span></p>
      </form>
    </div>

    
    
    <div class="login-form">
      <form action="login.php" method="post">
      <?php if (!empty($login_error)): ?>
    <div class="error-message">
        <?= htmlspecialchars($login_error); ?>
    </div>
<?php endif; ?>

        <i class='bx bx-x'></i> 
        <h1>Log In</h1>
        <div class="input-group">
          <input type="email" id="login_email" name="login_email" placeholder=" " required />
          <label for="login_email">Email</label>
        </div>
        <div class="input-group">
          <input type="password" id="login_password" name="login_password" placeholder=" " required />
          <label for="login_password">Password</label>
        </div>
        <div class="login-options">
          <div class="remember-me">
            <input type="checkbox">
          <span>remember me</span>
          </div>
          <a href="/GymFit/Project/html/forgot-pass.php" class="forgot-password">Forgot Password?</a>
        </div>
        <button type="submit" class="submit-btn">Log In</button>
        <p class="haveAacc">Don't have an account? <span class="show-signup-form">Sign Up</span></p>
      </form>
    </div>
    

    <main>
      <div class="content">
        <p>
          Unleash Your <br />
          Strength, Transform <br />Your Life with <span>GymFit</span>
        </p>
        <button class="joinnow-btn">join now</button>
      </div>
    </main>
    <section>
      <div class="about" >
        <div class="divider">about us</div>
        <div class="aboutUs-content" id="about">
          <img src="../images/AboutUs-photo_blackAndwhite 1.png" alt="" />
          <div class="aboutUs-paragraph">
            <p>
              At GymFit, we’re dedicated to helping you reach your fitness
              goals. With expert trainers, top-notch equipment, and a variety of
              programs, we provide the tools you need to transform your health
              and fitness. Whether you’re a beginner or an experienced athlete,
              GymFit is the place to start your journey.
            </p>
            <button class="joinUsNow">join us now</button>
          </div>
        </div>
      </div>
      <div class="plans" >
        <div class="divider">Plans</div>
        <div class="plan-content">
          <h1>Choose Your Membership Plan</h1>
          <p>
            Get access to top-tier facilities, expert trainers, and specialized
            programs designed to help you achieve your fitness goals. Pick a
            plan that fits your lifestyle and start your fitness journey today!
          </p>
        </div>
          <div class="boxes" id="plan">
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
               <button class="BoxJoinNow-btn">join now</button>
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
             <button class="BoxJoinNow-btn">join now</button>
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
              <button class="BoxJoinNow-btn">join now</button>
            </div>
          </div>
        </div>
      </div>
    </section>
    <footer id="footer">
      <div class="top-footer">
        <div class="info">
          <a href="#"><i class='bx bx-envelope'></i>info@gymfit.com</a>
        </div>
        <div class="social-media">
          <a href="#"><i class='bx bxl-facebook'></i></a>
          <a href="#"><i class='bx bxl-instagram'></i></a>
          <a href="#"><i class='bx bxl-twitter'></i></a>
          
        </div>
      </div>
      <div class="bottom-footer">
        <div class="location">
          <a href="../html/locations.html"><span class="material-symbols-outlined">
            location_on
              </span></i> locations</a>
            </div>
            <div class="terms-privace">
              <a href="#">privace plolicy</a>
              <span style="color: gray; font-size: 11px;">&</span>
              <a href="#">terms & condtions</a>
            </div>
          </div>
        </footer>     

        <span class="fade1"></span>
<span class="fade2"></span>


        <script src="../js/home-page.js?v=<?php echo time(); ?>"></script>
        <script>
    <?php if (!empty($login_error)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof showLoginForm === 'function') {
                showLoginForm();
            } else {
                console.log('showLoginForm function is not available.');
            }
        });
    <?php endif; ?>
    </script>

      </body>
      </html>
      

