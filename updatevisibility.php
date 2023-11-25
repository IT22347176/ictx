<?php
include('conn.php');

// Get the current month
$currentMonth = date('n');

// Update visibility for each classroom and month
$sql = "UPDATE Months
        SET visible = CASE
            WHEN month_number <= $currentMonth THEN 1
            ELSE 0
        END";
$conn->query($sql);
?>
