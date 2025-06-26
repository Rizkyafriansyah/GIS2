<?php
include '../koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $conn->real_escape_string($_POST['nama']);
    $latitude = $conn->real_escape_string($_POST['latitude']);
    $longitude = $conn->real_escape_string($_POST['longitude']);
    $status = $conn->real_escape_string($_POST['status']);

    $sql = "INSERT INTO hydrant (nama, latitude, longitude, status) VALUES ('$nama', '$latitude', '$longitude', '$status')";
    if ($conn->query($sql) === TRUE) {
        echo "Hydrant berhasil ditambahkan";
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Metode request tidak valid";
}
?>
