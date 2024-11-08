<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bikerental");


if ($conn->connect_error) {

    die("Connection failed: " . $conn->connect_error);

}


// Initialize variables for form fields

$pickup_date = isset($_GET['pickup_date']) ? $_GET['pickup_date'] : '';

$dropoff_date = isset($_GET['dropoff_date']) ? $_GET['dropoff_date'] : '';

$bike_class = isset($_GET['bike_class']) ? $_GET['bike_class'] : 'all';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent a Bike - RevRides Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="home_style.css">
    <style>
        /* Dark background for the entire page */
        body {
            background-color: #333;
            color: #fff;
        }

        /* Cards Section Background */
        #bikes-list {
            background-color: #000;
            padding: 20px;
            border-radius: 10px;
        }

        /* Footer Styling */
        footer {
            background-color: #222;
            color: #fff;
            padding: 20px;
            text-align: left;
            margin-top: 40px;
        }
        .footer-content {
            max-width: 1250px;
            margin: auto;
        }
        .footer-content p, .footer-content h5 {
            margin: 0;
            line-height: 1.6;
        }

        /* Social Media Section */
        .social-media {
            margin-top: 10px;
        }
        .social-media img {
            width: 24px;
            height: 24px;
            margin-right: 8px;
        }
    </style>
</head>
<body>

    <!-- Navbar Section -->
    <nav>
        <div class="menu">
            <div class="logo"><a href="home.php">RevRides Rental</a></div>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="profile.php">Profile</a></li>
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                    <li><a href="logout.php" onclick="return confirmLogout()">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Hero Section with Background Image -->
    <div class="img">
        <div class="center">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                <p id="welcome-message" class="text-white">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            <?php endif; ?>
            <h1 class="title">RevRides Rental</h1>
            <h3 class="sub_title">Find Your Perfect Ride</h3>
            <div class="btns">
                <button onclick="scrollToForm()">Rent a Bike</button>
            </div>
        </div>
    </div>

    <!-- Background Image Section -->
    <section class="bg-image-section">
   
    <!-- Rental Form Section with Gradient Background -->
    <main class="container mt-5">

    <section class="rental-form">

        <h2>Rent a Bike</h2>

        <form id="bike-form">

            <div class="form-row">

                <div class="form-group">

                    <label for="pickup-date" class="form-label">Pickup Date:</label>

                    <input type="date" id="pickup-date" class="form-control" value="<?php echo htmlspecialchars($pickup_date); ?>" required>

                </div>

                <div class="form-group">

                    <label for="dropoff-date" class="form-label">Drop-off Date:</label>

                    <input type="date" id="dropoff-date" class="form-control" value="<?php echo htmlspecialchars($dropoff_date); ?>" required>

                </div>

                <div class="form-group">

                    <label for="bike-class" class="form-label">Bike Class:</label>

                    <select id="bike-class" class="form-select" required style="background: rgba(0, 0, 0, 0.7); color: #fff; border: 1px solid rgba(255, 255, 255, 0.3); padding: 0.8rem; border-radius: 5px; transition: background 0.3s ease;">

                        <option value="all" <?php echo $bike_class === 'all' ? 'selected' : ''; ?>>All</option>

                        <option value="standard" <?php echo $bike_class === 'standard' ? 'selected' : ''; ?>>Standard</option>

                        <option value="mountain" <?php echo $bike_class === 'mountain' ? 'selected' : ''; ?>>Mountain</option>

                        <option value="premium" <?php echo $bike_class === 'premium' ? 'selected' : ''; ?>>Premium</option>

                    </select>

                </div>

            </div>

            <button type="button" class="btn btn-primary w-100" id="check-availability-btn" onclick="scrollToForm()">Check Availability</button>

        </form>

    </section>
    </section>
        
        <!-- Available Bikes Section -->
        <div id="bikes-list" class="row mx-0 mt-0 mb-0" required style=" border-radius: 0;margin-bottom: 0;"></div>
    
    </main>


    <section  class="full-width-section">
    <div class="contact-us">
            <h5>Contact Us</h5>
            <p>Head Office: Kottayam<br>
            Pickup and Dropoff Point: Changanacherry<br>
            For Enquiry: +91 8547236599<br>
            Email: revridesrental@gmail.com</p>
            <div class="social-media">
                <p>Follow us on: 
                    <img src="../images/home/FB logo.png" alt="Facebook">
                    <img src="../images/home/instagram.png" alt="Instagram">
                </p>
            </div>
        </div>
    </section> 
   
    <!-- Footer Section -->
    <footer class="small-footer">   

       <p style="text-align: center;">©RevRides Rental. All Rights Reserved</p>
    </footer>

    <!-- JavaScript for Availability Check, Welcome Message Fade-Out, Scroll, and Logout Confirmation -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Hide welcome message after 2 seconds
        $(document).ready(function() {
            setTimeout(() => {
                $('#welcome-message').addClass('hide-message');
            }, 2000);

            // Scroll to the rental form
            window.scrollToForm = function() {
                document.getElementById('bike-form').scrollIntoView({ behavior: 'smooth' });
                document.getElementById('bikes-list').scrollIntoView({ behavior: 'smooth' });
            };
        });

        // Logout confirmation
        function confirmLogout() {
            alert("Logout successful");
            return true;
        }

        // Check availability via AJAX
        $('#check-availability-btn').click(function() {
            const pickupDate = $('#pickup-date').val();
            const dropoffDate = $('#dropoff-date').val();
            const bikeClass = $('#bike-class').val();

            if (!pickupDate || !dropoffDate) {
                alert('Please insert both Pickup and Drop-off dates.');
                return;
            }

            $.ajax({
                url: 'check_bike_availability.php',
                type: 'POST',
                data: {
                    pickup_date: pickupDate,
                    dropoff_date: dropoffDate,
                    bike_class: bikeClass
                },
                success: function(response) {
                    $('#bikes-list').html(response).hide().fadeIn(500);
                },
                error: function() {
                    alert('Error fetching available bikes');
                }
            });
        });
    </script>
</body>
</html>
