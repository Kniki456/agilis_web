<?php
include_once('db_config.php');


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION)) {
    session_start();
}
function response()
{
    if (isset($_SESSION['response']) && $_SESSION['response'] != "") {
        echo '
    <div class="alert alert-warning alert-dismissible fade show" role="alert" id="toast">
        <strong>' . $_SESSION['response'] . '</strong>
        <button type="button" class="close" id="toastCloseBtn" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <script src="script/responseClose.js"></script>'
        ;
        // var_dump(($_SESSION['response']));
        $_SESSION['response'] = "";
        session_commit();

    }
}


function sendVerificationMail($email, $verification_key)
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = gethostbyname('medve.stud.vts.su.ac.rs');
    $mail->SMTPAuth = "true";
    $mail->Username = "medve";
    $mail->Password = "ltFIHmnBvdnIoaM";
    $mail->SMTPSecure = "ssl";
    $mail->Port = 465;
    $mail->setFrom("medve@medve.stud.vts.su.ac.rs");
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = "Verify code";
    $mail->Body = 'Click here to verify registration: http://localhost/ehh/functions.php?verification_key=' . $verification_key;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );
    $mail->send();
}

function randomVerificationKey()
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < 20; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
    return $randomString;
}

//Register
if (isset($_POST["register-submit"])) {
    if (!empty($_POST["register-email"]) || !empty($_POST["register-password"]) || !empty($_POST["register-password-repeat"])) {
        // Check if email already exists
        $stmt = mysqli_prepare($con, "SELECT * FROM user WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $_POST['register-email']);
        mysqli_stmt_execute($stmt);
        $rows = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        if (mysqli_num_rows($rows) > 0) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['response'] = "User already registered!";
            session_commit();
            // header("Location:" . $_SERVER['HTTP_REFERER']);
        } else {
            // salt
            $options = [
                'cost' => 11
            ];
            $register_password = $_POST['register-password'];
            // BCRYPT
            $hash = password_hash($register_password, PASSWORD_BCRYPT, $options);
            // Array for the nem user
            $new_user = [
                $email = $_POST['register-email'],
                $hash,
                $verification_key = randomVerificationKey()
            ];
            // Insert query with POD params
            $query = "INSERT INTO user (email,password,token) VALUES (?,?,?)";
            $pdo->prepare($query)->execute($new_user);
            $pdo->beginTransaction();
            if ($pdo->commit() > 0) {
                sendVerificationMail($_POST['register-email'], $verification_key);
                if (!isset($_SESSION)) {
                    session_start();
                }
                $_SESSION['response'] = "Sikeres regisztráció. Emailbe küldtünk hitelesítőt!";
                session_commit();
                // header("Location:" . $_SERVER['HTTP_REFERER']);
            } //Error lehet?
        }
    } else {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Üres mezők";
        session_commit();
        // header("Location:" . $_SERVER['HTTP_REFERER']);
    }
}
// Verificiation status update
if (isset($_GET['verification_key'])) {
    $stmt = mysqli_prepare($con, "SELECT * FROM user WHERE token = ?");
    mysqli_stmt_bind_param($stmt, 's', $_GET['verification_key']);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        $row = mysqli_fetch_array($rows);
        $query = "UPDATE user SET status = 1 WHERE token = ?";
        $verification = [
            $verification_key = $_GET['verification_key']
        ];
        $pdo->prepare($query)->execute($verification);
        $pdo->beginTransaction();
        if ($pdo->commit() > 0) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['email'] = $row['email'];
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['response-text'] = "Sikeres verifikáció";
            session_commit();
            header("Location:profile.php");
        } //Error lehet?
    } else {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Hibás token";
        session_commit();
        header("Location:index.php");
    }
}
// Login
if (isset($_POST["login-submit"])) {
    if (!isset($_SESSION)) {
        session_start();
    }
    if (!is_banned($_POST['login-email'])) {
        if (is_verified($_POST['login-email'])) {
            $stmt = mysqli_prepare($con, "SELECT * FROM user WHERE email = ? AND status = 1");
            mysqli_stmt_bind_param($stmt, 's', $_POST['login-email']);
            mysqli_stmt_execute($stmt);
            $rows = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
            if (mysqli_num_rows($rows) > 0) {
                $row = mysqli_fetch_assoc($rows);
                if (password_verify($_POST['login-password'], $row["password"])) {
                    $_SESSION['email'] = $_POST['login-email'];
                    $_SESSION['user_id'] = $row['user_id'];
                    //                    $_SESSION['response'] = true;
//                    $_SESSION['response-text'] = "succesfully-logined";
                    session_commit();
                    header("Location: admin.php");
                } else {
                    if (!isset($_SESSION)) {
                        session_start();
                    }
                    $_SESSION['response'] = "Hibás kombináció!";
                    session_commit();
                    // header("Location:" . $_SERVER['HTTP_REFERER']);
                }
            } else {
                if (!isset($_SESSION)) {
                    session_start();
                }
                $_SESSION['response'] = "Nincs ilyen felhasználó! Vagy nincs még verifikálva!";
                session_commit();
                // header("Location:" . $_SERVER['HTTP_REFERER']);
            }
        } else {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['response'] = "Nincs ilyen felhasználó! Vagy nincs még verifikálva!";
            session_commit();
            // header("Location:" . $_SERVER['HTTP_REFERER']);
        }
    } else {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Felfüggesztett felhasználó!";
        session_commit();
        //        session_commit();
    }
}
// Admin ? 1 : 0
function is_admin($email)
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM user WHERE email = ? AND admin = 1");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        return true;
    } else
        false;
}
// Banned ? 1 : 0
function is_banned($email)
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM user WHERE email = ? AND status = 2");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        return true;
    } else {
        false;
    }
}
function is_veterinarian($id)
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM veterinarians WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        return true;
    } else {
        false;
    }
}
function is_verified_veterinarian($id)
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM veterinarians WHERE user_id = ? and status = 1");
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        return true;
    } else {
        false;
    }
}
// Verified ? 1 : 0
function is_verified($email)
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM user WHERE email = ? AND status = 1");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        return true;
    } else
        false;
}

