<?php
session_start();
include 'config.php';

// Cek apakah user sudah login 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Menambah tugas ke dalam database
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $nama = $_POST['nama'];
    $prioritas = $_POST['prioritas'];
    $tanggal = $_POST['tanggal'];

    if (!empty($nama) && !empty($prioritas) && !empty($tanggal)) {
        $sql = "INSERT INTO tasks (nama, prioritas, tanggal, user_id, status) 
                VALUES ('$nama', '$prioritas', '$tanggal', '$user_id', 'Belum Selesai')";
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Semua field harus diisi!";
    }
}

// Update status tugas
if (isset($_GET['toggle_status'])) {
    $task_id = $_GET['toggle_status'];
    $current_status = $_GET['current_status'];
    $new_status = ($current_status == "Selesai") ? "Belum Selesai" : "Selesai";
    $conn->query("UPDATE tasks SET status='$new_status' WHERE id='$task_id'");
    header("Location: index.php");
    exit();
}

// Menambah subtasks ke dalam database
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_subtask'])) {
    $task_id = $_POST['task_id'];
    $subtask_nama = $_POST['subtask_nama'];

    if (!empty($task_id) && !empty($subtask_nama)) {
        $sql = "INSERT INTO subtasks (task_id, nama, status) 
                VALUES ('$task_id', '$subtask_nama', 'Belum Selesai')";
        if ($conn->query($sql) === TRUE) {
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Subtask tidak boleh kosong!";
    }
}

// Pencarian dan filter tugas
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$query = "SELECT * FROM tasks WHERE user_id='$user_id' ORDER BY id DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Flatpickr CSS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="cssindexx.css">
    <script>
        function confirmDelete(id) {
            return confirm("Apakah Anda yakin ingin menghapus item ini?");
        }
        function checkDeadlines() {
            let today = new Date().toISOString().split('T')[0];
            document.querySelectorAll('.task-item').forEach(task => {
                let deadline = task.getAttribute('data-deadline');
                if (deadline < today) {
                    task.style.color = 'red';
                    alert("Tugas '" + task.getAttribute('data-name') + "' sudah melewati deadline!");
                }
            });
        }
        window.onload = checkDeadlines;
    </script>
</head>
<body>
<div class="container">
    <h2>To-Do List</h2>
    <p>Halo, <?= $_SESSION['user_id']; ?>! <a href="logout.php">Logout</a></p>
    
    <!-- Form Pencarian -->
    <form method="GET">
        <input type="text" name="search" placeholder="Cari Tugas" value="<?= $search; ?>">
        <select name="status">
            <option value="">Semua Status</option>
            <option value="Belum Selesai" <?= $status_filter == "Belum Selesai" ? "selected" : ""; ?>>Belum Selesai</option>
            <option value="Selesai" <?= $status_filter == "Selesai" ? "selected" : ""; ?>>Selesai</option>
        </select>
        <button type="submit">Cari</button>
    </form>

    <!-- Form Tambah Tugas -->
    <form method="POST">
        <input type="text" name="nama" placeholder="Nama Tugas" required>
        <select name="prioritas">
            <option value="Tinggi">Tinggi</option>
            <option value="Sedang">Sedang</option>
            <option value="Rendah">Rendah</option>
        </select>
        <input type="date" name="tanggal" required>
        <button type="submit" name="add_task">Tambah Tugas</button>
    </form>

    <h3>Daftar Tugas</h3>
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li class="task-item" data-deadline="<?= $row['tanggal']; ?>" data-name="<?= $row['nama']; ?>">
                <strong><?= $row['nama']; ?></strong> (<?= $row['prioritas']; ?>, <?= $row['tanggal']; ?>, <?= $row['status']; ?>)
                <a href="?toggle_status=<?= $row['id']; ?>&current_status=<?= $row['status']; ?>">
                    <?= $row['status'] == "Selesai" ? "Tidak Selesai" : "Selesai"; ?>
                </a> |
                <a href="delete.php?id=<?= $row['id']; ?>" class="btn-delete" onclick="return confirmDelete(<?= $row['id']; ?>)">Hapus</a>
                |
                <a href="edit.php?id=<?= $row['id']; ?>" class="btn-delete" onclick="return confirmUpdate(<?= $row['id']; ?>)">Edit</a>
                <!-- Query untuk subtasks -->
                <?php
                $task_id = $row['id'];
                $subtask_query = "SELECT * FROM subtasks WHERE task_id='$task_id'";
                $subtask_result = $conn->query($subtask_query);
                ?>
                <ul>
                    <?php while ($subtask = $subtask_result->fetch_assoc()): ?>
                        <li>
                            <?= $subtask['nama']; ?> - <?= $subtask['status']; ?>
                            <a href="toggle_subtask.php?id=<?= $subtask['id']; ?>">Selesai</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
                
                <!-- Form tambah subtasks -->
                <form method="POST">
                    <input type="hidden" name="task_id" value="<?= $row['id']; ?>">
                    <input type="text" name="subtask_nama" placeholder="Tambah Subtask" required>
                    <button type="submit" name="add_subtask">Tambah</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>
</div>
<!-- Flatpickr JS -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("input[name='tanggal']", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "l, d F Y",
            minDate: "today",
            theme: "dark",
            locale: "id", // Bahasa Indonesia
            onReady: function(selectedDates, dateStr, instance) {
                instance.altInput.setAttribute("placeholder", "Atur Deadline");
            }
        });
    });
</script>





</body>
</html>