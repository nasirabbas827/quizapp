<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Initialize variables
$id = $_GET['id'] ?? null;
$success_message = $error_message = '';

// Fetch instructor details
$sql = "SELECT * FROM instructors WHERE id = ?";
if ($stmt = $conn->prepare($sql)) {
    // Bind the parameter
    $stmt->bind_param("i", $id);

    // Attempt to execute the prepared statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $instructor = $result->fetch_assoc();
        } else {
            $error_message = "Instructor not found.";
        }
    } else {
        $error_message = "Oops! Something went wrong. Please try again later.";
    }

    // Close statement
    $stmt->close();
}

// Update instructor details
if (isset($_POST['update'])) {
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password = $_POST['password'];
    
    // Check if password is provided
    if (!empty($password)) {
        // Update password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE instructors SET username = ?, first_name = ?, last_name = ?, password = ? WHERE id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param("ssssi", $username, $first_name, $last_name, $hashed_password, $id);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $success_message = "Instructor updated successfully.";
            } else {
                $error_message = "Oops! Something went wrong. Please try again later.";
            }
            
            // Close statement
            $stmt->close();
        }
    } else {
        // Update without changing password
        $sql = "UPDATE instructors SET username = ?, first_name = ?, last_name = ? WHERE id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind parameters
            $stmt->bind_param("sssi", $username, $first_name, $last_name, $id);
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $success_message = "Instructor updated successfully.";
            } else {
                $error_message = "Oops! Something went wrong. Please try again later.";
            }
            
            // Close statement
            $stmt->close();
        }
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Instructor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5">
        <h2>Edit Instructor</h2>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (isset($instructor)): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($instructor['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($instructor['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($instructor['last_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">New Password (leave blank to keep current)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <button type="submit" name="update" class="btn btn-primary">Update Instructor</button>
                <a href="view_instructors.php" class="btn btn-secondary">Cancel</a>
            </form>
        <?php else: ?>
            <div class="alert alert-danger">Instructor not found.</div>
        <?php endif; ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
