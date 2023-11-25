<?php
session_start();
include('conn.php');

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Check if the user has the admin role
if ($_SESSION["role"] !== "admin") {
    // Redirect to a different page or display an error message
    exit();
}

// Handle form submission for creating a classroom
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["create_classroom"])) {
    $class_name = $_POST["class_name"];
    $class_key = $_POST["class_key"];

    // Check if the key already exists
    $checkKeySql = "SELECT * FROM classrooms WHERE class_key = '$class_key'";
    $checkResult = $conn->query($checkKeySql);

    if ($checkResult->num_rows > 0) {
        // Key already exists, handle accordingly (display error message or redirect)
        exit("Classroom with the same key already exists.");
    }

    // Insert the classroom
    $sqlCreateClassroom = "INSERT INTO classrooms (admin_id, class_name, class_key) VALUES ({$_SESSION['user_id']}, '$class_name', '$class_key')";
    $conn->query($sqlCreateClassroom);

    // Get the ID of the newly created classroom
    $classroom_id = $conn->insert_id;

    // Create 12 months for the classroom
    for ($month_number = 1; $month_number <= 12; $month_number++) {
        $sqlCreateMonth = "INSERT INTO months (class_id, month_number) VALUES ($classroom_id, $month_number)";
        $conn->query($sqlCreateMonth);
    }

    // Redirect to the admin console after creating the classroom
    header("Location: manageclassroom.php");
    exit();
}

// Handle classroom deletion
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] === "delete" && isset($_GET["classroom_id"])) {
    $classroom_id = $_GET["classroom_id"];

    // Display a confirmation dialog before deleting
    echo "<script>
            let confirmDelete = confirm('Are you sure you want to delete this classroom?');
            if(confirmDelete) {
                window.location.href = 'manageclassroom.php?action=confirmed_delete&classroom_id=$classroom_id';
            } else {
                window.location.href = 'manageclassroom.php';
            }
          </script>";
}

// Handle confirmed classroom deletion
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["action"]) && $_GET["action"] === "confirmed_delete" && isset($_GET["classroom_id"])) {
    $classroom_id = $_GET["classroom_id"];

    // You need to modify the SQL queries based on your database schema
    $sqlDeleteClassroom = "DELETE FROM classrooms WHERE class_id = $classroom_id";
    $sqlDeleteMonths = "DELETE FROM months WHERE class_id = $classroom_id";

    $conn->query($sqlDeleteClassroom);
    $conn->query($sqlDeleteMonths);

    // Redirect to the admin console after deleting the classroom
    header("Location: manageclassroom.php");
    exit();
}

// Handle classroom editing
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_classroom"])) {
    $classroom_id = $_POST["classroom_id"];
    $class_name = $_POST["class_name"];
    $class_key = $_POST["class_key"];

    // You need to modify the SQL query based on your database schema
    $sqlEditClassroom = "UPDATE classrooms SET class_name = '$class_name', class_key = '$class_key' WHERE class_id = $classroom_id";
    $conn->query($sqlEditClassroom);

    // Redirect to the admin console after editing the classroom
    header("Location: manageclassroom.php");
    exit();
}

// Fetch all classrooms for display
$sqlFetchClassrooms = "SELECT * FROM classrooms";
$result = $conn->query($sqlFetchClassrooms);

// Check if there are any classrooms
if ($result !== false && $result->num_rows > 0) {
    $classrooms = [];

    while ($row = $result->fetch_assoc()) {
        $classrooms[] = [
            'id' => $row['class_id'],
            'name' => $row['class_name'],
            'key' => $row['class_key'],
        ];
    }
} else {
    // No classrooms found
    $classrooms = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/manageclassrooms.css">
    <title>Admin Console</title>
</head>
<body>
    <div id="main-content">
        <div class="left-part">
            <h1>Admin Console</h1>
            <p>Welcome, <?php echo $_SESSION['name']; ?>!</p>
            <!-- Add other admin information if needed -->
            <!-- Back Button -->
            <form action="adminconsole.php" method="post">
                <button type="submit" class="back-button">Back</button>
            </form>
        </div>

        <div class="right-part">
            <h2>Create Classroom</h2>
            <form action="manageclassroom.php" method="post">
                <label for="class_name">Classroom Name:</label>
                <input type="text" id="class_name" name="class_name" required>

                <label for="class_key">Enrollment Key:</label>
                <input type="text" id="class_key" name="class_key" required>
                <br>
                <button type="submit" class="create-classroom-button" name="create_classroom">Create Classroom</button>
            </form>

            <h2>Classrooms</h2>
            <ul>
                <?php foreach ($classrooms as $classroom): ?>
                    <li>
                        <span><?php echo $classroom['name']; ?> (Key: <?php echo $classroom['key']; ?>)</span>
                        <div>
                            <a href="manageclassroom.php?action=delete&classroom_id=<?php echo $classroom['id']; ?>" class="delete-classroom-button">Delete</a>
                            <a href="#" class="edit-classroom-button" onclick="openEditForm(<?php echo $classroom['id']; ?>, '<?php echo $classroom['name']; ?>', '<?php echo $classroom['key']; ?>')">Edit</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Hidden form for editing classrooms -->
    <div class="popup" id="editForm">
        <div class="popup-content">
            <span class="close" onclick="closeEditForm()">&times;</span>
            <h2>Edit Classroom</h2>
            <form action="manageclassroom.php" method="post">
                <input type="hidden" id="edit_classroom_id" name="classroom_id">
                <label for="edit_class_name">Classroom Name:</label>
                <input type="text" id="edit_class_name" name="class_name" required>

                <label for="edit_class_key">Enrollment Key:</label>
                <input type="text" id="edit_class_key" name="class_key" required>

                <button type="submit" class="edit-classroom-submit" name="edit_classroom">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        function openEditForm(classroom_id, class_name, class_key) {
            document.getElementById("edit_classroom_id").value = classroom_id;
            document.getElementById("edit_class_name").value = class_name;
            document.getElementById("edit_class_key").value = class_key;
            document.getElementById("editForm").style.display = "block";
        }

        function closeEditForm() {
            document.getElementById("editForm").style.display = "none";
        }
    </script>
</body>
</html>
