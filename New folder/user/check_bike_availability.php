<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bikerental");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Server-side validation for dates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup_date = $_POST['pickup_date'];
    $dropoff_date = $_POST['dropoff_date'];
    $bike_class = $_POST['bike_class'];

    // Current date
    $current_date = date('Y-m-d');

    // Check if pickup date is before the current date
    if ($pickup_date < $current_date) {
        echo '<script>alert("Pickup date cannot be earlier than today."); window.location.href = "home.php";</script>';
        exit;
    }

    // Check if drop-off date is earlier than pickup date
    if ($dropoff_date < $pickup_date) {
        echo '<script>alert("Drop-off date cannot be earlier than pickup date."); window.location.href = "home.php";</script>';
        exit;
    }

    // SQL query to check available bikes
    $sql = "SELECT bike_no, bike_name, bike_class, bike_img, price, brand 
            FROM bike 
            WHERE booking_status = 0 AND bike_class = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $bike_class); // bind bike_class to the SQL query
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // For each available bike, create a card
            echo '<div class="col-md-6 col-lg-4 mb-4"> 
                    <div class="card shadow-sm h-100" style="border-radius: 15px; overflow: hidden;"> 
                        <img src="' . htmlspecialchars($row['bike_img']) . '" class="card-img-top" alt="' . htmlspecialchars($row['bike_name']) . '" style="height: 100px; object-fit: cover;"> 
                        <div class="card-body d-flex flex-column"> 
                            <h5 class="card-title">' . htmlspecialchars($row['bike_name']) . '</h5>
                            <p class="card-text mb-2">Class: ' . htmlspecialchars($row['bike_class']) . '</p> 
                            <p class="card-text mb-4">Price: $' . htmlspecialchars($row['price']) . '</p>
                            <form action="invoice.php" method="POST" class="mt-auto">';
            // Check if the user is logged in
            if (!isset($_SESSION['email'])) {
                echo '<button type="button" class="btn btn-warning" onclick="alert(\'Please login to book a bike.\')">Book Now</button>';
            } else {
                echo '<input type="hidden" name="bike_name" value="' . htmlspecialchars($row['bike_name']) . '">
                      <input type="hidden" name="bike_class" value="' . htmlspecialchars($row['bike_class']) . '">
                      <input type="hidden" name="bike_img" value="' . htmlspecialchars($row['bike_img']) . '">
                      <input type="hidden" name="price_per_day" value="' . htmlspecialchars($row['price']) . '">
                      <input type="hidden" name="bike_no" value="' . htmlspecialchars($row['bike_no']) . '">
                      <input type="hidden" name="brand" value="' . htmlspecialchars($row['brand']) . '">
                      <input type="hidden" name="pickup_date" value="' . htmlspecialchars($pickup_date) . '">
                      <input type="hidden" name="dropoff_date" value="' . htmlspecialchars($dropoff_date) . '">
                      <button type="submit" class="btn btn-success">Book Now</button>';
            }
            echo '</form></div></div></div>';
        }
    } else {
        echo '<p>No bikes available for the selected dates and class.</p>';
    }

    $stmt->close();
    $conn->close();
}
?>


