<?php
include_once "functions.php";
if (!isset($_SESSION)) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="stylesheet" href="css/login.css">
    <link rel="icon" type="image/png" href="img/calculator4.png"/>
    <link rel="stylesheet" type="text/css" href="css/nav.css">
    <link rel="stylesheet" type="text/css" href="css/css.css">
    <link rel="stylesheet" type="text/css" href="css/admin.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Bejelentkezés</title>
    <link href="https://fonts.googleapis.com/css2?family=Kodchasan&family=Livvic&family=Megrim&display=swap" rel="stylesheet">
</head>

<body class="valami">
    <img class="logo" src="img/logo.jpg">
    <h1 class="cim2">Rapid Math</h1>
    <section>
        <div class="form-box">
            <div class="form-box1"
            <div class="form-value">
                <form action="" method="post">
                    <h2 class="reg">Log in</h2>
                    <div class="inputbox2">
                        <ion-icon name="mail-outline"></ion-icon>
                        <input type="text" id="login-email" name="login-email" required class="error">
                        <label for="">E-mail</label>
                    </div>
                    <div class="inputbox2">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                        <input type="password" id="login-password" name="login-password" required>
                        <label for="">Passworld</label>
                    </div>

                    <button name="login-submit" id="login-button">Log in</button>

                </form>
            </div>
        </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</script>
</body>

</html>