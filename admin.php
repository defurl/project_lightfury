<?php
session_start();
require_once './processes/conn.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

// Check if the user is an admin
$sql = "SELECT id, role, username FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$stmt->bind_result($admin_id, $role, $admin_name);
$stmt->fetch();
$stmt->close();

if ($role !== 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch users moderated by the admin
$moderated_user_sql = "SELECT username FROM users WHERE admin_id = ?";
$moderated_user_stmt = $conn->prepare($moderated_user_sql);
$moderated_user_stmt->bind_param("i", $admin_id);
$moderated_user_stmt->execute();
$moderated_user_result = $moderated_user_stmt->get_result();
$moderated_user = [];
while ($row = $moderated_user_result->fetch_assoc()) {
    $moderated_user = $row['username'];
}
$moderated_user_stmt->close();

// Fetch pending tasks
$sql = "SELECT ut.id AS user_task_id, u.username, t.description, t.point_value 
        FROM user_tasks ut
        JOIN tasks t ON ut.task_id = t.id
        JOIN users u ON ut.user_id = u.id
        WHERE ut.status = 'pending' AND u.admin_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="wishlish app using HTML, CSS, Javascript and PHP, MySql">
    <meta name="author" content="Minh Hieu Tran">
    <title>nightfury</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/output.css">
    <link rel="stylesheet" href="styles/style.css">
    <script src="scripts/script.js"></script>
</head>

<body>
    <header class="header">
        <img src="styles/images/headervector.png" alt="Header Background">
        <nav class="nav">
            <div class="logo">
                lightfury
            </div>
            <ul class="nav-links">
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
    <div class="mx-12">
        <div class="mb-8">
            <h2>Admin: <?php echo htmlspecialchars($admin_name); ?></h2>
            <h3>Moderated User: <?php echo htmlspecialchars($moderated_user); ?></h3>
            <h2>Pending Tasks</h2>
        </div>
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-lg">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="py-2 px-4 text-left text-gray-600 font-semibold">User</th>
                    <th class="py-2 px-4 text-left text-gray-600 font-semibold">Task</th>
                    <th class="py-2 px-4 text-left text-gray-600 font-semibold">Points</th>
                    <th class="py-2 px-4 text-left text-gray-600 font-semibold">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr class="border-b bg-gray-500 hover:bg-gray-700">
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['description']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['point_value']); ?></td>
                        <td class="py-2 px-4">
                            <form method="POST" action="./processes/processVerifyTask.php">
                                <input type="hidden" name="user_task_id" value="<?php echo htmlspecialchars($row['user_task_id']); ?>" />
                                <button type="submit" name="action" value="approve" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Approve</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="mt-8 mx-12">
        <h2 class="text-2xl font-bold mb-4">Recent Redemptions</h2>
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-lg">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th class="py-2 px-4 text-left text-gray-600 font-semibold">User</th>
                    <th class="py-2 px-4 text-left text-gray-600 font-semibold">Reward</th>
                    <th class="py-2 px-4 text-left text-gray-600 font-semibold">Redeemed At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT u.username, r.name, ur.redeemed_at 
                    FROM user_rewards ur
                    JOIN users u ON ur.user_id = u.id 
                    JOIN rewards r ON ur.reward_id = r.id
                    ORDER BY ur.redeemed_at DESC
                    LIMIT 10";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) { ?>
                    <tr class="border-b bg-gray-500 hover:bg-gray-700">
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['username']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['name']); ?></td>
                        <td class="py-2 px-4"><?php echo htmlspecialchars($row['redeemed_at']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>