<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Get the lesson ID and difficulty level from the URL
$lesson_id = $_GET['lesson_id'];
$difficulty = $_GET['difficulty'];

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

// Randomize the order of questions
shuffle($exercises);

// Handle question navigation
$current_question_index = isset($_GET['question']) ? (int)$_GET['question'] : 0;
$total_questions = count($exercises);

// Check if the current question index is valid
if ($current_question_index < 0) {
    $current_question_index = 0;
}
if ($current_question_index >= $total_questions) {
    $current_question_index = $total_questions - 1;
}

$current_exercise = $exercises[$current_question_index];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5">
    <h2>Quiz - <?php echo ucfirst($difficulty); ?> Level</h2>
    <form id="quizForm" action="submit_quiz.php" method="POST">
        <input type="hidden" name="lesson_id" value="<?php echo $lesson_id; ?>">
        <input type="hidden" name="difficulty" value="<?php echo $difficulty; ?>">
        
        <?php foreach ($exercises as $index => $exercise): ?>
        <div class="card mb-4 question-card" id="question_<?php echo $index; ?>" style="display: none;">
            <div class="card-body">
                <h5 class="card-title">Question <?php echo $index + 1; ?>: <?php echo htmlspecialchars($exercise['question_text']); ?></h5>
                <input type="hidden" name="exercise_id_<?php echo $index; ?>" value="<?php echo $exercise['exercise_id']; ?>">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answer_<?php echo $index; ?>" value="1" required>
                    <label class="form-check-label"><?php echo htmlspecialchars($exercise['option_one']); ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answer_<?php echo $index; ?>" value="2" required>
                    <label class="form-check-label"><?php echo htmlspecialchars($exercise['option_two']); ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answer_<?php echo $index; ?>" value="3" required>
                    <label class="form-check-label"><?php echo htmlspecialchars($exercise['option_three']); ?></label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answer_<?php echo $index; ?>" value="4" required>
                    <label class="form-check-label"><?php echo htmlspecialchars($exercise['option_four']); ?></label>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-primary" id="prevBtn" onclick="showPreviousQuestion()">Previous</button>
            <button type="button" class="btn btn-primary" id="nextBtn" onclick="showNextQuestion()">Next</button>
            <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">Submit Quiz</button>
        </div>
    </form>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    var currentQuestionIndex = 0;
    var totalQuestions = <?php echo $total_questions; ?>;

    function showQuestion(index) {
        // Hide all questions
        document.querySelectorAll('.question-card').forEach(function (card) {
            card.style.display = 'none';
        });

        // Show the current question
        document.getElementById('question_' + index).style.display = 'block';

        // Update button visibility
        document.getElementById('prevBtn').style.display = index === 0 ? 'none' : 'inline-block';
        document.getElementById('nextBtn').style.display = index === totalQuestions - 1 ? 'none' : 'inline-block';
        document.getElementById('submitBtn').style.display = index === totalQuestions - 1 ? 'inline-block' : 'none';
    }

    function showPreviousQuestion() {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            showQuestion(currentQuestionIndex);
        }
    }

    function showNextQuestion() {
        if (currentQuestionIndex < totalQuestions - 1) {
            currentQuestionIndex++;
            showQuestion(currentQuestionIndex);
        }
    }

    // Initialize the first question
    showQuestion(currentQuestionIndex);
</script>
</body>
</html>
