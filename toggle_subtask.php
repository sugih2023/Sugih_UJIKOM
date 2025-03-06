<?php
session_start();
include 'config.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Pastikan parameter id tersedia
if (isset($_GET['id'])) {
    $subtask_id = $_GET['id'];

    // Ambil status saat ini dari database
    $query = "SELECT status FROM subtasks WHERE id='$subtask_id'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $subtask = $result->fetch_assoc();
        $new_status = ($subtask['status'] == "Selesai") ? "Belum Selesai" : "Selesai";

        // Update status subtask
        $update_query = "UPDATE subtasks SET status='$new_status' WHERE id='$subtask_id'";
        if ($conn->query($update_query) === TRUE) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Subtask tidak ditemukan.";
    }
} else {
    echo "ID Subtask tidak valid.";
}
?>
