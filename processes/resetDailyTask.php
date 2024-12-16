<?php
require_once './processes/conn.php';

// Reset daily tasks
$sql = "UPDATE user_tasks SET status = 'unfinished' WHERE task_type = 'daily'";
if ($conn->query($sql) === TRUE) {
    echo "Daily tasks reset successfully.";
} else {
    echo "Error resetting daily tasks: " . $conn->error;
}

$conn->close();
?>