function getStatus($id)
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM user WHERE user_id = ? AND admin = 1");
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        return "admin";
    }
    $stmt = mysqli_prepare($con, "SELECT * FROM user WHERE status = 0 and user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        return "unverificated";
    }
    $stmt = mysqli_prepare($con, "SELECT * FROM user WHERE status = 2 and user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        return "banned";
    }
    $stmt = mysqli_prepare($con, "SELECT * FROM veterinarians WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        return "veterinarian";
    } else {
        return "user";
    }

}

function is_yourPet($user_id, $pet_id)
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM user
     WHERE user_id = ? and admin = 1");
    mysqli_stmt_bind_param($stmt, 's', $user_id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        return true;
    }
    $stmt = mysqli_prepare($con, "SELECT * FROM pets
     WHERE (user_id = ? AND pet_id = ?)");
    mysqli_stmt_bind_param($stmt, 'ss', $user_id, $pet_id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        return true;
    } else
        false;
}
function getUserData()
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM user WHERE email = ? ");
    mysqli_stmt_bind_param($stmt, 's', $_SESSION['email']);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        $row = mysqli_fetch_array($rows);
        // $_SESSION['description'] = $row['description'];
        $_SESSION['user_id'] = $row['user_id'];
        $_SESSION['firstname'] = $row['firstname'];
        $_SESSION['lastname'] = $row['lastname'];
        $_SESSION['address'] = $row['address'];
        $_SESSION['gender'] = $row['gender'];
        $_SESSION['status'] = $row['status'];
        $_SESSION['picture'] = $row['picture'];
        $_SESSION['phone'] = $row['phone'];
        $_SESSION['admin'] = $row['admin'];

    }
}

function getveterinarianData($id)
{
    $data = [];
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM veterinarians WHERE user_id = ? ");
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        $row = mysqli_fetch_array($rows);
        // $_SESSION['description'] = $row['description'];
        $data["veterinarian-id"] = $row['veterinarian_id'];
        $data["specialization"] = $row['specialization'];
        $data["start"] = $row['work_start'];
        $data["end"] = $row['work_end'];
        $data["place"] = $row['place'];
    }
    return $data;
}


function getWalkerCards()
{
    $sql = "SELECT * FROM veterinarians v
    INNER JOIN user u ON v.user_id=u.user_id 
    WHERE v.status=1 ";
    if (isset($_POST['categorySearch'])) {

        if (isset($_POST['city'])) {
            $sql .= " AND v.place = '" . $_POST['city'] . "'";
        }
        if (isset($_POST['specialization'])) {
            $sql .= " AND v.specialization = '" . $_POST['specialization'] . "'";
        }
    }
    //
    include('db_config.php');
    $stmt = mysqli_prepare($con, $sql);

    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    echo '<h2>Sétáltatók</h2> <div class="container d-flex flex-wrap align-items-center">';
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            $id = $row["veterinarian_id"];
            echo '   
      
    <div class="card" style="width:400px">
        <img class="card-img-top" src=' . $row["picture"] . ' alt="Card image" style="width:100%">
        <div class="card-body">
            <h4 class="card-title">' . $row["firstname"] . ' ' . $row["lastname"] . '</h4>
            <p class="card-text">Város: ' . $row["place"] . '</p> 
            <p class="card-text">Munkaidő ' . $row["work_start"] . '-' . $row["work_end"] . '</p>
            
            <ul class="list-group list-group-flush">
                <li class="list-group-item">' . $row["specialization"] . '</li>   
             ';
            echo '</ul>
<form action="walkerprof.php" method="post">
            <input type="hidden" name="vet-id" value="' . $row["veterinarian_id"] . '">
            <button type="submit" class="btn btn-warning" name="doctor">További információ</button>
            </form></div></div>';
        }
    }

    echo '</div>';
}

function getFiveCards()
{
    $sql = "SELECT * FROM veterinarians v
    INNER JOIN user u ON v.user_id=u.user_id 
    WHERE v.status=1 ";
    if (isset($_POST['categorySearch'])) {

        if (isset($_POST['city'])) {
            $sql .= " AND v.place = '" . $_POST['city'] . "'";
        }
        if (isset($_POST['specialization'])) {
            $sql .= " AND v.specialization = '" . $_POST['specialization'] . "'";
        }
    }
    // Add ORDER BY and LIMIT to get the 5 most active veterinarians
    $sql .= " ORDER BY (TIME_TO_SEC(v.work_end) - TIME_TO_SEC(v.work_start)) DESC LIMIT 5";

    include('db_config.php');
    $stmt = mysqli_prepare($con, $sql);

    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    echo '<h2>Kiemelkedő sétáltatóink</h2> <div class="container d-flex flex-wrap align-items-center">';
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            $id = $row["veterinarian_id"];
            echo '   
                <div class="card" style="width:400px">
                    <img class="card-img-top" src=' . $row["picture"] . ' alt="Card image" style="width:100%">
                    <div class="card-body">
                        <h4 class="card-title">' . $row["firstname"] . ' ' . $row["lastname"] . '</h4>
                        <p class="card-text">Város: ' . $row["place"] . '</p> 
                        <p class="card-text">Munkaidő ' . $row["work_start"] . '-' . $row["work_end"] . '</p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">' . $row["specialization"] . '</li>   
                        ';
            echo '</ul>
                        <form action="walkerprof.php" method="post">
                            <input type="hidden" name="vet-id" value="' . $row["veterinarian_id"] . '">
                            <button type="submit" class="btn btn-warning" name="doctor">További információ</button>
                        </form>
                    </div>
                </div>';
        }
    }

    echo '</div>';
}


