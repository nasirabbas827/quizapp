<?php
session_start();
include('config.php');

// Check if the user is logged in as an instructor
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: instructor_login.php");
    exit;
}

// Fetch data for dashboard
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"))['total'];
$totalLessons = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM lessons"))['total'];
$totalExercises = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM exercises"))['total'];

// Fetch all feedbacks with usernames
$stmt = $conn->prepare("SELECT f.*, u.username FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$feedbacks = [];
while ($row = $result->fetch_assoc()) {
    $feedbacks[] = $row;
}
$stmt->close();

// Handle delete request
if (isset($_POST['delete_feedback'])) {
    $feedback_id = $_POST['delete_feedback'];

    $delete_stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $delete_stmt->bind_param("i", $feedback_id);
    $delete_stmt->execute();
    $delete_stmt->close();

    // Redirect to prevent duplicate submissions
    header("Location: instructor_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('instructor_navbar.php'); ?>

<div class="container mt-5">
    <h2 class="text-center">Instructor Dashboard</h2>
    <div class="row mt-4">
        <!-- Total Users Card -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text"><?php echo $totalUsers; ?></p>
                </div>
            </div>
        </div>

       

        <!-- Total Lessons Card -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Lessons</h5>
                    <p class="card-text"><?php echo $totalLessons; ?></p>
                </div>
            </div>
        </div>

        <!-- Total Exercises Card -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Exercises</h5>
                    <p class="card-text"><?php echo $totalExercises; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5">
        <h3>All Feedbacks</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Feedback Text</th>
                    <th>Rating</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feedbacks as $feedback): ?>
                <tr>
                    <td><?php echo $feedback['id']; ?></td>
                    <td><?php echo htmlspecialchars($feedback['username']); ?></td>
                    <td><?php echo htmlspecialchars($feedback['feedback_text']); ?></td>
                    <td><?php echo $feedback['rating']; ?></td>
                    <td><?php echo date("Y-m-d H:i:s", strtotime($feedback['created_at'])); ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="delete_feedback" value="<?php echo $feedback['id']; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
