<?php
session_start();
require_once 'conn.php';

header('Content-Type: application/json');

$response = [
    'status' => 500,
    'message' => 'An error occurred.'
];

if (!isset($_SESSION['admin_id'])) {
    $response['message'] = 'Admin ID is not set in the session.';
    echo json_encode($response);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $user_task_id = intval($_POST['user_task_id']);
    $action = $_POST['action'];
    $admin_id = $_SESSION['admin_id'];

    if ($action === 'approve') {
        // Check if the task belongs to a user assigned to the admin
        $check_sql = "SELECT ut.id 
                      FROM user_tasks ut
                      JOIN users u ON ut.user_id = u.id
                      WHERE ut.id = ? AND u.admin_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ii", $user_task_id, $admin_id);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            // Approve task
            $sql = "UPDATE user_tasks 
                    SET status = 'confirmed' 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_task_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                // Reward points
                $reward_sql = "INSERT INTO Points (user_id, points_added) 
                               SELECT ut.user_id, t.point_value
                               FROM user_tasks ut
                               JOIN tasks t ON ut.task_id = t.id
                               WHERE ut.id = ?";
                $reward_stmt = $conn->prepare($reward_sql);
                $reward_stmt->bind_param("i", $user_task_id);
                $reward_stmt->execute();

                if ($reward_stmt->affected_rows > 0) {
                    // Update total points in Users table
                    $update_points_sql = "UPDATE users 
                                          SET points = points + (SELECT t.point_value 
                                                                 FROM user_tasks ut
                                                                 JOIN tasks t ON ut.task_id = t.id
                                                                 WHERE ut.id = ?)
                                          WHERE id = (SELECT user_id FROM user_tasks WHERE id = ?)";
                    $update_points_stmt = $conn->prepare($update_points_sql);
                    $update_points_stmt->bind_param("ii", $user_task_id, $user_task_id);
                    $update_points_stmt->execute();

                    if ($update_points_stmt->affected_rows > 0) {
                        $response['status'] = 200;
                        $response['message'] = 'Task approved and points rewarded successfully.';
                    } else {
                        $response['message'] = 'Failed to update user points.';
                    }
                } else {
                    $response['message'] = 'Failed to reward points.';
                }
            } else {
                $response['message'] = 'Failed to approve task.';
            }
        } else {
            $response['message'] = 'Task does not belong to a user assigned to the admin.';
        }
    }
}

echo json_encode($response);
header("Location: ../admin.php");
exit();