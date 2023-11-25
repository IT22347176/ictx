<?php
// Include necessary configurations or database connections if required
session_start();
include('conn.php');

// Get the month_id from the URL
$monthId = isset($_GET['month_id']) ? $_GET['month_id'] : null;

// Fetch weeks for the selected month from the database
$sql = "SELECT * FROM weeks WHERE month_id = $monthId";
$result = $conn->query($sql);

// Check if there are any weeks
if ($result->num_rows > 0) {
    $weeks = [];

    while ($row = $result->fetch_assoc()) {
        // Add each week to the list
        $weeks[] = [
            'id' => $row['week_id'],
            'number' => $row['week_number'],
            'content' => $row['content'],
            'created_at' => $row['created_at'],
            'visible' => $row['visible'],
        ];
    }
} else {
    // No weeks found
    $weeks = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/weeks.css">
    <title>Weeks</title>
</head>
<body>
    <div id="main-content">
        <div class="left-part">
            <h1>Weeks</h1>
            <p>Welcome, <?php echo $_SESSION['name']; ?>!</p>
    
            <button type="button" class="back-button" onclick="window.location.href='classroom.php?classroom_id=<?php echo $classroomId; ?>'">Back to Classroom</button>
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
            <!-- Display Weeks as Boxes -->
            <div class="week-boxes">
                <?php foreach ($weeks as $week): ?>
                    <div class="week-box">
                        <h2>Week <?php echo $week['number']; ?></h2>
                        <p><?php echo $week['content']; ?></p>
                        <!-- Add more details or actions if needed -->
                    </div>
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
