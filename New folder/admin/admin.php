<?php
session_start();

// Check if admin is logged in, if not redirect to admin_login.php
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "bikerental");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch relevant statistics (total bikes, total bookings, bikes available, bookings today)
$totalBikesQuery = "SELECT COUNT(*) AS total_bikes FROM bike";
$totalBikesResult = $conn->query($totalBikesQuery);
$totalBikes = $totalBikesResult->fetch_assoc()['total_bikes'];

$totalBookingsQuery = "SELECT COUNT(*) AS total_bookings FROM booking";
$totalBookingsResult = $conn->query($totalBookingsQuery);
$totalBookings = $totalBookingsResult->fetch_assoc()['total_bookings'];

// Bikes available
$bikesAvailableQuery = "SELECT COUNT(*) AS bikes_available FROM bike WHERE booking_status = '0'";
$bikesAvailableResult = $conn->query($bikesAvailableQuery);
$bikesAvailable = $bikesAvailableResult->fetch_assoc()['bikes_available'];

// Bookings today
$bookingsTodayQuery = "SELECT COUNT(*) AS bookings_today FROM booking WHERE DATE(booking_date) = CURDATE()";
$bookingsTodayResult = $conn->query($bookingsTodayQuery);
$bookingsToday = $bookingsTodayResult->fetch_assoc()['bookings_today'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Include Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .dashboard-card {
            border-radius: 10px;
            margin: 10px 0;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .admin-header {
            background-color: #343a40;
            color: white;
            padding: 10px;
            text-align: center;
            border-radius: 10px;
        }
        .admin-actions {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <h1>Welcome, Admin: <?php echo $_SESSION['username']; ?>!</h1>
    </div>

    <!-- Main Dashboard Content -->
    <div class="container mt-4">
        <div class="row">
            <!-- Admin Actions on the left -->
            <div class="col-md-3 admin-actions">
                <h2>Admin Actions</h2>
                <ul class="list-group text-start">
                    <li class="list-group-item"><a href="admin_add_bike.php">Add Bike</a></li>
                    <li class="list-group-item"><a href="admin_manage_bikes.php">Manage Bikes</a></li>
                    <li class="list-group-item"><a href="admin_view_bookings.php">View Bookings</a></li>
                    <li class="list-group-item"><a href="admin_users.php">Users</a></li>
                    <li class="list-group-item"><a href="admin_logout.php">Logout</a></li>
                </ul>
            </div>

            <!-- Dashboard Overview Cards on the right -->
            <div class="col-md-9">
                <h2>Dashboard Overview</h2>
                <div class="row">
                    <div class="col-md-3 dashboard-card bg-primary text-white">
                        <h4>Total Bikes</h4>
                        <p><?php echo $totalBikes; ?></p>
                    </div>
                    <div class="col-md-3 dashboard-card bg-success text-white">
                        <h4>Total Bookings</h4>
                        <p><?php echo $totalBookings; ?></p>
                    </div>
                    <div class="col-md-3 dashboard-card bg-warning text-white">
                        <h4>Bikes Available</h4>
                        <p><?php echo $bikesAvailable; ?></p>
                    </div>
                    <div class="col-md-3 dashboard-card bg-danger text-white">
                        <h4>Bookings Today</h4>
                        
                        <p><?php echo $bookingsToday; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
