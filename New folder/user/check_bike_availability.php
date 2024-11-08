<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bikerental");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup_date = $_POST['pickup_date'];
    $dropoff_date = $_POST['dropoff_date'];
    $bike_class = $_POST['bike_class'];
    $current_date = date('Y-m-d');

    // Validate dates
    if ($pickup_date < $current_date) {
        echo '<script>alert("Pickup date cannot be earlier than today."); window.location.href = "home.php";</script>';
        exit;
    }

    if ($dropoff_date < $pickup_date) {
        echo '<script>alert("Drop-off date cannot be earlier than pickup date."); window.location.href = "home.php";</script>';
        exit;
    }

    // Adjust SQL query based on the selected bike_class
    if ($bike_class === "all") {
        $sql = "SELECT bike_no, bike_name, bike_class, bike_img, price, brand 
                FROM bike 
                WHERE booking_status = 0";
        $stmt = $conn->prepare($sql);
    } else {
        $sql = "SELECT bike_no, bike_name, bike_class, bike_img, price, brand 
                FROM bike 
                WHERE booking_status = 0 AND bike_class = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $bike_class);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Full-width container for bike cards
    echo '<div class="container-fluid bg-dark py-4"><div class="row justify-content-center">';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="col-md-6 col-lg-3 mb-4"> 
                    <div class="card shadow-sm h-100" style="border-radius: 10px; overflow: hidden; background-color: #333; color: #fff;"> 
                        <img src="' . htmlspecialchars($row['bike_img']) . '" class="card-img-top" alt="' . htmlspecialchars($row['bike_name']) . '" style="height: 150px; object-fit: cover;"> 
                        <div class="card-body"> 
                            <h5 class="card-title">' . htmlspecialchars($row['bike_name']) . '</h5>
                            <p class="card-text">Class: ' . htmlspecialchars($row['bike_class']) . '</p> 
                            <p class="card-text">Price: $' . htmlspecialchars($row['price']) . '</p>
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
        echo '<p class="text-center text-white">No bikes available for the selected dates and class.</p>';
    }

    echo '</div></div>'; // Close row and container
    $stmt->close();
    $conn->close();
}
?>
