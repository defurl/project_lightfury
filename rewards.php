<?php
session_start();
require_once './processes/conn.php';

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


// Fetch rewards from the database
$sql = "SELECT id, name, description, cost, picture FROM rewards";
$result = $conn->query($sql);
$rewards = [];
while ($row = $result->fetch_assoc()) {
    $rewards[] = $row;
}

// $responseData = [
//     'total_rewards' => count($rewards),
//     'rewards_data' => $rewards,
//     'sql_query' => $sql,
//     'user_points' => $_SESSION['points'] ?? 0
// ];

// echo json_encode($responseData, JSON_PRETTY_PRINT);
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
            <ul class="nav-links flex space-x-4">
                <li><a href="index.php" class="">Home</a></li>
                <li><a href="main.php" class="">Missions</a></li>
                <li><a href="logout.php" class="">Logout</a></li>
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

    <div class="container mx-auto mt-10">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <?php
                echo htmlspecialchars($_SESSION['message']);
                unset($_SESSION['message']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <?php
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <h1 class="text-3xl font-bold text-center mb-8">Rewards</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php foreach ($rewards as $reward) { ?>
                <div class="bg-gray-500 p-6 rounded-lg shadow-lg text-center">
                    <img src="<?php echo htmlspecialchars($reward['picture']); ?>"
                        alt="<?php echo htmlspecialchars($reward['name']); ?>"
                        class="reward-image object-cover mx-auto mb-4 rounded">
                    <h2 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($reward['name']); ?></h2>
                    <p class="mb-4"><?php echo htmlspecialchars($reward['description']); ?></p>
                    <p class="font-bold mb-4">Cost: <?php echo htmlspecialchars($reward['cost']); ?> points</p>
                    <form method="POST" action="./processes/processRedeem.php">
                        <input type="hidden" name="reward_id" value="<?php echo htmlspecialchars($reward['id']); ?>">
                        <button type="submit" class="bg-blue-500 px-4 py-2 rounded hover:bg-blue-700">Redeem</button>
                    </form>
                </div>
            <?php } ?>
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