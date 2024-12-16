<?php
session_start();
require_once './processes/conn.php';
require_once './processes/markingTask.php';

if (!$conn) {
    header("maintenance.php");
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit();
}

// Fetch user points
$sql = "SELECT points FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$points = $row['points'];
$stmt->close();

// Fetch tasks assigned to the user
$sql = "SELECT ut.id AS user_task_id, t.description, ut.status 
        FROM user_tasks ut
        JOIN tasks t ON ut.task_id = t.id
        WHERE ut.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$tasks_result = $stmt->get_result();
$tasks = [];
while ($task_row = $tasks_result->fetch_assoc()) {
    $tasks[] = $task_row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="wishlish app using HTML, CSS, Javascript and PHP, MySql">
    <meta name="author" content="Minh Hieu Tran">
    <title>nightfury</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="styles/output.css">
    <link rel="stylesheet" href="styles/style.css">
    <script src="scripts/script.js"></script>
</head>

<body>
    <header class="header">
        <img src="styles/images/headervector.png" alt="Header Background">
        <nav class="flex justify-center mt-12">
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="rewards.php">Rewards</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="points-box bg-white text-gray-800 p-2 rounded-lg shadow-lg fixed top-4 left-4">
        <span class="text-lg font-semibold">
            <?php echo "Welcome " . htmlspecialchars($_SESSION['username']) . ","; ?>
        </span>
        <br>
        <span class="text-lg font-semibold">You have
            <?php echo htmlspecialchars($points); ?>
            points!
        </span>
    </div>
    <div class="mx-64 mt-10 p-6 bg-gray-500 rounded-lg shadow-lg">
        <h3 class="text-center text-2xl font-semibold text-gray-800 mb-6">Your Tasks</h3>
        <div class="space-y-6">
            <!-- Daily Tasks -->
            <div class="task-category">
                <h4 class="text-xl font-semibold text-gray-700 mb-4">Daily Tasks</h4>
                <ul class="task-list space-y-4" id="daily-tasks">
                    <?php
                    foreach ($tasks as $task) {
                        echo "<li class='flex items-center justify-between'>";
                        echo "<span>" . htmlspecialchars($task['description']) . "</span>";

                        if ($task['status'] === 'unfinished') {
                            echo "<form method='POST' action='./processes/markingTask.php'>";
                            echo "<input type='hidden' name='user_task_id' value='" . htmlspecialchars($task['user_task_id']) . "' />";
                            echo "<button type='submit' class='btn btn-primary'>Complete</button>";
                            echo "</form>";
                        } elseif ($task['status'] === 'pending') {
                            echo "<span class='text-blue-500'>In Progress</span>";
                        } elseif ($task['status'] === 'confirmed') {
                            echo "<span class='text-green-500'>Approved</span>";
                        }

                        echo "</li>";
                    }
                    ?>
                </ul>
            </div>

            <!-- Weekly Tasks -->
            <div class="task-category">
                <h4 class="text-xl font-semibold text-gray-700 mb-4">Weekly Tasks</h4>
                <ul class="task-list space-y-4" id="weekly-tasks">
                    <!-- Similar logic for weekly tasks -->
                </ul>
            </div>
            <!-- One-Time Tasks -->
            <div class="task-category">
                <h4 class="text-xl font-semibold text-gray-700 mb-4">One-Time Tasks</h4>
                <ul class="task-list space-y-4" id="one-time-tasks">
                    <!-- Similar logic for one-time tasks -->
                </ul>
            </div>
        </div>
    </div>

    <footer class="footer mt-24">
        <img class="footer-vector" src="styles/images/footervector.png" alt="Footer Background">
        <div class="contact-me mt-24">
            <div class="left-side">
                <p class="mb-4">Contact me:</p>
                <a href="https://www.facebook.com/yr.nightfury/" aria-label="first link" target="_blank"><i
                        class="bx bxl-facebook"></i></a>
                <a href="https://www.instagram.com/_not.zilus/" aria-label="second link" target="_blank"><i
                        class="bx bxl-instagram-alt"></i></a>
                <a href="https://www.tiktok.com/@dslef.iwnl_" aria-label="third link" target="_blank"><i
                        class="bx bxl-tiktok"></i></a>
            </div>
            <img src="styles/images/me.png" alt="Admin" class="footer-image">
            <div>
                <p>Tui la Tran Minh Hieu</p>
                <p>anh be iu cua TPL >< </p>
            </div>
        </div>
    </footer>
</body>

</html>