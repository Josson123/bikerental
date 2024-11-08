<?php
//session_start();
include("includes.php");

$conn = new mysqli("localhost", "root", "", "bikerental");

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in by checking session variable
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Please login to see profile');</script>";
    echo "<script>window.location.href='home.php';</script>";  
    exit();
}

// Fetch the logged-in user's information from the database
$username = $_SESSION['username'];
$sql = "SELECT user_name, email, phone_no FROM user WHERE user_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the user's data
    $user = $result->fetch_assoc();
} else {
    echo "User  not found.";
    exit();
}

// Fetch current bookings
$current_bookings_sql = "SELECT booking_no, pickup_date, dropoff_date, bike_no FROM booking WHERE user_name = ? AND dropoff_date >= CURDATE()";
$current_bookings_stmt = $conn->prepare($current_bookings_sql);
$current_bookings_stmt->bind_param("s", $username);
$current_bookings_stmt->execute();
$current_bookings_result = $current_bookings_stmt->get_result();

// Fetch past completed bookings
$past_bookings_sql = "SELECT booking_no, pickup_date, dropoff_date, bike_no FROM booking WHERE user_name = ? AND dropoff_date < CURDATE() ORDER BY dropoff_date DESC LIMIT 3";
$past_bookings_stmt = $conn->prepare($past_bookings_sql);
$past_bookings_stmt->bind_param("s", $username);
$past_bookings_stmt->execute();
$past_bookings_result = $past_bookings_stmt->get_result();

