<?php
session_start();
include('conn.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['classroom_id'])) {
    $classroomId = $_POST['classroom_id'];
    $studentId = $_SESSION['user_id'];

    // Check if the student is already enrolled in the selected classroom
    $checkEnrollmentSql = "SELECT * FROM enrollments WHERE student_id = $studentId AND classroom = $classroomId";
    $result = $conn->query($checkEnrollmentSql);

    $response = ['enrolled' => false];

    if ($result->num_rows > 0) {
        // Student is already enrolled
        $response['enrolled'] = true;
    }

    echo json_encode($response);
    exit();
} else {
    // Invalid request
    echo json_encode(['enrolled' => false]);
    exit();
}
?>
