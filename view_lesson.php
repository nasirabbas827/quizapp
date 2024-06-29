<?php
include('config.php');

session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION["id"]) || empty($_SESSION["id"])) {
    header("location: index.php");
    exit;
}

// Check if lesson_id is set in the URL
if (!isset($_GET['lesson_id'])) {
    header("location: homepage.php");
    exit;
}

$lesson_id = $_GET['lesson_id'];

// Fetch lesson details
$stmt = $conn->prepare("SELECT * FROM lessons WHERE lesson_id = ?");
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();
$lesson = $result->fetch_assoc();

if (!$lesson) {
    echo "Lesson not found.";
    exit;
}

$file_path = $lesson['file_path'];
$file_ext = pathinfo($file_path, PATHINFO_EXTENSION);

?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($lesson['title']); ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-5">
    <h2><?php echo htmlspecialchars($lesson['title']); ?></h2>
    <p><?php echo htmlspecialchars($lesson['description']); ?></p>

    <?php if ($file_ext == 'pdf'): ?>
        <a href="./admin/<?php echo $file_path; ?>" class="btn btn-primary" download>Download PDF</a>
    <?php else: ?>
        <video width="100%" controls>
            <source src="./admin/<?php echo $file_path; ?>" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    <?php endif; ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
