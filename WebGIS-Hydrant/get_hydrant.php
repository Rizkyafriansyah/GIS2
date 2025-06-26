<?php
$conn = new mysqli("localhost", "root", "", "hydrant_db");
$result = $conn->query("SELECT * FROM hydrant");
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>
