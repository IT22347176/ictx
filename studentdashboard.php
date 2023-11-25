<?php
// Include necessary configurations or database connections if required

// Check if the user is logged in and has the student role
// If not, redirect to the login page
session_start();
include('conn.php');
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit();
}

// Check if the enrollment request is made
if (isset($_GET['enroll']) && $_GET['enroll'] === 'true') {
    // Ensure the required parameters are provided
    if (isset($_GET['classroom_id']) && isset($_GET['key'])) {
        $classroomId = $_GET['classroom_id'];
        $key = $_GET['key'];

        // Validate the key against the database
        $validateKeySql = "SELECT class_key FROM Classrooms WHERE class_id = $classroomId";
        $result = $conn->query($validateKeySql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $correctKey = $row['class_key'];

            if ($key === $correctKey) {
                // Enroll the student in the classroom
                $studentId = $_SESSION['user_id'];
                $enrollSql = "INSERT INTO enrollments (student_id, classroom) VALUES ($studentId, $classroomId)";
                $conn->query($enrollSql);

                // Redirect back to studentdashboard.php after enrollment
                header("Location: studentdashboard.php");
                exit();
            } else {
                echo "Incorrect Classroom Key.";
            }
        } else {
            echo "Classroom not found.";
        }
    } else {
        echo "Invalid parameters for enrollment.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_classroom'])) {
    $classroomIdToDelete = $_POST['delete_classroom'];

    // Perform the deletion of the enrollment based on $classroomIdToDelete
    $studentId = $_SESSION['user_id'];
    $deleteEnrollmentSql = "DELETE FROM enrollments WHERE student_id = $studentId AND classroom = $classroomIdToDelete";
    $conn->query($deleteEnrollmentSql);

    // Redirect back to studentdashboard.php after deletion
    header("Location: studentdashboard.php");
    exit();
}

// Fetch enrolled classrooms from the database
$studentId = $_SESSION['user_id'];
$sql = "SELECT c.* FROM enrollments e
        JOIN Classrooms c ON e.classroom = c.class_id
        WHERE e.student_id = $studentId";
$result = $conn->query($sql);

// Check if there are any enrolled classrooms
if ($result->num_rows > 0) {
    $enrolledClassrooms = [];

    while ($row = $result->fetch_assoc()) {
        // Add each enrolled classroom to the list
        $enrolledClassrooms[] = [
            'id' => $row['class_id'],
            'name' => $row['class_name'],
        ];
    }
} else {
    // No enrolled classrooms found
    $enrolledClassrooms = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/studentdashboards.css">
    <title>Student Dashboard</title>

    <script>
        function confirmUnenroll(classroomId) {
            // Show the delete classroom modal
            document.getElementById('deleteClassroomModal').style.display = 'block';

            // Store the classroom ID for later use
            document.getElementById('classroomToDelete').value = classroomId;
        }

        function hideDeleteModal() {
            // Hide the delete classroom modal
            document.getElementById('deleteClassroomModal').style.display = 'none';
        }

        function submitUnenrollForm() {
            // Retrieve the stored classroom ID
            const classroomIdToDelete = document.getElementById('classroomToDelete').value;

            // Make sure the classroom ID is available
            if (!classroomIdToDelete) {
                console.error('Classroom ID not found.');
                return;
            }

            // Submit the form for enrollment deletion
            document.getElementById('unenrollForm').submit();
        }
    </script>
</head>

<body>
    <div id="main-content">
        <div class="left-part">
            <h1>Student Dashboard</h1>
            <p>Welcome, <?php echo $_SESSION['name']; ?>!</p>
            <p>Phone Number: <?php echo $_SESSION['phone_number']; ?></p>
            <form action="logout.php" method="post">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>

        <div class="right-part">
            <div class="classroom-boxes">
                <?php foreach ($enrolledClassrooms as $classroom): ?>
                    <div class="classroom-box">
                        <a href="classroom.php?classroom_id=<?php echo $classroom['id']; ?>" class="box-link">
                            <div class="box-icon">📚</div>
                            <p><?php echo $classroom['name']; ?></p>
                        </a>
                        <button type="button" class="unenroll-button" onclick="confirmUnenroll(<?php echo $classroom['id']; ?>)">❌</button>
                    </div>
                <?php endforeach; ?>

                <a href="addclassroom.php" class="classroom-box add-class-button">
                    <div class="box-icon">➕</div>
                    <p>Add a Class</p>
                </a>
            </div>
        </div>

        <div id="deleteClassroomModal" class="modal" style="display: none;">
            <div class="modal-content">
                <p>Are you sure you want to unenroll from this classroom?</p>
                <button type="button" onclick="submitUnenrollForm()">Yes</button>
                <button type="button" onclick="hideDeleteModal()">No</button>
            </div>
        </div>
    </div>

    <!-- Hidden form for enrollment deletion -->
    <form id="unenrollForm" method="post" style="display: none;">
        <input type="hidden" id="classroomToDelete" name="delete_classroom" value="">
    </form>
</body>
</html>
