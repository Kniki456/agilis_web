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
            <input class="search" id="searchInput" placeholder="Search....">
            <button class="vmi" onclick="searchByName()">Search</button>
        </div>

        <div class="section operation active" data-value="open-pets-container" id="open-pets-container">
            <ul class="rows">
                <?php
                getVmiRow();
                ?>
            </ul>
        </div>
    </main>
    <footer>
        <h3 class="btm">Copyright 2023 Rapid Math. All rights reserved. </h3>
    </footer>
</body>

</html>
<script>
    function searchByName() {
        var searchValue = document.getElementById('searchInput').value.toUpperCase();
        var items = document.querySelectorAll('.name');

        items.forEach(function(item) {
            var itemName = item.textContent.toUpperCase();
            var li = item.parentElement;

            if (itemName.indexOf(searchValue) > -1) {
                li.style.display = '';
            } else {
                li.style.display = 'none';
            }
        });
    }
</script>