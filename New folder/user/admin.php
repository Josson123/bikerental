<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin') {
    header('Location: adminlogin.php');
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "bikerental");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch users and bookings from the database
$usersQuery = "SELECT id, user_name, email, phone_no FROM user";
$bookingsQuery = "SELECT id, user_name, bike_class, pickup_date, dropoff_date FROM bookings";
$usersResult = $conn->query($usersQuery);
$bookingsResult = $conn->query($bookingsQuery);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin.php">Admin Dashboard</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Welcome Message -->
    <div class="container mt-4">
        <h1>Welcome, Admin!</h1>
        <p class="lead">Here you can manage users and view bookings.</p>
    </div>

    <!-- Manage Users Section -->
    <div class="container mt-5">
        <h2>Manage Users</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $usersResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['user_name']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['phone_no']; ?></td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- View Bookings Section -->
    <div class="container mt-5">
        <h2>View Bookings</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Username</th>
                    <th>Bike Class</th>
                    <th>Pickup Date</th>
                    <th>Drop-off Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($booking = $bookingsResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $booking['id']; ?></td>
                    <td><?php echo $booking['user_name']; ?></td>
                    <td><?php echo $booking['bike_class']; ?></td>
                    <td><?php echo $booking['pickup_date']; ?></td>
                    <td><?php echo $booking['dropoff_date']; ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p>&copy; 2024 Bike Rental System. All rights reserved.</p>
    </footer>
</body>
</html>
