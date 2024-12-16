<?php
session_start();
require_once './processes/conn.php';

if (!$conn) {
    header("maintenance.php");
}

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
        <nav class="nav">
            <div class="logo">
                lightfury
            </div>
            <ul class="nav-links">
                <li><a href="main.php">Missions</a></li>
                <li><a href="login.php">Login</a></li>
            </ul>
        </nav>
    </header>

    <main class="position-relative">
        <div class="relative min-h-screen">
            <i class='bx bxs-heart absolute left-[15%] text-3xl text-red-500 rotate-45'></i>

            <i class='bx bxs-heart absolute top-[25%] right-[10%] text-4xl text-rose-400 -rotate-12'></i>

            <i class='bx bxs-heart absolute top-[45%] left-[30%] text-2xl text-pink-500 rotate-90'></i>

            <i class='bx bxs-heart absolute bottom-[10%] left-[25%] text-3xl text-rose-300 rotate-180'></i>

            <i class='bx bxs-heart absolute bottom-[15%] right-[30%] text-4xl text-red-400 -rotate-45'></i>

            <div class="intro-container">
                <div class="intro-content">
                    Baby I'd give up anything for you to be by my side ~
                </div>
                <div class="image-content">
                    <img class="imgtop" src="styles/images/homepageimg.png" alt="Homepage Image">
                    <!-- <img class="imgbot" src="styles/images/homepageimg-border.png" alt="Homepage image Border"> -->
                    <img class="box-vector" src="styles/images/presentbox.png" alt="Present Box">
                </div>
            </div>

            <div class="main-content">
                <div class="rules">
                    <div class="tasks">
                        <div class="firstrow">
                            <p>Completing a task will reward you with an amount of points.</p>
                        </div>
                        <br>
                        <div class="secondrow">
                            <p>You must send proof to Admin via Messenger or face-to-face.</p>
                            <p>Being naughty will result in point deductions >;[</p>
                        </div>
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
    </main>
</body>

</html>