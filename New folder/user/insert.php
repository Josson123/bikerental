<?php
$conn = mysqli_connect("localhost", "root", "", "bikerental");

if ($conn->connect_error) {
    die("Unsuccessful: " . $conn->connect_error);
} else {
    echo "Database connected successfully<br>";

    if (isset($_POST['submit'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $number = $_POST['phoneno'];

        // Print values for debugging
        echo "Username: " . $username . "<br>";
        echo "Email: " . $email . "<br>";
        echo "Password: " . $password . "<br>";
        echo "Number: " . $number . "<br>";

        // Encrypt password
        $pass_encode = password_hash($password, PASSWORD_DEFAULT);

        // SQL insert query
        $insert = "INSERT INTO user (user_name, email, password, phone_no) VALUES (?, ?, ?, ?)";

        if ($stmt = $conn->prepare($insert)) {
            $stmt->bind_param("ssss", $username, $email, $pass_encode, $number);

            // Execute the prepared statement
            if ($stmt->execute()) {
                echo "New record created successfully";
                // Redirect to login.php after successful insertion
                header('Location: login.php');
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    }
}

mysqli_close($conn);
exit();
?>
