<?php
session_start();
include('config.php');

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Fetch all lessons
$result_lessons = $conn->query("SELECT * FROM lessons ORDER BY created_at DESC");

// Fetch all feedbacks with ratings and usernames
$stmt_feedbacks = $conn->prepare("SELECT f.*, u.username FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC");
$stmt_feedbacks->execute();
$result_feedbacks = $stmt_feedbacks->get_result();
$feedbacks = [];
while ($row = $result_feedbacks->fetch_assoc()) {
    $feedbacks[] = $row;
}
$stmt_feedbacks->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Home Page</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h1>Read the Lesson and Attempt the Quizzes</h1>

   
    <!-- Display Lessons -->
    <div class="row">
        <?php while ($row = $result_lessons->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                        <a href="view_lesson.php?lesson_id=<?php echo $row['lesson_id']; ?>" class="btn btn-primary">View Lesson File</a>
                        <a href="take_quiz.php?lesson_id=<?php echo $row['lesson_id']; ?>" class="btn btn-secondary">Take Quiz</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <hr>
    <h2>Feedbacks</h2>

     <!-- Display Feedbacks -->
     <div class="row mt-4">
        <?php foreach ($feedbacks as $feedback): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($feedback['username']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($feedback['feedback_text']); ?></p>
                        <div class="text-warning">
                            <?php
                            $rating = $feedback['rating'];
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<i class="fas fa-star"></i>';
                                } else {
                                    echo '<i class="far fa-star"></i>';
                                }
                            }
                            ?>
                        </div>
                        <small class="text-muted">Date: <?php echo date("Y-m-d", strtotime($feedback['created_at'])); ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <hr>

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
