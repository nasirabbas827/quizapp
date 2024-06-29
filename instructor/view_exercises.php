<?php
session_start();
include('config.php');

// Check if the user is logged in as an instructor
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: instructor_login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $exercise_id = $_GET['delete'];

    // Delete the record from the database
    $stmt = $conn->prepare("DELETE FROM exercises WHERE exercise_id = ?");
    $stmt->bind_param("i", $exercise_id);
    if ($stmt->execute()) {
        echo "Exercise deleted successfully.";
    } else {
        echo "Error deleting exercise: " . $conn->error;
    }
    $stmt->close();
}

// Handle edit request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_exercise_id'])) {
    $exercise_id = $_POST['edit_exercise_id'];
    $question_text = $_POST['question_text'];
    $option_one = $_POST['option_one'];
    $option_two = $_POST['option_two'];
    $option_three = $_POST['option_three'];
    $option_four = $_POST['option_four'];
    $correct_option = $_POST['correct_option'];
    $difficulty = $_POST['difficulty'];
    $marks = $_POST['marks'];

    // Update the exercise in the database
    $stmt = $conn->prepare("UPDATE exercises SET question_text = ?, option_one = ?, option_two = ?, option_three = ?, option_four = ?, correct_option = ?, difficulty = ?, marks = ?, updated_at = NOW() WHERE exercise_id = ?");
    $stmt->bind_param("ssssssssi", $question_text, $option_one, $option_two, $option_three, $option_four, $correct_option, $difficulty, $marks, $exercise_id);

    if ($stmt->execute()) {
        echo "Exercise updated successfully.";
    } else {
        echo "Error updating exercise: " . $conn->error;
    }
    $stmt->close();
}

// Fetch all exercises
$result = $conn->query("SELECT e.*, l.title AS lesson_title FROM exercises e JOIN lessons l ON e.lesson_id = l.lesson_id ORDER BY e.created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - View Exercises</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('instructor_navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2>View Exercises</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Lesson</th>
                <th>Question</th>
                <th>Option 1</th>
                <th>Option 2</th>
                <th>Option 3</th>
                <th>Option 4</th>
                <th>Correct Option</th>
                <th>Difficulty</th>
                <th>Marks</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['exercise_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['lesson_title']); ?></td>
                    <td><?php echo htmlspecialchars($row['question_text']); ?></td>
                    <td><?php echo htmlspecialchars($row['option_one']); ?></td>
                    <td><?php echo htmlspecialchars($row['option_two']); ?></td>
                    <td><?php echo htmlspecialchars($row['option_three']); ?></td>
                    <td><?php echo htmlspecialchars($row['option_four']); ?></td>
                    <td><?php echo $row['correct_option']; ?></td>
                    <td><?php echo htmlspecialchars($row['difficulty']); ?></td>
                    <td><?php echo htmlspecialchars($row['marks']); ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td><?php echo $row['updated_at']; ?></td>
                    <td>
                        <button class="mb-2 btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?php echo $row['exercise_id']; ?>">Edit</button>
                        <a href="view_exercises.php?delete=<?php echo $row['exercise_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this exercise?');">Delete</a>
                    </td>
                </tr>
                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $row['exercise_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Exercise</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="view_exercises.php">
                                    <input type="hidden" name="edit_exercise_id" value="<?php echo $row['exercise_id']; ?>">
                                    <div class="form-group">
                                        <label for="question_text">Question Text:</label>
                                        <textarea class="form-control" id="question_text" name="question_text" rows="3" required><?php echo htmlspecialchars($row['question_text']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="option_one">Option One:</label>
                                        <input type="text" class="form-control" id="option_one" name="option_one" value="<?php echo htmlspecialchars($row['option_one']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="option_two">Option Two:</label>
                                        <input type="text" class="form-control" id="option_two" name="option_two" value="<?php echo htmlspecialchars($row['option_two']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="option_three">Option Three:</label>
                                        <input type="text" class="form-control" id="option_three" name="option_three" value="<?php echo htmlspecialchars($row['option_three']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="option_four">Option Four:</label>
                                        <input type="text" class="form-control" id="option_four" name="option_four" value="<?php echo htmlspecialchars($row['option_four']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="correct_option">Correct Option (1-4):</label>
                                        <input type="number" class="form-control" id="correct_option" name="correct_option" min="1" max="4" value="<?php echo $row['correct_option']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="difficulty">Difficulty Level:</label>
                                        <select class="form-control" id="difficulty" name="difficulty" required>
                                            <option value="low" <?php echo ($row['difficulty'] == 'low') ? 'selected' : ''; ?>>Low</option>
                                            <option value="medium" <?php echo ($row['difficulty'] == 'medium') ? 'selected' : ''; ?>>Medium</option>
                                            <option value="high" <?php echo ($row['difficulty'] == 'high') ? 'selected' : ''; ?>>High</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="marks">Marks:</label>
                                        <input type="number" class="form-control" id="marks" name="marks" value="<?php echo $row['marks']; ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
