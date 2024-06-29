<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the lesson ID from the URL
$lesson_id = $_GET['lesson_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Take Quiz</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Select Difficulty Level</h2>
    <div class="row">
        <div class="col-md-4 mb-4">
            <a href="quiz.php?lesson_id=<?php echo $lesson_id; ?>&difficulty=low" class="btn btn-primary btn-block">Easy</a>
        </div>
        <div class="col-md-4 mb-4">
            <a href="quiz.php?lesson_id=<?php echo $lesson_id; ?>&difficulty=medium" class="btn btn-warning btn-block">Medium</a>
        </div>
        <div class="col-md-4 mb-4">
            <a href="quiz.php?lesson_id=<?php echo $lesson_id; ?>&difficulty=high" class="btn btn-danger btn-block">Hard</a>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
