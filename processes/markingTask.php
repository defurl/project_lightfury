<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_task_id = intval($_POST['user_task_id']);
    $username = $_SESSION['username'];

    // debugging
    error_log("user_task_id: " . $user_task_id);
    error_log("username: " . $username);

    // check user exists
    $user_check_sql = "SELECT id FROM users WHERE username = ?";
    $user_check_stmt = $conn->prepare($user_check_sql);
    $user_check_stmt->bind_param("s", $username);
    $user_check_stmt->execute();
    $user_check_stmt->store_result();

    if ($user_check_stmt->num_rows === 0) {
        $responseData = array(
            'code' => 500,
            'message' => 'User not found'
        );
        echo json_encode($responseData);
        http_response_code(500);
        exit();
    }

    // update status to "pending"
    $sql = "UPDATE user_tasks 
            SET status = 'pending'
            WHERE id = ?    
            AND user_id = (SELECT id FROM users WHERE username = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_task_id, $_SESSION['username']);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $responseData = array(
            'code' => 200,
            'message' => 'Task updated successfully'
        );
        http_response_code(200);
        header("Location: ../main.php");
    } else {
        $responseData = array(
            'code' => 500,
            'message' => 'Failed to update task'
        );
        http_response_code(500);
        header("Location: ../main.php");
    }
    $stmt->close();
    $conn->close();
    exit();
}
