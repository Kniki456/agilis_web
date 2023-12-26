<?php
$con = mysqli_connect("localhost", "root", "", "agilis");

$sql = "SELECT feladat FROM elsoszint ORDER BY RAND() LIMIT 1";
$result = mysqli_query($con, $sql);

$data = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $data[] = $row;
}

if(!empty($data)) {
    echo json_encode($data);
} else {
    echo json_encode("No data found.");
}
?>
