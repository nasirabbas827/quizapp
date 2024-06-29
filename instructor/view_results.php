<?php
session_start();
include('config.php');

// Check if the user is logged in as an instructor
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: instructor_login.php");
    exit;
}

// Fetch all users' quiz results including lesson names
$result = $conn->query("SELECT qr.id, qr.user_id, qr.lesson_id, qr.difficulty, qr.total_marks, qr.user_score, qr.created_at, users.username
                        FROM quiz_results qr
                        INNER JOIN users ON qr.user_id = users.id
                        ORDER BY qr.created_at DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>View Results</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include('instructor_navbar.php'); ?>

<div class="container mt-5">
    <h2>Users' Quiz Results</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Username</th>
                <th>Lesson ID</th>
                <th>Difficulty</th>
                <th>Total Marks</th>
                <th>User Score</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo $row['lesson_id']; ?></td>
                    <td><?php echo $row['difficulty']; ?></td>
                    <td><?php echo $row['total_marks']; ?></td>
                    <td><?php echo $row['user_score']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
