<?php
include('conn.php');

// PHP code for processing login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST["phone"];
    $password = $_POST["password"];

    // Validate user input and authenticate user (ensure to use hashed passwords in production)
    // Example validation and authentication (replace with your actual validation and authentication logic)
    $sql = "SELECT * FROM Users WHERE phone_number = '$phone' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        session_start();

        // Store user details in the session for later use
        $_SESSION["user_id"] = $row["user_id"];
        $_SESSION["role"] = $row["role"];
        $_SESSION["name"] = $row["name"];
        $_SESSION["phone_number"] = $row["phone_number"];

        // Redirect based on the user's role
        if ($row["role"] === "admin") {
            header("Location: adminconsole.php");
            exit();
        } elseif ($row["role"] === "sadmin") {
            header("Location: sadminpage.php");
            exit();
        } elseif ($row["role"] === "student") {
            header("Location: studentdashboard.php");
            exit();
        }
    } else {
        // Invalid credentials, you might want to show an error message
        echo "Invalid phone number or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="Styles/logins.css">
    <title>LMS Login</title>
</head>
<body>
    <div class="login-container">
        <div class="login-image"></div>
        <div class="login-form">
            <h2>Login</h2>
            <form action="login.php" method="post">
                <label for="phone">Phone Number:</label>
                <input type="tel" id="phone" name="phone" placeholder="e.g., 0712345678" required pattern="07[0-8]\d{7}" title="Please enter a valid phone number">
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
