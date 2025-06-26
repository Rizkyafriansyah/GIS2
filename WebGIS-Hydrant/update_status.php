<?php
$conn = new mysqli("localhost", "root", "", "hydrant_db");
$id = $_POST['id'];
$status = $_POST['status'];

$conn->query("UPDATE hydrant SET status='$status' WHERE id=$id");
echo "Status updated";
?>
