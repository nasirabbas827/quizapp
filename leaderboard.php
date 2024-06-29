<?php
include('config.php');

session_start();



// Fetch all quiz results grouped by user and difficulty
$stmt = $conn->prepare("
    SELECT 
        u.username, 
        qr.difficulty, 
        COUNT(qr.id) AS total_quizzes, 
        SUM(qr.total_marks) AS total_marks, 
        SUM(qr.user_score) AS total_score
    FROM quiz_results qr 
    JOIN users u ON qr.user_id = u.id 
    GROUP BY qr.user_id, qr.difficulty 
    ORDER BY total_score DESC, qr.created_at ASC
");
$stmt->execute();
$result = $stmt->get_result();
$quiz_results = [];
while ($row = $result->fetch_assoc()) {
    $quiz_results[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Leaderboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Leaderboard</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Rank</th>
                <th>Username</th>
                <th>Difficulty</th>
                <th>Total Quizzes Attempts</th>
                <th>Total Marks</th>
                <th>Total Score</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($quiz_results as $index => $result): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($result['username']); ?></td>
                    <td><?php echo htmlspecialchars($result['difficulty']); ?></td>
                    <td><?php echo $result['total_quizzes']; ?></td>
                    <td><?php echo $result['total_marks']; ?></td>
                    <td><?php echo $result['total_score']; ?></td>
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
