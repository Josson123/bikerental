<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bikerental");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loginError = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute the query using parameterized statements
    $stmt = $conn->prepare("SELECT admin, password FROM admin WHERE admin = ?");
    $stmt->bind_param("s", $admin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Login successful
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $admin; // Storing the username in session
            header('Location: admin.php');
            exit();
        } else {
            // Incorrect password
            $loginError = "Incorrect password.";
        }
    } else {
        // Username not found
        $loginError = "Username not found.";
    }

    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Bike Rental</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="register-container">
        <h2>Login</h2>

        <?php if (!empty($loginError)): ?>
            <p style="color:red;"><?php echo $loginError; ?></p>
        <?php elseif (!empty($loginSuccess)): ?>
            <p style="color:green;"><?php echo $loginSuccess; ?></p>
        <?php endif; ?>

        <form action="adminlogin.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>

       <!--<p>New User? <a href="New Profile.php">Sign Up</a></p>-->
    </div>
</body>
</html>