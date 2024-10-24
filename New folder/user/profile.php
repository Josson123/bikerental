<?php
include("includes.php");
$conn = new mysqli("localhost", "root", "", "bikerental");

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in by checking session variable
if (!isset($_SESSION['username'])) {
    // If user is not logged in, show alert and redirect to home.php
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
    echo "User not found.";
    exit();
}

// If the form is submitted, update the user's profile
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_changes'])) {
    $new_email = $_POST['email'];
    $new_phone_no = $_POST['phone_no'];

    // Update the user's details in the database
    $update_sql = "UPDATE user SET email = ?, phone_no = ? WHERE user_name = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sss", $new_email, $new_phone_no, $username);

    if ($update_stmt->execute()) {
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
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title text-center">My Profile</h5>
                    </div>
                    <div class="card-body">
                        <!-- Display user's current profile details -->
                        <table class="table table-borderless mt-3">
                            <tbody>
                                <tr>
                                    <th>Username:</th>
                                    <td><?php echo $user['user_name']; ?></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td><?php echo $user['email']; ?></td>
                                </tr>
                                <tr>
                                    <th>Phone Number:</th>
                                    <td><?php echo $user['phone_no']; ?></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- "Edit Profile" button triggers form to edit details -->
                        <div class="text-center mt-5">
                            <button id="editBtn" class="btn btn-primary">Edit Profile</button>
                        </div>

                        <!-- Hidden form for editing profile -->
                        <form id="editForm" action="profile.php" method="POST" style="display:none;" class="mt-4">
                            <div class="form-group mb-3">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="phone_no">Phone Number:</label>
                                <input type="text" class="form-control" name="phone_no" value="<?php echo $user['phone_no']; ?>" required>
                            </div>
                            <button type="submit" name="save_changes" class="btn btn-success">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show the edit form when the "Edit Profile" button is clicked
        document.getElementById("editBtn").addEventListener("click", function() {
            document.getElementById("editForm").style.display = "block";
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
