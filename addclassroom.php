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

// Fetch available classrooms from the database
$sql = "SELECT * FROM Classrooms";
$result = $conn->query($sql);

// Check if there are any classrooms
if ($result->num_rows > 0) {
    $availableClassrooms = [];
    
    while ($row = $result->fetch_assoc()) {
        // Add each classroom to the list
        $availableClassrooms[] = [
            'id' => $row['class_id'],
            'name' => $row['class_name'],
            'key' => $row['class_key'],
        ];
    }
} else {
    // No classrooms found
    $availableClassrooms = [];
}



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/addclassrooms.css">
    <title>Add Classroom</title>
</head>
<body>
    <div id="main-content">
        <div class="left-part">
            <h1>Add Classroom</h1>
            <!-- Back Button -->
            <button class="back-button" onclick="location.href='studentdashboard.php'">Back</button>
        </div>

        <div class="right-part">
            <!-- Display Classrooms as Square Boxes -->
            <div class="classroom-boxes">
                <?php foreach ($availableClassrooms as $classroom): ?>
                    <a href="#" class="classroom-box" onclick="promptForClassroomKey(<?php echo $classroom['id']; ?>)">
                        <div class="box-icon">📚</div>
                        <p><?php echo $classroom['name']; ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

<!-- Popup for Classroom Key Entry -->
<div class="popup" id="keyPopup">
    <div class="popup-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <input type="hidden" id="classroomId">
        <label for="classroomKey">Enter Classroom Key:</label>
        <input type="text" id="classroomKey" placeholder="Classroom Key">
        <button onclick="enrollInClassroom()">Enroll</button>
    </div>
</div>


<script>
    function promptForClassroomKey(classroomId) {
        var popup = document.getElementById('keyPopup');
        popup.style.display = 'block';

        // Set the classroom ID in a hidden input field
        var classroomIdInput = document.getElementById('classroomId');
        classroomIdInput.value = classroomId;
    }

    function closePopup() {
        document.getElementById('keyPopup').style.display = 'none';
    }

    function enrollInClassroom() {
        var keyInput = document.getElementById('classroomKey');
        var key = keyInput.value;

        if (key.trim() !== '') {
            // Get the classroom ID from the hidden input
            var classroomIdInput = document.getElementById('classroomId');
            var classroomId = classroomIdInput.value;

            // Check if the user is already enrolled in the classroom using AJAX
            var checkEnrollmentUrl = 'checkenrollment.php';
            var formData = new FormData();
            formData.append('classroom_id', classroomId);

            fetch(checkEnrollmentUrl, {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.enrolled) {
                    alert('You are already enrolled in this classroom.');
                } else {
                    // Enroll the user if not already enrolled
                    alert('Successfully enrolled in the classroom!');
                    // Close the popup
                    document.getElementById('keyPopup').style.display = 'none';
                    // Redirect to studentdashboard.php
                    window.location.href = 'studentdashboard.php?enroll=true&classroom_id=' + classroomId + '&key=' + key;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        } else {
            alert('Please enter the Classroom Key.');
        }
    }
</script>

    </div>
</body>
</html>
