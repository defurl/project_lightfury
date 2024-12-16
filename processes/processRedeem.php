<?php
session_start();
require_once 'conn.php';

if (!isset($_SESSION['loggedin']) || !isset($_POST['reward_id'])) {
    header("Location: rewards.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$reward_id = intval($_POST['reward_id']);

// Start transaction
$conn->begin_transaction();

try {
    // Get reward cost and user points
    $sql = "SELECT r.cost, r.name, u.points 
            FROM rewards r, users u 
            WHERE r.id = ? AND u.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $reward_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row || $row['points'] < $row['cost']) {
        throw new Exception("Insufficient points");
    }

    // Deduct points
    $sql = "UPDATE users SET points = points - ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $row['cost'], $user_id);
    $stmt->execute();

    // Record user redeeming reward
    $sql = "INSERT INTO user_rewards (user_id, reward_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $reward_id);
    $stmt->execute();

    $conn->commit();
    $_SESSION['message'] = "Successfully redeemed " . $row['name'];
    header("Location: ../rewards.php");

} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = $e->getMessage();
    header("Location: ../rewards.php");
}
exit();