function getFiveCards2()
{
    $sql = "SELECT u.firstname, u.lastname, u.picture, MAX(v.place) AS place, v.specialization, AVG(r.rating) AS average_rating
            FROM user u
            INNER JOIN veterinarians v ON u.user_id = v.user_id
            LEFT JOIN reviews r ON v.veterinarian_id = r.veterinarian_id
            WHERE v.status = 1
            GROUP BY u.user_id, u.firstname, u.lastname, u.picture, v.specialization
            ORDER BY average_rating DESC
            LIMIT 5";

    include('db_config.php');
    $stmt = mysqli_prepare($con, $sql);

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    echo '<h2>Legjobban értékelt sétáltatóink</h2> <div class="container d-flex flex-wrap align-items-center">';
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_array($result)) {
            echo '   
                <div class="card" style="width:400px">
                    <img class="card-img-top" src=' . $row["picture"] . ' alt="Card image" style="width:100%">
                    <div class="card-body">
                        <h4 class="card-title">' . $row["firstname"] . ' ' . $row["lastname"] . '</h4>
                        <p class="card-text">Város: ' . $row["place"] . '</p> 
                        <p class="card-text">Értékelés: <b>' . number_format($row["average_rating"], 2) . '</b></p>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">' . $row["specialization"] . '</li>   
                        ';
            echo '</ul>
                    </div>
                </div>';
        }
    }

    echo '</div>';
}


function WalkerProf($id)
{
    $doctorinfo = [];
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM veterinarians v 
         INNER JOIN user u ON v.user_id = u.user_id
         WHERE veterinarian_id = ? ");
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        $row = mysqli_fetch_array($rows);
        $doctorinfo["name"] = $row["firstname"] . ' ' . $row["lastname"];
        $doctorinfo["email"] = $row["email"];
        $doctorinfo["gender"] = $row["gender"];
        $doctorinfo["phone"] = $row["phone"];
        $doctorinfo["specialization"] = $row["specialization"];
        $doctorinfo["work"] = $row["work_start"] . '-' . $row["work_end"];
        $doctorinfo["picture"] = $row["picture"];

    }
    return $doctorinfo;
}

function WalkerServices($id)
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM services WHERE veterinarian_id = ? ");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) >= 0) {
        while ($row = mysqli_fetch_array($rows)) {
            echo '<li class="service-li"><div class="service-li-left">
            <span><b>Ár: ' . $row['service_price'] . '</b></span>
            </div>
            <span>Leírás: ' . $row['service_description'] . '</span>' . '
            <form action="termin_reservation.php" method="post">
            <input type="hidden" name="service_id" value="' . $row["service_id"] . '">';
            if (isset($_SESSION['selected-pet'])) {
                echo '<button class="service-li-button" name="selected-service-btn" type="submit" name="choose-service-btn">Ezt választom!</button>';
            }

            echo '
            </form>
            </li>';
        }
    } else {
        echo '<li class="service-li">Nincs szolgáltatás elkönyvelve! További információkért érdeklődjön telefonon!</li>';
    }
}

function walkerTermins($id)
{
    $datumok = [];
    $idopontok = [];
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM veterinarians WHERE veterinarian_id = ? ");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        $row = mysqli_fetch_array($rows);
        $work_start = $row['work_start'];
        $work_end = $row['work_end'];
    }
    // idopontok kiszedese
    $stmt = mysqli_prepare($con, "SELECT * FROM termins WHERE veterinarian_id = ? AND date_termin = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $id, $_SESSION['selected-date']);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            array_push($idopontok, $row['hour_termin']);
            // array_push($datumok, $row['date_termin']);
        }
    }
    //szabad idopontok megjelenitese
    for ($i = $work_start; $i < $work_end; $i++) {
        if (in_array($i, $idopontok)) {
            echo '
            <li class="service-li">
                <span>Időpont(Foglalt): ' . $i . '-' . ($i + 1) . 'h</span>
            </li>';
        } else {
            echo '<li class="service-li">
        <span>Időpont: ' . $i . '-' . ($i + 1) . 'h</span>
        <form method="post">
        <input type="hidden" name="hour-termin" value="' . $i . '">
        <button class="service-li-button" type="submit" name="choose-termin-btn">Ezt választom!</button>
        </form>
        </li>';
        }
    }
}

function getSpecializations()
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT DISTINCT specialization FROM veterinarians");
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            echo '<option value="' . $row['specialization'] . '">' . $row['specialization'] . '</option>';
        }
    }
}

function getLocations()
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT DISTINCT place FROM veterinarians");

    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            echo '<option value="' . $row['place'] . '">' . $row['place'] . '</option>';
        }
    }
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

function getAnimals()
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM pets where user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            echo '<div class="card mb-3" style="max-width: 540px;">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="' . $row['photo'] . '" class="img-fluid rounded-start" alt="...">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">Név: ' . $row['pet_name'] . '</h5>
                        <p class="card-text">Faj: ' . $row['species'] . '</p>
                        <p class="card-text">Nem: ' . $row['gender'] . '</p>
                    </div>
                    <p class="card-text"><small class="text-muted">Született: ' . $row['birth_date'] . '</small></p>
                </div>
                <div class="buttons">
                    <form action="" method="post" class="col-md-8 pet-form">
                        <input type="hidden" name="pet-id" value="' . $row["pet_id"] . '">
                        <button type="submit" name="pet-delete" class="btn btn-danger">Törlés</button>
                    </form>
                    <form action="pet_modify.php" method="post" class="col-md-8 pet-form">
                        <input type="hidden" name="pet-id" value="' . $row["pet_id"] . '">
                        <button type="submit" name="pet-modify" class="btn btn-warning">Szerkesztés</button>
                    </form>
                    <form action="pet.php" method="post" class="col-md-8 pet-form">
                        <input type="hidden" name="pet-id" value="' . $row["pet_id"] . '">
                        <button type="submit" name="pet-choose" class="btn btn-success">Mutasd</button>
                    </form>
                </div>
            </div>
        </div>';
        }
    }
}

function getPet($id)
{
    $petinfo = [];
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM pets 
         WHERE pet_id = ? ");
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        $row = mysqli_fetch_array($rows);
        $petinfo["name"] = $row["pet_name"];
        $petinfo["species"] = $row["species"];
        $petinfo["gender"] = $row["gender"];
        $petinfo["picture"] = $row["photo"];
        $petinfo["birth_date"] = $row["birth_date"];

    }
    return $petinfo;
}

