<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $task_id = $_GET['id'];

    // Hapus semua subtasks terkait tugas ini
    $conn->query("DELETE FROM subtasks WHERE task_id='$task_id'");

    // Hapus tugas utama
    $sql = "DELETE FROM tasks WHERE id='$task_id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "ID tugas tidak ditemukan!";
}
?>
