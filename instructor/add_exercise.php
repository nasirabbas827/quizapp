<?php
session_start();
include('config.php');

// Check if the user is logged in as an instructor
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: instructor_login.php");
    exit;
}

// Fetch all lessons for the dropdown
$lessons_result = $conn->query("SELECT lesson_id, title FROM lessons ORDER BY title");

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $lesson_id = $_POST['lesson_id'];
    $question_text = $_POST['question_text'];
    $option_one = $_POST['option_one'];
    $option_two = $_POST['option_two'];
    $option_three = $_POST['option_three'];
    $option_four = $_POST['option_four'];
    $correct_option = $_POST['correct_option'];
    $difficulty = $_POST['difficulty'];
    $marks = $_POST['marks'];

    // Prepare an insert statement
    $sql = "INSERT INTO exercises (lesson_id, question_text, option_one, option_two, option_three, option_four, correct_option, difficulty, marks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("isssssisi", $lesson_id, $question_text, $option_one, $option_two, $option_three, $option_four, $correct_option, $difficulty, $marks);

        if ($stmt->execute()) {
            echo "The exercise has been added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Add Exercise</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('instructor_navbar.php'); ?>

<div class="container mt-5 mb-4">
    <h2>Add New Exercise</h2>
    <form action="add_exercise.php" method="POST">
        <div class="form-group">
            <label for="lesson_id">Select Lesson:</label>
            <select class="form-control" id="lesson_id" name="lesson_id" required>
                <?php while ($lesson = $lessons_result->fetch_assoc()): ?>
                    <option value="<?php echo $lesson['lesson_id']; ?>"><?php echo htmlspecialchars($lesson['title']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="question_text">Question Text:</label>
            <textarea class="form-control" id="question_text" name="question_text" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="option_one">Option One:</label>
            <input type="text" class="form-control" id="option_one" name="option_one" required>
        </div>
        <div class="form-group">
            <label for="option_two">Option Two:</label>
            <input type="text" class="form-control" id="option_two" name="option_two" required>
        </div>
        <div class="form-group">
            <label for="option_three">Option Three:</label>
            <input type="text" class="form-control" id="option_three" name="option_three" >
        </div>
        <div class="form-group">
            <label for="option_four">Option Four:</label>
            <input type="text" class="form-control" id="option_four" name="option_four" >
        </div>
        <div class="form-group">
            <label for="correct_option">Correct Option (1-4):</label>
            <input type="number" class="form-control" id="correct_option" name="correct_option" min="1" max="4" required>
        </div>
        <div class="form-group">
            <label for="difficulty">Difficulty Level:</label>
            <select class="form-control" id="difficulty" name="difficulty" required>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
        </div>
        <div class="form-group">
            <label for="marks">Marks:</label>
            <input type="number" class="form-control" id="marks" name="marks" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Exercise</button>
        <a class="btn btn-outline-success" href="view_exercises.php">View Exercises</a>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