function getSpecies($specie = "")
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT DISTINCT species FROM pets");
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            if ($row['species'] == $specie) {
                echo '<option value="' . $row['species'] . '" selected>' . $row['species'] . '</option>';
            } else {
                echo '<option value="' . $row['species'] . '">' . $row['species'] . '</option>';
            }


        }
    }
}

function getPetCards()
{
    $sql = "SELECT * FROM pets p 
    INNER JOIN user u ON u.user_id = p.user_id 
    WHERE 0=0 ";
    if (isset($_POST['categorySearchPets'])) {

        if (isset($_POST['species'])) {
            $sql .= " AND species = '" . $_POST['species'] . "'";
        }
    }
    // 
    include('db_config.php');
    $stmt = mysqli_prepare($con, $sql);

    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    echo '<h2>Kisállatok</h2> <div class="container d-flex flex-wrap align-items-center">';
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            echo '   
      
    <div class="card" style="width:400px">
        <img class="card-img-top" src=' . $row["photo"] . ' alt="Card image" style="width:100%">
        <div class="card-body">
            <h4 class="card-title">' . $row["pet_name"] . '</h4>
            <p class="card-text">Fajta: ' . $row["species"] . '</p> 
            
            <ul class="list-group list-group-flush">
                <li class="list-group-item">' . $row["firstname"] . ' ' . $row["lastname"] . '</li>   
             ';
            echo '</ul>
<form action="pet.php" method="post">
            <input type="hidden" name="pet-id" value="' . $row["pet_id"] . '">
            <button type="submit" class="btn btn-warning" name="petprofile">Profil megtekintése</button>
            </form></div></div>';
        }
    }

    echo '</div>';
}

if (isset($_POST["pet-delete"])) {
    if (is_admin($_SESSION["email"])) {
        $stmt = mysqli_prepare($con, "SELECT * FROM pets
        WHERE pet_id = ?");
        mysqli_stmt_bind_param($stmt, 's', $_POST['pet-id']);
    } else {
        $stmt = mysqli_prepare($con, "SELECT * FROM pets
        WHERE pet_id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, 'ss', $_POST['pet-id'], $_SESSION['user_id']);
    }
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) <= 0) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Ez a kisállat nem létezik vagy nem a tiéd!";
        session_commit();
    } else {
        $delete_pet = [
            $pet_id = $_POST['pet-id']
        ];



        //termin
        $query2 = "DELETE FROM termins WHERE pet_id = ?";
        $pdo->prepare($query2)->execute($delete_pet);
        $pdo->beginTransaction();
        $pdo->commit();

        //allat
        $query3 = "DELETE FROM pets WHERE pet_id = ?";
        $pdo->prepare($query3)->execute($delete_pet);
        $pdo->beginTransaction();
        $pdo->commit();

        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Sikeresen kitörölted a kisállatot!";
        session_commit();
        // header("Location: profile.php");
    }
}

function getPetsRow()
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM pets");

    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            echo '
            <li class="item">
                <img class="rows-img" src="' . $row['photo'] . '">
                <span class="name">' . $row['pet_name'] . '</span>
                <div class="muveletek">
                    <form method="post"> 
                        <input type="hidden" name="pet-id" value="' . $row['pet_id'] . '">
                        <button type="submit" name="pet-delete" class="btn btn-danger">Delete</button>
                    </form>
                    <form method="post" action="pet_modify.php">
                        <input type="hidden" name="pet-id" value="' . $row['pet_id'] . '">
                        <button type="submit" class="btn btn-warning">Edit</button>
                    </form>
                    
                </div>
            </li>';
        }
    }
}

function getUsersRow()
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM user");

    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            echo '
            <li class="item">
                <img class="rows-img" src="' . $row['picture'] . '">
                <span class="name">' . $row['email'] . '</span>';

            echo '<form class="muveletek" method="post">
                    <input type="hidden" name="user_id" value="' . $row['user_id'] . '">';
            if ($row["status"] == 2) {
                echo '<button type="submit" name="unban" class="btn btn-success">Engedélyezés</button>';
            } else {
                echo '<button type="submit" name="ban" class="btn btn-danger">Felfüggesztés</button>';
            }
            if ($row["admin"] == 0) {
                echo '<button type="submit" name="setAdmin" class="btn btn-success">Admin jogosultság hozzáadása</button>';
            } else {
                echo '<button type="submit" name="unsetAdmin" class="btn btn-danger">Admin jogosultság elvétele</button>';
            }
            echo '
                </form>
            </li>';
        }
    }
}

function getWalkerRow()
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT u.picture,u.email,u.user_id,v.status as 'status1', u.status as 'status2', u.admin FROM veterinarians v 
    INNER JOIN user u ON u.user_id = v.user_id
    ");

    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            echo '
            <li class="item">
                <img class="rows-img" src="' . $row['picture'] . '">
                <span class="name">' . $row['email'] . '</span>
                <form class="muveletek" method="post">
                    <input type="hidden" name="user_id" value="' . $row['user_id'] . '">';
            // if ($row["status2"] == 2) {
            //     echo '<button type="submit" name="unban" class="btn btn-success">Engedélyezés</button>';
            // } else {
            //     echo '<button type="submit" nam="ban" class="btn btn-danger">Felfüggesztés</button>';
            // }
            // if ($row["admin"] == 0) {
            //     echo '<button type="submit" name="setAdmin" class="btn btn-success">Admin jogosultság hozzáadása</button>';
            // } else {
            //     echo '<button type="submit" name="unsetAdmin" class="btn btn-danger">Admin jogosultság elvétele</button>';
            // }
            if ($row["status1"] == 0) {
                echo '<button type="submit" name="verify" class="btn btn-success">Verifikáció</button>';
            } else {
                echo '<button type="submit" name="unVerify"  class="btn btn-danger">UnVerifikáció</button>';
            }
            echo '
                </form>
            </li>';
        }
    }
}

if (isset($_POST["ban"])) {
    $query = "UPDATE user SET status = 2 WHERE user_id = ?";
    $user = [
        $id = $_POST['user_id']
    ];
    $pdo->prepare($query)->execute($user);
    $pdo->beginTransaction();
    if ($pdo->commit() > 0) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Sikeres felfüggesztés!";
        session_commit();
        // header("Location:profile.php");
    }
}

