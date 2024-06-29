<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Initialize variables
$success_message = $error_message = '';

// Delete instructor if ID is provided
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Prepare a delete statement
    $sql = "DELETE FROM instructors WHERE id = ?";

    if ($stmt = $conn->prepare($sql)) {
        // Bind variables to the prepared statement as parameters
        $stmt->bind_param("i", $delete_id);

        // Attempt to execute the prepared statement
        if ($stmt->execute()) {
            $success_message = "Instructor deleted successfully.";
        } else {
            $error_message = "Oops! Something went wrong. Please try again later.";
        }

        // Close statement
        $stmt->close();
    }
}

// Fetch all instructors
$sql = "SELECT id, username, first_name, last_name FROM instructors";
$result = $conn->query($sql);
$instructors = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $instructors[] = $row;
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
    <title>Manage Instructors</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include('admin_navbar.php'); ?>

    <div class="container mt-5">
        <h2>Manage Instructors</h2>
        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($instructors as $instructor): ?>
                    <tr>
                        <td><?php echo $instructor['id']; ?></td>
                        <td><?php echo htmlspecialchars($instructor['username']); ?></td>
                        <td><?php echo htmlspecialchars($instructor['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($instructor['last_name']); ?></td>
                        <td>
                            <a href="edit_instructor.php?id=<?php echo $instructor['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="view_instructors.php?delete_id=<?php echo $instructor['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this instructor?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
