<?php
include "functions.php";
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Rapid Math</title>
    <link rel="icon" type="image/png" href="img/calculator4.png"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="css/css.css">
    <link rel="stylesheet" type="text/css" href="css/nav.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kodchasan&family=Livvic&family=Megrim&display=swap" rel="stylesheet">
</head>

<body class="valami">
    <header>
        <nav>
            <ul class="lista1">
                <li><a href="?logout=true">Log out</a></li>
            </ul>
        </nav>
    </header>
    <img class="logo" src="img/logo.jpg">
    <h1 class="cim">Rapid Math</h1>
    <main class="admin">
        <div class="section section-nav">
            <h1 class="highscores">Highscores</h1>
            <input type="text" class="search" onkeyup="search();" placeholder="Search...">
        </div>
        <!-- Pets -->
        <div class="section operation active" data-value="open-pets-container" id="open-pets-container">

            <ul class="rows">
                <?php
                getPetsRow();
                ?>
            </ul>
        </div>
        <!-- users  -->
        <div class="section operation" data-value="open-users-container" id="open-users-container">
            <h3>Felhasználók</h3>
            <ul class="rows">
                <?php
                getUsersRow();
                ?>
            </ul>
        </div>
    </main>
    <footer>
        <h3 class="btm">Copyright 2023 Rapid Math. All rights reserved. </h3>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>


<script>function search() {
    var input = document.querySelector(".search");
    var items = document.querySelectorAll(".active .rows .item");

    filter = input.value.toLowerCase();
    for (i = 0; i < items.length; i++) {
    title = items[i].querySelector(".name");
    if (title.innerText.toLowerCase().indexOf(filter) > -1) {
    items[i].style.display = "";
        } else {
        items[i].style.display = "none";
        }
    }
}
</script>