if (isset($_POST["unban"])) {
    $query = "UPDATE user SET status = 1 WHERE user_id = ?";
    $user = [
        $id = $_POST['user_id']
    ];
    $pdo->prepare($query)->execute($user);
    $pdo->beginTransaction();
    if ($pdo->commit() > 0) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Sikeres engedélyezés!";
        session_commit();
        // header("Location:profile.php");
    }
}

if (isset($_POST["setAdmin"])) {
    $query = "UPDATE user SET admin = 1 WHERE user_id = ?";
    $user = [
        $id = $_POST['user_id']
    ];
    $pdo->prepare($query)->execute($user);
    $pdo->beginTransaction();
    if ($pdo->commit() > 0) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Sikeres admin jogosultság megadva!";
        session_commit();
        // header("Location:profile.php");
    }
}


if (isset($_POST["unsetAdmin"])) {
    $query = "UPDATE user SET admin = 0 WHERE user_id = ?";
    $user = [
        $id = $_POST['user_id']
    ];
    $pdo->prepare($query)->execute($user);
    $pdo->beginTransaction();
    if ($pdo->commit() > 0) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Sikeres admin jogosultság megvonás!";
        session_commit();
        // header("Location:profile.php");
    }
}

if (isset($_POST["verify"])) {
    $query = "UPDATE veterinarians SET status = 1 WHERE user_id = ?";
    $user = [
        $id = $_POST['user_id']
    ];
    $pdo->prepare($query)->execute($user);
    $pdo->beginTransaction();
    if ($pdo->commit() > 0) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Sikeres verifikáció!";
        session_commit();
        // header("Location:profile.php");
    }
}
if (isset($_POST["unVerify"])) {
    $query = "UPDATE veterinarians SET status = 0 WHERE user_id = ?";
    $user = [
        $id = $_POST['user_id']
    ];
    $pdo->prepare($query)->execute($user);
    $pdo->beginTransaction();
    if ($pdo->commit() > 0) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Sikeres verifikáció megvonása!";
        session_commit();
        // header("Location:profile.php");
    }
}

function getPetData($id)
{
    $data = [];
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM pets WHERE pet_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            $data["id"] = $row["pet_id"];
            $data["user_id"] = $row["user_id"];
            $data["name"] = $row["pet_name"];
            $data["species"] = $row["species"];
            $data["gender"] = $row["gender"];
            $data["photo"] = $row["photo"];
            $data["birth_date"] = $row["birth_date"];
        }
        return $data;
    }
}

function getPetGender($gender = "")
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT DISTINCT gender FROM pets");
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            if ($row['gender'] == $gender) {
                echo '<option value="' . $row['gender'] . '" selected>' . $row['gender'] . '</option>';
            } else {
                echo '<option value="' . $row['gender'] . '">' . $row['gender'] . '</option>';
            }
        }
    }
}

if (isset($_POST['submit-modify-pet'])) {
    $error = false;
    $errormsg = "";
    if (!is_yourPet($_SESSION['user_id'], $_POST['pet-id'])) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Csak a saját állataid tudod szerkeszteni!";
        session_commit();
        // exit();
    } else {
        $stmt = mysqli_prepare($con, "SELECT * FROM pets WHERE pet_id = ?");
        mysqli_stmt_bind_param($stmt, 's', $_POST['pet-id']);
        mysqli_stmt_execute($stmt);
        $rows = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        if (mysqli_num_rows($rows) > 0) {
            while ($row = mysqli_fetch_array($rows)) {
                $img = $row["photo"];
            }
        }


        // var_dump($_FILES["image"]);
        if (!empty($_FILES["img"]["name"])) {
            $filename = $_FILES["img"]["name"];
            $tempname = $_FILES["img"]["tmp_name"];
            $folder = "image/" . $filename;
            move_uploaded_file($tempname, $folder);
            $img = $folder;
        }
        if ($_POST["birth_date"]) {
            $date_now = date("Y-m-d"); // this format is string comparable

            if ($date_now < $_POST["birth_date"]) {
                $errormsg .= "A kisállatodnak valós születési dátumot adj meg! ";
                $error = true;
            }
        }
        if ($error) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['response'] = $errormsg;
            session_commit();
        } else {

            $query = "UPDATE pets SET pet_name = ?, species = ?, gender = ?, photo = ?, birth_date = ? WHERE pet_id = ?";
            $pet = [
                $name = $_POST['name'],
                $specie = $_POST['species'],
                $gender = $_POST['gender'],
                $photo = $img,
                $birth_date = $_POST['birth_date'],
                $id = $_POST['pet-id']
            ];
            $pdo->prepare($query)->execute($pet);
            $pdo->beginTransaction();
            if ($pdo->commit() > 0) {
                if (!isset($_SESSION)) {
                    session_start();
                }
                $_SESSION['response'] = "Sikeres módosítás!";
                session_commit();
                // header("Location:profile.php");
            }
        }
    }

}

if (isset($_POST['submit-add-pet'])) {
    $error = false;
    $errormsg = "";
    // var_dump($_FILES["image"]);
    if (!empty($_FILES["img"]["name"])) {
        $filename = $_FILES["img"]["name"];
        $tempname = $_FILES["img"]["tmp_name"];
        $folder = "image/" . $filename;
        move_uploaded_file($tempname, $folder);
        $img = $folder;
    }
    $specievalue;
    if (isset($_POST['new-specie-input'])) {
        $specievalue = $_POST['new-specie-input'];
    } else {
        $specievalue = $_POST['species'];
    }

    if ($_POST["birth_date"]) {
        $date_now = date("Y-m-d"); // this format is string comparable

        if ($date_now < $_POST["birth_date"]) {
            $errormsg .= "A kisállatodnak valós születési dátumot adj meg! ";
            $error = true;
        }
    }
    if ($error) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = $errormsg;
        session_commit();
    } else {
        $query = "INSERT INTO pets (`user_id`,`pet_name`, `species`, `gender`, `photo`, `birth_date`) VALUES (?,?,?,?,?,?)";
        $pet = [
            $user_id = $_SESSION["user_id"],
            $name = $_POST['name'],
            $specie = $_POST['species'],
            $gender = $_POST['gender'],
            $photo = $img,
            $birth_date = $_POST['birth_date']
        ];
        $pdo->prepare($query)->execute($pet);
        $pdo->beginTransaction();
        if ($pdo->commit() > 0) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['response'] = "Sikeres állat hozzáadása!";
            session_commit();
            header("Location:profile.php");
        }
    }

}

