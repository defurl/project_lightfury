<?php
session_start();
require_once 'processes/conn.php';

if (!$conn) {
    header("Location: maintenance.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['username'];
    $password = $_POST['password'];
    $errors = [];

    // name and password validation
    if (empty($name)) {
        $errors[] = "Username is required";
    }

    if (empty($password)) {
        $errors[] = "Password is required";
    }

    if (empty($errors)) {
        $sql = "SELECT id, password, role FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $dbpw = $row['password'];
            $role = $row['role'];
            $user_id = $row['id'];

            // check password in the database
            if ($password == $dbpw) {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $name;
                $_SESSION['user_id'] = $user_id;

                if ($role === 'admin') {
                    $_SESSION['admin_id'] = $user_id;
                    header("Location: admin.php");
                } else {
                    header("Location: main.php");
                }
                exit();
            } else {
                $errors[] = "Invalid password.";
            }
        } else {
            $errors[] = "Invalid username or password.";
        }

        $stmt->close();
    }

    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p>$error</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="wishlish app using HTML, TailwindCSS, Javascript and PHP, MySql">
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
        <nav class="nav">
            <div class="logo">
                lightfury
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
            </ul>
        </nav>
    </header>

    <div class="container-xl mt-12 mx-48">
        <h1 class="login-text text-3xl color-blue-300 mb-8">Login</h1>
        <form action="login.php" method="POST" class="width-1/2">
            <div>
                <input placeholder="Username" type="text" name="username" id="username" autocomplete="off" class="w-full px-3 py-2 border rounded-3xl mb-8 text-black">
            </div>
            <div>
                <input placeholder="Password" type="password" name="password" id="password" autocomplete="off" class="w-full px-3 py-2 border rounded-3xl mb-8 text-black">
            </div>
            <div class="flex justify-center mt-4">
                <button class="bg-gray-500 px-48 py-2 rounded-3xl">Login</button>
            </div>
        </form>
        <?php if (!empty($errors)) : ?>
            <div class="mt-4 text-red-600">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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