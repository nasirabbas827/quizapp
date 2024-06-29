<?php
session_start();
include('config.php');

// Check if the user is logged in as an admin
if (!isset($_SESSION["usertype"]) || $_SESSION["usertype"] !== "admin") {
    header("Location: admin_login.php");
    exit;
}

// Handle delete request
if (isset($_GET['delete'])) {
    $lesson_id = $_GET['delete'];
    // Get the file path to delete the file from the server
    $query = $conn->prepare("SELECT file_path FROM lessons WHERE lesson_id = ?");
    $query->bind_param("i", $lesson_id);
    $query->execute();
    $query->bind_result($file_path);
    $query->fetch();
    $query->close();

    // Delete the record from the database
    $stmt = $conn->prepare("DELETE FROM lessons WHERE lesson_id = ?");
    $stmt->bind_param("i", $lesson_id);
    if ($stmt->execute()) {
        // Delete the file from the server
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        echo "Lesson deleted successfully.";
    } else {
        echo "Error deleting lesson: " . $conn->error;
    }
    $stmt->close();
}

// Handle edit request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_lesson_id'])) {
    $lesson_id = $_POST['edit_lesson_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Check if a new file is uploaded
    if ($_FILES['file']['name']) {
        // Get the old file path
        $query = $conn->prepare("SELECT file_path FROM lessons WHERE lesson_id = ?");
        $query->bind_param("i", $lesson_id);
        $query->execute();
        $query->bind_result($old_file_path);
        $query->fetch();
        $query->close();

        // File upload
        $target_dir = "uploads/";
        $file_name = basename($_FILES["file"]["name"]);
        $target_file = $target_dir . time() . "_" . $file_name;
        $uploadOk = 1;
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size (50MB limit)
        if ($_FILES["file"]["size"] > 50000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($fileType != "pdf" && $fileType != "mp4" && $fileType != "avi") {
            echo "Sorry, only PDF, MP4, & AVI files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk && move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            // Delete the old file
            if (file_exists($old_file_path)) {
                unlink($old_file_path);
            }

            // Update the lesson with the new file path
            $stmt = $conn->prepare("UPDATE lessons SET title = ?, description = ?, file_path = ?, updated_at = NOW() WHERE lesson_id = ?");
            $stmt->bind_param("sssi", $title, $description, $target_file, $lesson_id);
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        // Update the lesson without changing the file
        $stmt = $conn->prepare("UPDATE lessons SET title = ?, description = ?, updated_at = NOW() WHERE lesson_id = ?");
        $stmt->bind_param("ssi", $title, $description, $lesson_id);
    }

    if ($stmt->execute()) {
        echo "Lesson updated successfully.";
    } else {
        echo "Error updating lesson: " . $conn->error;
    }
    $stmt->close();
}

// Fetch all lessons
$result = $conn->query("SELECT * FROM lessons ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - View Lessons</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<?php include('admin_navbar.php'); ?>

<div class="container mt-5">
    <h2>View Lessons</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>File</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['lesson_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><a href="<?php echo $row['file_path']; ?>" target="_blank">View File</a></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td><?php echo $row['updated_at']; ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?php echo $row['lesson_id']; ?>">Edit</button>
                        <a href="view_lessons.php?delete=<?php echo $row['lesson_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this lesson?');">Delete</a>
                    </td>
                </tr>
                <!-- Edit Modal -->
                <div class="modal fade" id="editModal<?php echo $row['lesson_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel">Edit Lesson</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="view_lessons.php" enctype="multipart/form-data">
                                    <input type="hidden" name="edit_lesson_id" value="<?php echo $row['lesson_id']; ?>">
                                    <div class="form-group">
                                        <label for="title">Lesson Title:</label>
                                        <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="description">Description:</label>
                                        <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="file">Upload New File:</label>
                                        <input type="file" class="form-control-file" id="file" name="file">
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