if (isset($_POST['submit-modify'])) {
    $error = false;
    $errormsg = "";
    $stmt = mysqli_prepare($con, "SELECT * FROM user WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            $img = $row["picture"];
        }
    } else {
        if (!isset($_SESSION)) {
            session_start();
        }
        $errormsg .= "Nincs ilyen idvel rendelkező felhasználó! ";
        session_commit();
        $error = true;
    }

    if ($error) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = $errormsg;
        session_commit();
    } else {
        // var_dump($_FILES["image"]);
        if (!empty($_FILES["profile-img"]["name"])) {
            $filename = $_FILES["profile-img"]["name"];
            $tempname = $_FILES["profile-img"]["tmp_name"];
            $folder = "image/" . $filename;
            move_uploaded_file($tempname, $folder);
            $img = $folder;
        }

        $query = "UPDATE user SET firstname = ?, lastname = ?, address = ?, gender = ?, phone = ?, picture = ? WHERE user_id = ?";
        $pet = [
            $name = $_POST['firstname'],
            $specie = $_POST['lastname'],
            $_POST['address'],
            $gender = $_POST['gender'],
            $birth_date = $_POST['phone'],
            $photo = $img,


            $_SESSION["user_id"]
        ];
        $pdo->prepare($query)->execute($pet);
        $pdo->beginTransaction();
        if ($pdo->commit() > 0) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['response'] = "Sikeres módosítás!";
            session_commit();
            // header("Location:profile.php");
        }
    }
}

if (isset($_POST['new-veterinarian'])) {
    if (!is_veterinarian($_SESSION['user_id'])) {
        $query = "INSERT INTO veterinarians (`user_id`,`specialization`, `work_start`, `work_end`, `status`, `place`) VALUES (?,?,?,?,0,?)";
        $vet = [
            $user_id = $_SESSION["user_id"],
            $name = $_POST['specialization'],
            $specie = $_POST['start'],
            $gender = $_POST['end'],
            $birth_date = $_POST['place'],
        ];
        $pdo->prepare($query)->execute($vet);
        $pdo->beginTransaction();
        if ($pdo->commit() > 0) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['response'] = "Sikeres jelentkezés sétáltatónak. Várd meg, hogy leelenőrizzék az adataidat!";
            session_commit();
            header("Location:profile.php");
        }
    } else {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Már sétáltató vagy!";
        session_commit();
    }
}


if (isset($_POST['edit-veterinarian'])) {
    if (is_veterinarian($_SESSION['user_id'])) {
        $query = "UPDATE veterinarians SET `specialization` = ?, `work_start` = ?, `work_end`=?, `place` = ?,`description` = ? WHERE user_id = ?";
        $vet = [
            $specialization = $_POST['specialization'],
            $start = $_POST['start'],
            $end = $_POST['end'],
            $place = $_POST['place'],
            $description = $_POST['description'],
            $id = $_SESSION['user_id']
        ];
        $pdo->prepare($query)->execute($vet);
        $pdo->beginTransaction();
        if ($pdo->commit() > 0) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['response'] = "Sikeresen megváltoztattad az sétáltatói profilod!";
            session_commit();
            // header("Location:profile.php");
        }
    } else {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Nem vagy sétáltató!";
        session_commit();
    }
}


if (isset($_POST['pet-select'])) {
    if (is_yourPet($_SESSION['user_id'], $_POST['pet-id'])) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['selected-pet'] = $_POST['pet-id'];
        $_SESSION['response'] = "Sikeresen kiválasztottad a kisállatod, válassz neki egy sétáltatót!";
        session_commit();
    } else {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Ezt a kisállatot nemtudod kiválasztani!";
        session_commit();
    }

}
if (isset($_POST['dismiss-pet'])) {
    if (!isset($_SESSION)) {
        session_start();
    }
    unset($_SESSION['selected-pet']);
    unset($_SESSION['selected-veterinarian']);
    unset($_SESSION['selected-service']);
    unset($_SESSION['selected-date']);
    $_SESSION['response'] = "Sikeresen kitörölted a választásod!";
    session_commit();
}

if (isset($_POST['selected-service-btn'])) {
    $stmt = mysqli_prepare($con, "SELECT * FROM services WHERE service_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $_POST['service_id']);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            $vet_id = $row["veterinarian_id"];
        }
    }
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['selected-veterinarian'] = $vet_id;
    $_SESSION['selected-service'] = $_POST['service_id'];
    $_SESSION['response'] = "Sikeresen kiválasztottad a szolgáltatást és az sétáltatót!";
    session_commit();

}

if (isset($_POST['setCalendar'])) {
    if (isset($_POST['selected-date'])) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['selected-date'] = $_POST['selected-date'];
        session_commit();
    }
}

