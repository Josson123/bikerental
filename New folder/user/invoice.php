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

    <title>Booking Confirmation</title>

    <style>

        body {

            font-family: Arial, sans-serif;

            background-color: rgba(0, 0, 0, 0.5); /* Dark translucent background */

            margin: 0;

            padding: 20px;

            display: flex;

            justify-content: center;

            align-items: center;

            height: 100vh; /* Full height */

        }

        .container {

            max-width: 600px;

            margin: auto;

            background: rgba(255, 255, 255, 0.9); /* Translucent white background */

            padding: 20px;

            border-radius: 8px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);

        }

        .image-container {

            text-align: center;

        }

        .image-container img {

          max-width: 100%;

          height: auto;

          border-radius: 15px; /* Rounded edges */

          box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Optional shadow for depth */

        }

        h1 {

            text-align: center;

            color: #333;

        }

        .details {

            margin: 20px 0;

        }

        .details p {

            margin: 5px 0;

            font-size: 16px;

        }

        .button-container {

            text-align: center;

        }

        .button {

            background-color: #28a745;

            color: white;

            border: none;

            padding: 10px 20px;

            border-radius: 5px;

            cursor: pointer;

            margin: 5px;

            font-size: 16px;

            transition: background-color 0.3s;

        }

        .button.cancel {

            background-color: #dc3545;

        }

        .button:hover {

            opacity: 0.9;

        }

    </style>

</head>
<body>
<div class="container">
    <h1>Booking Confirmation</h1>
    <div class="image-container">
    <img src="<?php echo isset($bike['bike_img']) ? htmlspecialchars($bike['bike_img']) : 'default.jpg'; ?>" class="card-img-top" alt="<?php echo isset($bike['bike_name']) ? htmlspecialchars($bike['bike_name']) : 'No bike'; ?>">
    </div>
    
        <div class="details">
           <h2><b><center><?php echo isset($bike['bike_name']) ? htmlspecialchars($bike['bike_name']) : 'Unknown Bike'; ?></center></b></h2>
           <p><strong>Bike Brand:</strong> <?php echo isset($bike['brand']) ? htmlspecialchars($bike['brand']) : 'N/A'; ?></p>
           <p><strong>Bike Class:</strong> <?php echo isset($bike['bike_class']) ? htmlspecialchars($bike['bike_class']) : 'N/A'; ?></p>
           <p><strong>Rent per day:</strong>$<?php echo isset($bike['price']) ? htmlspecialchars($bike['price']) : '0'; ?></p>
           <p><strong>Total Rent for  <?php echo $num_days; ?>days:$<?php echo $total_price; ?></strong></p>
         </div>

        <!-- Confirm Booking Button -->
        <!-- Confirm Booking Button -->

<form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

<input type="hidden" name="bike_no" value="<?php echo $bike_no; ?>">

<input type="hidden" name="pickup_date" value="<?php echo $pickup_date; ?>">

<input type="hidden" name="dropoff_date" value="<?php echo $dropoff_date; ?>">

<input type="hidden" name="confirm_booking" value="1"> <!-- Added hidden input -->

<div class="button-container">

    <button type="submit" class="button">Confirm Booking</button>

    <button type="button" class="button cancel" onclick="goBackToHome()">Go Back</button>

</div>

</form>
        <script>
               function goBackToHome() {
               const pickupDate = "<?php echo $pickup_date; ?>";

        const dropoffDate = "<?php echo $dropoff_date; ?>";

        const bikeClass = "<?php echo isset($bike['bike_class']) ? htmlspecialchars($bike['bike_class']) : 'all'; ?>"; // Default to 'all' if not set

        window.location.href = `home.php?pickup_date=${pickupDate}&dropoff_date=${dropoffDate}&bike_class=${bikeClass}#bikes-list`;
               }
        </script>
    </main>
</body>
</html>
