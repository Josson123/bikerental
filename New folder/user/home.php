<?php
       include("includes.php"); // This should include the DB connection and session management
   
       $conn = new mysqli("localhost", "root", "", "bikerental");

       // Check connection
       if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
}

       if (isset($_POST['check_availability'])) {
        $pickup_date = $_POST['pickup_date'];
        $dropoff_date = $_POST['dropoff_date'];
        $bike_class = $_POST['bike_class'];
    
        // Query to fetch available bikes
        $query = "SELECT * FROM bike WHERE booking_status = 0 AND bike_class = '$bike_class'";
        $result = $conn->query($query);
        $available_bikes = [];
    
        while ($row = $result->fetch_assoc()) {
            $available_bikes[] = $row;
        }
    
        echo json_encode($available_bikes);
        exit;
    }
   
    ?>  



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bike Rental System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  
</head>
<body>
    <main class="container">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true): ?>
        <div class="alert alert-success mt-4" role="alert">
            Welcome, <?php echo $_SESSION['username']; ?>! You have successfully logged in.
        </div>
        <?php endif; ?>

        <!-- Form Section -->
        <section class="rental-form">
            <h2>Rent a Bike</h2>
            <form id="bike-form">
                <div class="mb-3">
                    <label for="pickup-date" class="form-label">Pickup Date:</label>
                    <input type="date" id="pickup-date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="dropoff-date" class="form-label">Drop-off Date:</label>
                    <input type="date" id="dropoff-date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="bike-class" class="form-label">Bike Class:</label>
                    <select id="bike-class" class="form-select" required>
                        <option value="standard">Standard</option>
                        <option value="mountain">Mountain</option>
                        <option value="premium">Premium</option>
                    </select>
                </div>
                <button type="button" class="btn btn-primary w-100" id="check-availability-btn">Check Availability</button>
            </form>

            <!-- Cards Section -->
            <div id="bikes-list" class="row mt-4"></div>
        </section>
    </main>

    <script>
        $(document).ready(function() {
            $('#check-availability-btn').click(function() {
                const pickupDate = $('#pickup-date').val();
                const dropoffDate = $('#dropoff-date').val();
                const bikeClass = $('#bike-class').val();

                // Ensure both dates are selected
                if (!pickupDate || !dropoffDate) {
                    alert('Please insert both Pickup and Drop-off dates.');
                    return;
                }

                // AJAX request to fetch available bikes
                $.ajax({
                    url: 'check_bike_availability.php',
                    type: 'POST',
                    data: {
                        pickup_date: pickupDate,
                        dropoff_date: dropoffDate,
                        bike_class: bikeClass
                    },
                    success: function(response) {
                        $('#bikes-list').html(response);
                    },
                    error: function() {
                        alert('Error fetching available bikes');
                    }
                });
            });
        });
    </script>
</body>
</html>