if (isset($_POST['choose-termin-btn'])) {
    $error = false;
    if (!isset($_SESSION)) {
        session_start();
    }
    if (!isset($_SESSION['selected-pet'])) {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Nincs kijelölt kisállat!";
        session_commit();
        $error = true;
    }
    if (!isset($_SESSION['selected-service'])) {
        if (!isset($_SESSION)) {
            session_start();

        }
        $_SESSION['response'] = "Nincs kijelölt szolgáltatás!";
        session_commit();
        $error = true;
    }
    if (!isset($_SESSION['selected-veterinarian'])) {
        if (!isset($_SESSION)) {
            session_start();

        }
        $_SESSION['response'] = "Nincs kijelölt sétáltató!";
        session_commit();
        $error = true;
    }
    if (!isset($_SESSION['selected-date'])) {
        if (!isset($_SESSION)) {
            session_start();

        }
        $_SESSION['response'] = "Nincs kijelölt dátum!";
        session_commit();
        $error = true;
    }
    if (!isset($_SESSION['selected-veterinarian'])) {
        if (!isset($_SESSION)) {
            session_start();

        }
        $_SESSION['response'] = "Nincs kijelölt sétáltató!";
        session_commit();
        $error = true;
    }
    $someDate = new \DateTime($_SESSION['selected-date']);
    $now = new \DateTime();
    if ($now->diff($someDate)->days >= 1) {
        // Kiválasztott dátum legalább 1 nappal a jelenlegi dátum után van
    } else {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Legalább 2 nappal előre kell foglalni!";
        session_commit();
        $error = true;
    }


    if (!$error) {
        $stmt = mysqli_prepare($con, "SELECT * FROM termins WHERE veterinarian_id = ? AND date_termin = ? AND hour_termin = ? ");
        mysqli_stmt_bind_param($stmt, 'sss', $_SESSION['selected-veterinarian'], $_SESSION['selected-date'], $_POST['hour-termin']);
        mysqli_stmt_execute($stmt);
        $rows = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        if (mysqli_num_rows($rows) > 0) {
            $row = mysqli_fetch_array($rows);
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['response'] = "Ez az időpont már foglalt!";
            session_commit();
        } else {
            $termin = [
                $pet_id = $_SESSION['selected-pet'],
                $veterinarian_id = $_SESSION['selected-veterinarian'],
                $service_id = $_SESSION['selected-service'],
                $date_termin = $_SESSION['selected-date'],
                $hour_termin = $_POST['hour-termin']
            ];
            // Insert query with POD params
            $query = "INSERT INTO termins (pet_id,veterinarian_id,service_id,date_termin,hour_termin) VALUES (?,?,?,?,?)";
            $pdo->prepare($query)->execute($termin);
            $pdo->beginTransaction();
            if ($pdo->commit() > 0) {
                if (!isset($_SESSION)) {
                    session_start();
                }

                $_SESSION['response'] = "Sikeres időpont foglalás!";
                unset($_SESSION['selected-pet']);
                unset($_SESSION['selected-veterinarian']);
                unset($_SESSION['selected-service']);
                unset($_SESSION['selected-date']);
                session_commit();
            }
        }
    }
}

function getTermins($id)
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM termins t 
    INNER JOIN pets p ON t.pet_id = p.pet_id
    INNER JOIN user u ON p.user_id = u.user_id
    WHERE u.user_id = ?");
    mysqli_stmt_bind_param($stmt, 's', $id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            $terminDateTime = new DateTime($row['date_termin'] . ' ' . $row['hour_termin'] . ':00');
            $oneHourAgo = (new DateTime())->modify('-1 hour');

            echo '<li class="item">
            <img class="rows-img" src="' . $row['photo'] . '">
            <div class="info">
                <span class="name">' . $row['pet_name'] . '</span>
                <span class="name">' . $row['date_termin'] . '</span>
                <span class="name">' . $row['hour_termin'] . 'h</span>
            </div>
            <div class="muveletek">
                <form method="post"> 
                    <input type="hidden" name="termin-id" value="' . $row['termin_id'] . '">
                    <div class="buttons1">
                    <button type="submit" name="termin-delete" class="btn btn-danger">Törlés</button>';

            if ($terminDateTime <= $oneHourAgo) {
                echo '<button type="button" class="btn btn-success"><a class="rating" href="rating.php?veterinarian_id=' . $row['veterinarian_id'] . '">Értékelés</a></button>';
            }

            echo '</form>       
            </div>
        </li>';
        }
    } else {
        echo '<li class="item">Nincs lefoglalt időpontod!</li>';
    }
}

function getTerminsAdmin($date)
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM termins t 
    INNER JOIN pets p ON t.pet_id = p.pet_id
    INNER JOIN user u ON p.user_id = u.user_id 
    WHERE t.date_termin = ? ");
    mysqli_stmt_bind_param($stmt, 's', $date);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            echo '<li class="item">
            <img class="rows-img" src="' . $row['photo'] . '">
            <div class="info">
                <span class="name">' . $row['pet_name'] . '</span>
                <span class="name">' . $row['date_termin'] . '</span>
                <span class="name">' . $row['hour_termin'] . 'h</span>
            </div>
            <div class="muveletek">
                <form method="post"> 
                    <input type="hidden" name="termin-id" value="' . $row['termin_id'] . '">
                    <button type="submit" name="termin-delete" class="btn btn-danger">Törlés</button>
                </form>       
            </div>
        </li>';
        }
    } else {
        echo '
        <li class="item">
            Ezen a dátumon nincs lefoglalt időpont!
        </li>
        ';
    }
}

