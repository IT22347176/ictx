<?php
session_start();
include('conn.php');

// Check if the user is logged in and has the admin role
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit();
}

// Get admin information from the Users table
$admin_id = $_SESSION["user_id"];
$admin_info_sql = "SELECT * FROM Users WHERE user_id = $admin_id";
$result = $conn->query($admin_info_sql);

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
} else {
    // Handle the case where admin info is not found
    // Redirect or show an error message
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/adminconsoles.css">
    <title>Admin Dashboard</title>
</head>
<body>
    <div id="main-content">
        <div class="left-part">
            <div class="admin-info">
                <h1><?php echo $admin["name"]; ?></h1>
                <p>Date: <?php echo date("Y-m-d"); ?></p>
                <p>Phone Number: <?php echo $admin["phone_number"]; ?></p>
            </div>

            <button class="logout-button" onclick="location.href='logout.php'">Logout</button>
        </div>


        <div class="right-part">
            <div class="dashboard-buttons">
                <a href="manageclassroom.php" class="dashboard-button manage-classrooms">
                    <div class="button-icon">📚</div>
                    <p>Manage Classrooms</p>
                </a>

                <a href="managestudents.php" class="dashboard-button manage-students">
                    <div class="button-icon">👩‍🎓</div>
                    <p>Manage Students</p>
                </a>

                <a href="managepayments.php" class="dashboard-button manage-payments">
                    <div class="button-icon">💸</div>
                    <p>Manage Payments</p>
                </a>

                <a href="managestaff.php" class="dashboard-button manage-staff">
                    <div class="button-icon">👥</div>
                    <p>Manage Staff</p>
                </a>

                <a href="managenotices.php" class="dashboard-button manage-notices">
                    <div class="button-icon">📢</div>
                    <p>Manage Notices</p>
                </a>

                <a href="manageother.php" class="dashboard-button manage-other">
                    <div class="button-icon">X</div>
                    <p>Other</p>
                </a>

            </div>
        </div>
    </div>
</body>
</html>
