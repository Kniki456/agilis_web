<?php
include_once('db_config.php');

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_GET["logout"])) {
    if (!isset($_SESSION)) {
        session_start();
    }
    session_destroy();
    $_SESSION['response'] = "Kijelentkeztél!";
    session_commit();
    header("Location: index.php");
}

function getScoresRow()
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM highscores ORDER BY Score DESC");

    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            echo '
            <li class="item">
                <span class="name">' . $row['user_name'] . '</span>
                <span class="score">' . $row['Score'] . '</span>
                <div class="muveletek">
                    <form method="post"> 
                        <input type="hidden" name="pet-id" value="' . $row['user_id'] . '">
                        <button type="submit" name="pet-delete" class="btn btn-danger">Delete</button>
                    </form>
                    <form method="post" action="pet_modify.php">
                        <input type="hidden" name="pet-id" value="' . $row['user_id'] . '">
                        <button type="submit" class="btn btn-warning">Edit</button>
                    </form>
                </div>
            </li>';
        }
    }
}

function getVmiRow()
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM highscores ORDER BY Score DESC");

    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            echo '
            <li class="item">
                <span class="name">' . $row['user_name'] . '</span>
                <span class="score">' . $row['Score'] . '</span>
            </li>';
        }
    }
}

$error_message = ""; // Inicializáljuk az error_message változót
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Ellenőrzés a SQL-injekció ellen
    $email = mysqli_real_escape_string($con, $email);

    // Bejelentkezés ellenőrzése
    $sql = "SELECT * FROM admin WHERE email = '$email' AND password = '$password'";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        // Sikeres bejelentkezés
        session_start();
        $_SESSION["admin_email"] = $email; // Mentsd el az e-mail címet a session-ben
        header("Location: admin.php");
        exit();
    } else {
        $error_message = "Hibás e-mail cím vagy jelszó!";
    }
}