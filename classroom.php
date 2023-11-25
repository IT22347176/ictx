<?php
// Include necessary configurations or database connections if required

// Check if the user is logged in and has the student role
session_start();
include('conn.php');
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "student") {
    header("Location: login.php");
    exit();
}

// Fetch months for the classroom from the database
$classroomId = $_GET['classroom_id']; // Assuming you get the classroom_id from the URL
$sql = "SELECT * FROM months WHERE class_id = $classroomId";
$result = $conn->query($sql);

// Check if there are any months
if ($result->num_rows > 0) {
    $classroomMonths = [];
    
    while ($row = $result->fetch_assoc()) {
        // Add each month to the list
        $classroomMonths[] = [
            'id' => $row['month_id'],
            'number' => $row['month_number'],
            'name' => date("F", mktime(0, 0, 0, $row['month_number'], 1)),
            'content' => $row['content'],
            'created_at' => $row['created_at'],
            'visible' => $row['visible'],
            'weeks_created' => $row['weeks_created'],
        ];
    }
} else {
    // No months found
    $classroomMonths = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/classrooms.css">
    <title>Classroom</title>
</head>
<body>
    <div id="main-content">
        <div class="left-part">
            <h1>Classroom</h1>
            <p>Welcome, <?php echo $_SESSION['name']; ?>!</p>
    
            <button type="button" class="back-button" onclick="window.location.href='studentdashboard.php'">Back</button>
        </div>

        <!-- Menu button for mobile view -->
        <div class="menu-button">&#9776;</div>

        <!-- Menu for mobile view -->
        <div class="menu">
            <a href="#">Home</a>
            <a href="#">Profile</a>
            <!-- Add more menu items as needed -->
        </div>

        <div class="right-part">
            <!-- Display Months as Boxes -->
            <div class="month-boxes">
                <?php foreach ($classroomMonths as $month): ?>
                    <!-- Make month boxes clickable -->
                    <a href="week.php?month_id=<?php echo $month['id']; ?>" class="month-box">
                        <h2><?php echo $month['name']; ?></h2>
                        <p><?php echo $month['content']; ?></p>
                        <!-- Add more details or actions if needed -->
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        // JavaScript to toggle the menu visibility
        document.querySelector('.menu-button').addEventListener('click', function() {
            document.querySelector('.menu').style.display = 'flex';
        });

        document.querySelector('.menu').addEventListener('click', function() {
            document.querySelector('.menu').style.display = 'none';
        });
    </script>
</body>
</html>
