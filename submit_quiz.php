<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Initialize variables
$lesson_id = $_POST['lesson_id'];
$difficulty = $_POST['difficulty'];
$total_marks = 0;
$user_score = 0;
$user_id = $_SESSION['id'];

// Fetch exercises for the selected lesson and difficulty level
$stmt = $conn->prepare("SELECT * FROM exercises WHERE lesson_id = ? AND difficulty = ?");
$stmt->bind_param("is", $lesson_id, $difficulty);
$stmt->execute();
$result = $stmt->get_result();
$exercises = [];
while ($row = $result->fetch_assoc()) {
    $exercises[] = $row;
}
$stmt->close();

// Calculate user score
foreach ($exercises as $index => $exercise) {
    $total_marks += $exercise['marks'];
    $exercise_id = $exercise['exercise_id'];

    // Check if the user's answer matches the correct option
    if (isset($_POST["answer_$index"])) {
        if ($_POST["answer_$index"] == $exercise['correct_option']) {
            $user_score += $exercise['marks'];
        }
    }
}

// Insert the quiz result into the database
$stmt = $conn->prepare("INSERT INTO quiz_results (user_id, lesson_id, difficulty, total_marks, user_score) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisii", $user_id, $lesson_id, $difficulty, $total_marks, $user_score);
$stmt->execute();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Result</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <div class="jumbotron">
        <h1 class="display-4">Quiz Result</h1>
        <p class="lead">You have completed the quiz.</p>
        <hr class="my-4">
        <p>Your Score: <?php echo $user_score; ?> out of <?php echo $total_marks; ?></p>
        <a class="btn btn-primary btn-lg" href="home.php" role="button">Go to Home</a>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