// If the form is submitted, update the user's profile
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_changes'])) {
    $new_email = $_POST['email'];
    $new_phone_no = $_POST['phone_no'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Update the user's details in the database
    $update_sql = "UPDATE user SET email = ?, phone_no = ? WHERE user_name = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sss", $new_email, $new_phone_no, $username);

    if ($update_stmt->execute()) {
        // Update password if new password is provided
        if (!empty($new_password) && $new_password === $confirm_password) {
            // Verify current password
            $password_check_sql = "SELECT password FROM user WHERE user_name = ?";
            $password_check_stmt = $conn->prepare($password_check_sql);
            $password_check_stmt->bind_param("s", $username);
            $password_check_stmt->execute();
            $password_result = $password_check_stmt->get_result();
            $password_row = $password_result->fetch_assoc();

            if (password_verify($current_password, $password_row['password'])) {
                // Hash the new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_password_sql = "UPDATE user SET password = ? WHERE user_name = ?";
                $update_password_stmt = $conn->prepare($update_password_sql);
                $update_password_stmt->bind_param("ss", $hashed_password, $username);
                $update_password_stmt->execute();
            } else {
                echo "<script>alert('Current password is incorrect.');</script>";
            }
        }
        echo "<script>alert('Profile updated successfully');</script>";
        // Refresh the page to show updated details
        echo "<script>window.location.href='profile.php';</script>"; 
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body, html {
      margin: 0;
      padding: 0;
      overflow-x: hidden; /* Prevent horizontal scrollbar */
      height: 100%;
      font-family: Arial, sans-serif;
    }

    .bg-img{
        background: url("../images/home/profile.jpg") repeat center center/cover;
        width: 100%;
        height: 100vh;
        position: relative;
        z-index: 1;
    }

    /* Background Gradient */
    .gradient-bg {
      background: linear-gradient(to right, #e0c397, #8b5e3c, #e0c397); /* Sandy to earthy brown gradient */
      width: 100%;
      min-height: 100vh;
      position: relative;
    }

    .content-container {
      position: relative;
      padding-top: 100px; /* Adjust for header space if needed */
      z-index: 2;
    }

    /* Profile Section */
    .profile-section, .current-bookings, .past-bookings {
      padding: 20px;
      border-radius: 5px;
      margin-top: 20px;
      margin-bottom: 20px;
      background-color: rgba(249, 249, 249, 0.9); /* Slightly translucent white */
    }

    /* Current Bookings Section */
    .current-bookings {
      background-color: rgba(231, 243, 254, 0.9); /* Light blue */
      border: 1px solid #2196F3;
    }

    /* Past Bookings Section */
    .past-bookings {
      background-color: rgba(252, 228, 236, 0.9); /* Light pink */
      border: 1px solid #E91E63;
    }
  </style>
</head>

<body>
<div class=bg-img>
  <div class="gradient-bg">
    <div class="container content-container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <!-- Profile Section -->
          <div class="profile-section">
            <h5 class="text-center">My Profile</h5>
            <table class="table table-borderless mt-3">
              <tbody>
                <tr>
                  <th>Username:</th>
                  <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                </tr>
                <tr>
                  <th>Email:</th>
                  <td><?php echo htmlspecialchars($user['email']); ?></td>
                </tr>
                <tr>
                  <th>Phone Number:</th>
                  <td><?php echo htmlspecialchars($user['phone_no']); ?></td>
                </tr>
              </tbody>
            </table>
            <div class="text-center mt-5">
              <button id="editBtn" class="btn btn-primary">Edit Profile</button>
            </div>
            <form id="editForm" action="profile.php" method="POST" style="display:none;" class="mt-4">
              <div class="form-group mb-3">
                <label for="email">Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
              </div>
              <div class="form-group mb-3">
                <label for="phone_no">Phone Number:</label>
                <input type="text" class="form-control" name="phone_no" value="<?php echo htmlspecialchars($user['phone_no']); ?>" required>
              </div>
              <div class="form-group mb-3">
                <label for="current_password">Current Password:</label>
                <div class="input-group">
                  <input type="password" class="form-control" name="current_password">
                  <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">Show</button>
                </div>
              </div>
              <div class="form-group mb-3">
                <label for="new_password">New Password:</label>
                <div class="input-group">
                  <input type="password" class="form-control" name="new_password">
                  <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">Show</button>
                </div>
              </div>
              <div class="form-group mb-3">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" class="form-control" name="confirm_password">
              </div>
              <button type="submit" name="save_changes" class="btn btn-success">Save Changes</button>
            </form>
          </div>

          <!-- Current Bookings Section -->
          <div class="current-bookings">
            <h5>Current Bookings</h5>
            <table class="table">
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Pickup Date</th>
                  <th>Dropoff Date</th>
                  <th>Bike No</th>
                  <th>Details</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($booking = $current_bookings_result->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($booking['booking_no']); ?></td>
                    <td><?php echo htmlspecialchars($booking['pickup_date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['dropoff_date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['bike_no']); ?></td>
                    <td><a href="booking_details.php?booking_id=<?php echo htmlspecialchars($booking['booking_no']); ?>" class="btn btn-info">View Details</a></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

          <!-- Past Bookings Section -->
          <div class="past-bookings">
            <h5>Past Bookings</h5>
            <table class="table">
              <thead>
                <tr>
                  <th>Booking ID</th>
                  <th>Pickup Date</th>
                  <th>Dropoff Date</th>
                  <th>Bike No</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($past_booking = $past_bookings_result->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($past_booking['booking_no']); ?></td>
                    <td><?php echo htmlspecialchars($past_booking['pickup_date']); ?></td>
                    <td><?php echo htmlspecialchars($past_booking['dropoff_date']); ?></td>
                    <td><?php echo htmlspecialchars($past_booking['bike_no']); ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
  <!-- JavaScript -->
  <script>
    // Show the edit form when the "Edit Profile" button is clicked
    document.getElementById("editBtn").addEventListener("click", function() {
      document.getElementById("editForm").style.display = "block";
    });

    // Toggle password visibility
    document.getElementById("toggleCurrentPassword").addEventListener("click", function() {
      const passwordInput = document.querySelector('input[name="current_password"]');
      passwordInput.type = passwordInput.type === "password" ? "text" : "password";
      this.textContent = passwordInput.type === "password" ? "Show" : "Hide";
    });

    document.getElementById("toggleNewPassword").addEventListener("click", function() {
      const passwordInput = document.querySelector('input[name="new_password"]');
      passwordInput.type = passwordInput.type === "password" ? "text" : "password";
      this.textContent = passwordInput.type === "password" ? "Show" : "Hide";
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>