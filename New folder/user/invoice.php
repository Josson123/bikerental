<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bikerental");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the data is coming from check_bike_availability.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bike_no = $_POST['bike_no'];
    $pickup_date = $_POST['pickup_date'];
    $dropoff_date = $_POST['dropoff_date'];

    // Fetch bike details
    $query = "SELECT * FROM bike WHERE bike_no = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $bike_no);
    $stmt->execute();
    $result = $stmt->get_result();
    $bike = $result->fetch_assoc();

    // Calculate number of days
    $pickup = new DateTime($pickup_date);
    $dropoff = new DateTime($dropoff_date);
    
    // Calculate days difference, add 1 day if dates are different
    if ($pickup == $dropoff) {
        $num_days = 1;  // Same day is counted as one day
    } else {
        $interval = $pickup->diff($dropoff);
        $num_days = $interval->days + 1; // Add 1 day for inclusive booking
    }

    // Calculate total price
    $total_price = $num_days * $bike['price'];
}

if (isset($_POST['confirm_booking'])) {
    // Extract username from session (assumes the username is stored in session)
    $username = $_SESSION['username']; // Adjust based on how the username is stored in the session

    // Get current date for booking date
    $booking_date = date('Y-m-d H:i:s'); // Current date and time

    // Update bike status to booked
    $update_query = "UPDATE bike SET booking_status = 1 WHERE bike_no = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $bike_no);
    $stmt->execute();

    // Insert into booking table
    $insert_query = "INSERT INTO booking (pickup_date, dropoff_date, bike_no, user_name, booking_date) 
                     VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("ssiss", $pickup_date, $dropoff_date, $bike_no, $username, $booking_date);
    $stmt->execute();

    // Redirect to booking success page
    header("Location: booking_success.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <main class="container mt-5">
        <h2><center><b><u>INVOICE</u></b></center></h2>
        <div class="card">
            <!-- Display the image from the stored path -->
            <img src="<?php echo isset($bike['bike_img']) ? htmlspecialchars($bike['bike_img']) : 'default.jpg'; ?>" class="card-img-top" alt="<?php echo isset($bike['bike_name']) ? htmlspecialchars($bike['bike_name']) : 'No bike'; ?>" style="height: 300px; object-fit: contain;border-radius: 50%;">
            <div class="card-body">
                 <center>
                <h5 class="card-title"><?php echo isset($bike['bike_name']) ? htmlspecialchars($bike['bike_name']) : 'Unknown Bike'; ?></h5>
                <p class="card-text"><b>Class:</b> <?php echo isset($bike['bike_class']) ? htmlspecialchars($bike['bike_class']) : 'N/A'; ?></p>
                <p class="card-text"><b>Brand:</b> <?php echo isset($bike['brand']) ? htmlspecialchars($bike['brand']) : 'N/A'; ?></p>
                <p class="card-text"><b>Price per day:</b> $<?php echo isset($bike['price']) ? htmlspecialchars($bike['price']) : '0'; ?></p>
                <p class="card-text"><b>Total Rent for <?php echo $num_days; ?> days:</b> <strong>$<?php echo $total_price; ?></strong></p>
                 </center>
            </div>
        </div>

        <!-- Confirm Booking Button -->
        <form method="POST" action="invoice.php">
            <input type="hidden" name="bike_no" value="<?php echo $bike_no; ?>">
            <input type="hidden" name="pickup_date" value="<?php echo $pickup_date; ?>">
            <input type="hidden" name="dropoff_date" value="<?php echo $dropoff_date; ?>">
            <button type="submit" name="confirm_booking" class="btn btn-success mt-4 w-100">Confirm Booking</button>
        </form>
    </main>
</body>
</html>