function getTerminsRecord($date)
{
    include('db_config.php');
    if (!isset($_SESSION)) {
        session_start();
    }
    $stmt = mysqli_prepare($con, "SELECT * FROM termins t 
    INNER JOIN pets p ON t.pet_id = p.pet_id
    INNER JOIN user u ON p.user_id = u.user_id 
    INNER JOIN veterinarians v ON t.veterinarian_id = v.veterinarian_id 
    
    WHERE t.date_termin <= ? AND v.user_id = ?");
    // var_dump($_SESSION['user_id']);
    mysqli_stmt_bind_param($stmt, 'ss', $date, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        while ($row = mysqli_fetch_array($rows)) {
            $terminDateTime = new DateTime($row['date_termin'] . ' ' . $row['hour_termin'] . ':00');
            $now = new DateTime();

            echo '<li class="item">
                <img class="rows-img" src="' . $row['photo'] . '">
                <div class="info">
                    <span class="name">' . $row['pet_name'] . '</span>
                    <span class="name">' . $row['date_termin'] . '</span>
                    <span class="name">' . $row['hour_termin'] . '</span>
                    <span class="name">' . $row['address'] . '</span>
                </div>
                <div class="muveletek">
                    <form method="post"> 
                        <input type="hidden" name="termin-id" value="' . $row['termin_id'] . '">
                        <input type="hidden" name="user-id" value="' . $row['user_id'] . '">
                        <input type="hidden" name="veterinarian-id" value="' . $row['veterinarian_id'] . '">';

            if ($terminDateTime <= $now) {
                echo '<button type="submit" name="termin-missed" class="btn btn-danger">Meg lett sétáltatva!</button>';
            }

            echo '</form>       
                </div>
            </li>';
        }
    } else {
        echo '
        <li class="item">
            A mai nap('.$date.') nincs lefoglalt, vagy már lejárt időpont!
        </li>
        ';
    }
}

function is_yourTermin($user_id, $termin_id)
{
    include('db_config.php');
    $stmt = mysqli_prepare($con, "SELECT * FROM termins t 
    INNER JOIN pets p ON t.pet_id = p.pet_id
    INNER JOIN user u ON p.user_id = u.user_id
    WHERE u.user_id = ? AND t.termin_id = ?");
    mysqli_stmt_bind_param($stmt, 'ss', $user_id, $termin_id);
    mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
    if (mysqli_num_rows($rows) > 0) {
        return true;
    } else
        false;

}

if (isset($_POST['termin-delete'])) {
    if (is_yourTermin($_SESSION['user_id'], $_POST['termin-id'])) {

        $stmt = mysqli_prepare($con, "SELECT * FROM termins
     WHERE termin_id = ?");
        mysqli_stmt_bind_param($stmt, 's', $_POST['termin-id']);
        mysqli_stmt_execute($stmt);
        $rows = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        if (mysqli_num_rows($rows) > 0) {
            while ($row = mysqli_fetch_array($rows)) {
                $date = $row['date_termin'] . " " . $row['hour_termin'] . ':00';
            }
        }
        $terminDateTime = new DateTime($date);
        $now = new DateTime();

        $diff = $terminDateTime->diff($now);
        $hours = $diff->h;
        $hours = $hours + ($diff->days * 24);

        if ($hours < 4 && $terminDateTime > $now) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['response'] = "Sajnos most már nem tudod kitörölni!";
            session_commit();
        } else {
            $delete = [
                $pet_id = $_POST['termin-id']
            ];

            $query1 = "DELETE FROM termins WHERE termin_id = ?";
            $pdo->prepare($query1)->execute($delete);
            $pdo->beginTransaction();
            $pdo->commit();

            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['response'] = "Sikeresen kitörölted a foglalást!";
            session_commit();
        }
    }
}

if (isset($_POST['termin-missed'])) {
    $delete = [
        $_POST['termin-id']
    ];
    $query2 = "DELETE FROM TERMINS where termin_id = ?";
    $pdo->prepare($query2)->execute($delete);
    $pdo->beginTransaction();
    $pdo->commit();
    if (!isset($_SESSION)) {
        session_start();
    }
    session_commit();
    // header("Location:" . $_SERVER['HTTP_REFERER']);
} else {
    if (!isset($_SESSION)) {
        session_start();
    }

}

if (isset($_POST['add-service'])) {
    if (!empty($_POST['service-description']) && !empty($_POST['service-price'])) {
        $serv = [
            $_POST['veterinarian-id'],
            $_POST['service-description'],
            $_POST['service-price']
        ];
        $query = "INSERT INTO services (veterinarian_id,service_description,service_price) VALUES (?,?,?)";
        $pdo->prepare($query)->execute($serv);
        $pdo->beginTransaction();
        if ($pdo->commit() > 0) {
            if (!isset($_SESSION)) {
                session_start();
            }
            $_SESSION['response'] = "Sikeres szolgáltatás hozzáadása!";
            session_commit();
            // header("Location:" . $_SERVER['HTTP_REFERER']);
        }
    } else {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Hibásan kitöltött mezők!";
        session_commit();
    }

}

function getServices($id)
{
    include('db_config.php');

    $stmt2 = mysqli_prepare($con, "SELECT * FROM services WHERE veterinarian_id = ?");
    mysqli_stmt_bind_param($stmt2, 's', $id);
    mysqli_stmt_execute($stmt2);
    $rows2 = mysqli_stmt_get_result($stmt2);
    mysqli_stmt_close($stmt2);

    if (mysqli_num_rows($rows2) > 0) {
        while ($row2 = mysqli_fetch_array($rows2)) {
            echo ' <div class="szolgaltatas">
                         <div class="ertekek">
                             <span>Leírás: ' . $row2['service_description'] . '</span><br>
                             <span><b>Ár: ' . $row2['service_price'] . 'din/óra</b></span>
                         </div>
                         <form class="muveletek" method="post">
                         <input type="hidden" name="service-id" value="' . $row2['service_id'] . '">
                             <button type="submit" name="delete-service" class="btn btn-danger">Törlés</button>
                         </form>
                     </div>';
        }
    }
}

if (isset($_POST['delete-service'])) {
    $delete_pet = [
        $pet_id = $_POST['service-id']
    ];

    //cardboard
    $query1 = "DELETE FROM services WHERE service_id = ?";
    $pdo->prepare($query1)->execute($delete_pet);
    $pdo->beginTransaction();
    if($pdo->commit() > 0){
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['response'] = "Sikeresen kitörölted a szolgáltatást!";
        session_commit();
    }
}

if (isset($_POST['submit-review'])) {
    include('db_config.php');

    $veterinarian_id = $_POST['veterinarian_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Ellenőrzések és validációk (pl. $rating 1 és 5 között van-e)
    if ($rating < 1 || $rating > 5) {
        echo "Az értékelés csak 1 és 5 közötti lehet.";
    } else {
        // INSERT parancs az értékelés rögzítéséhez a reviews táblába
        $insert_query = "INSERT INTO reviews (user_id, veterinarian_id, rating, comment)
                        VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $insert_query);
        $user_id = $_SESSION['user_id']; // Felhasználó azonosítója, ha be vannak jelentkezve
        mysqli_stmt_bind_param($stmt, 'iiis', $user_id, $veterinarian_id, $rating, $comment);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['response'] = "Sikeres értékelés";
        } else {
            $_SESSION['response'] = "Hiba történt az értékelés rögzítésekor: " . mysqli_error($con);
        }

        mysqli_stmt_close($stmt);
    }
}

