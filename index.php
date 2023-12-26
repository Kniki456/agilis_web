<?php
include "functions.php";
if (!isset($_SESSION)) {
    session_start();
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rapid Math</title>
    <link rel="icon" type="image/png" href="img/calculator4.png"/>
    <link rel="stylesheet" type="text/css" href="css/css.css">
    <link rel="stylesheet" type="text/css" href="css/nav.css">
    <link rel="stylesheet" type="text/css" href="css/admin.css">
    <link rel="stylesheet" href="css/tailwind.css">
    <link rel="stylesheet" href="css/tooplate-antique-cafe.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Kodchasan&family=Livvic&family=Megrim&display=swap" rel="stylesheet">
</head>

<body>
<header class="hehe">
    <nav>
        <ul class="lista1">
            <li><a href="index.php">Home</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="scores.php">Highscores</a></li>
            <li><a href="login.php">Log in</a></li>
        </ul>
    </nav>
    <img class="logo" src="img/logo.jpg">
    <div class="header-content">
        <h1 class="header-title">Rapid</h1>
        <h1 class="header-title1">Math</h1>
        <h6 class="header-mono">How fast can you count?</h6> <br>
        <a class="button-21" href="infok.php">Play now!</a>
    </div>

</header>
<main class="ind">
    <?php response(); ?>
    <h3 class="about" id="about">About the game</h3>
    <div class="container1">
        <div class="section">
            <img class="kepecske" src="img/help.PNG.png" alt="Kép 1">
        </div>
        <br>
        <div class="section1">
            <p class="description">With this game you can find out how fast you can perform various calculations.<br>
                The game consists of 30 levels, each level has 8 number of tasks that must be completed in 30 seconds or 1 minutes. If you solve the task well, you get 20 point.<br><br>

                You can see how good you are compared to the others in the highscore list.<br><br>
                Solve addition, subtraction, division, and multiplication questions.<br><br>
                Earn more bonus points the faster you answer.</p>
        </div>
    </div>

</main>
<div id="contact" class="parallax-window relative" data-parallax="scroll" data-image-src="img/antique-cafe-bg-04.jpg">
    <div class="custom-shape-divider-top-1701094399">
        <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
            <path d="M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z" opacity=".25" class="shape-fill"></path>
            <path d="M0,0V15.81C13,36.92,27.64,56.86,47.69,72.05,99.41,111.27,165,111,224.58,91.58c31.15-10.15,60.09-26.07,89.67-39.8,40.92-19,84.73-46,130.83-49.67,36.26-2.85,70.9,9.42,98.6,31.56,31.77,25.39,62.32,62,103.63,73,40.44,10.79,81.35-6.69,119.13-24.28s75.16-39,116.92-43.05c59.73-5.85,113.28,22.88,168.9,38.84,30.2,8.66,59,6.17,87.09-7.5,22.43-10.89,48-26.93,60.65-49.24V0Z" opacity=".5" class="shape-fill"></path>
            <path d="M0,0V5.63C149.93,59,314.09,71.32,475.83,42.57c43-7.64,84.23-20.12,127.61-26.46,59-8.63,112.48,12.24,165.56,35.4C827.93,77.22,886,95.24,951.2,90c86.53-7,172.46-45.71,248.8-84.81V0Z" class="shape-fill"></path>
        </svg>
    </div>
    <div class="container mx-auto tm-container pt-24 pb-48 sm:py-48">
        <div class="flex flex-col lg:flex-row justify-around items-center lg:items-stretch">
            <div class="flex-1 rounded-xl px-10 py-12 m-5 bg-white bg-opacity-80 tm-item-container">
                <h2 class="text-3xl mb-6 tm-text-green">Contact Us</h2>
                <p class="mb-6 text-lg leading-8">
                    If you have any questions or problems with the game, feel free to contact us!
                </p>
                <p class="mb-10 text-lg">
                    <span class="block mb-2">Tel: <a>010-020-0340</a></span>
                    <span class="block">Email: <a>rapidmath621@gmail.com</a></span>
                </p>
            </div>
            <div class="flex-1 rounded-xl p-12 pb-14 m-5 bg-black bg-opacity-50 tm-item-container">
                <form action="" method="POST" class="text-lg">
                    <input type="text" name="name" class="input w-full bg-black border-b bg-opacity-0 px-0 py-4 mb-4 tm-border-gold" placeholder=" Name" required="" />
                    <input type="email" name="email" class="input w-full bg-black border-b bg-opacity-0 px-0 py-4 mb-4 tm-border-gold" placeholder=" Email" required="" />
                    <textarea rows="6" name="message" class="input w-full bg-black border-b bg-opacity-0 px-0 py-4 mb-4 tm-border-gold" placeholder=" Message..." required=""></textarea>
                    <div class="text-right">
                        <button type="submit" class="button">Send it</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <footer class="absolute bottom-0 left-0 w-full">
        <h3 class="btm">Copyright 2023 Rapid Math. All rights reserved. </h3>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>
