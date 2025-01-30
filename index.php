<?php
$koneksi = mysqli_connect('localhost', 'root', '', 'aplikasi_todolist');

// Tambah Task
if (isset($_POST['add_task'])) {
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    if (!empty($task) && !empty($priority) && !empty($due_date)) {
        $query = "INSERT INTO tasks (task, priority, due_date, status) VALUES ('$task', '$priority', '$due_date', '0')";
        mysqli_query($koneksi, $query);
        echo "<script>alert('Data Berhasil Disimpan!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Semua Kolom Harus Diisi!'); window.location='index.php';</script>";
    }
}

// Menandai task Selesai
if (isset($_GET['complete'])) {
    $id = $_GET['complete'];
    mysqli_query($koneksi, "UPDATE tasks SET status=1 WHERE id=$id");
    echo "<script>alert('Data Berhasil diperbaharui'); window.location='index.php';</script>";
}

// Menghapus task
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($koneksi, "DELETE FROM tasks WHERE id=$id");
    echo "<script>alert('Data Berhasil Dihapus'); window.location='index.php';</script>";
}

// Mengedit task
if (isset($_POST['edit_task'])) {
    $id = $_POST['task_id'];
    $task = $_POST['task'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    
    $query = "UPDATE tasks SET task='$task', priority='$priority', due_date='$due_date' WHERE id=$id";
    mysqli_query($koneksi, $query);
    echo "<script>alert('Data Berhasil Diperbarui!'); window.location='index.php';</script>";
}

$result = mysqli_query($koneksi, "SELECT * FROM tasks ORDER BY status ASC, priority DESC, due_date ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi To-Do-List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-2">
        <h2 class="text-center">Aplikasi To-Do-List</h2>

        <?php
        if (isset($_GET['edit'])) {
            $id = $_GET['edit'];
            $edit_result = mysqli_query($koneksi, "SELECT * FROM tasks WHERE id=$id");
            $edit_data = mysqli_fetch_assoc($edit_result);
        ?>
        <form method="POST" class="border rounded bg-light p-2">
            <input type="hidden" name="task_id" value="<?php echo $edit_data['id']; ?>">
            <label class="form-label">Nama Tugas</label>
            <input type="text" name="task" class="form-control" value="<?php echo $edit_data['task']; ?>" required>
            <label class="form-label">Prioritas</label>
            <select name="priority" class="form-control" required>
                <option value="1" <?php if ($edit_data['priority'] == 1) echo "selected"; ?>>Low</option>
                <option value="2" <?php if ($edit_data['priority'] == 2) echo "selected"; ?>>Medium</option>
                <option value="3" <?php if ($edit_data['priority'] == 3) echo "selected"; ?>>High</option>
            </select>
            <label class="form-label">Tanggal & Waktu</label>
            <input type="datetime-local" name="due_date" class="form-control" value="<?php echo date('Y-m-d\TH:i', strtotime($edit_data['due_date'])); ?>" required>
            <button class="btn btn-warning w-100 mt-2" name="edit_task">Perbarui</button>
        </form>
        <?php } else { ?>
        <form method="POST" class="border rounded bg-light p-2">
            <label class="form-label">Nama Tugas</label>
            <input type="text" name="task" class="form-control" placeholder="Masukkan Tugas Baru" required>
            <label class="form-label">Prioritas</label>
            <select name="priority" class="form-control" required>
                <option value="">---Pilih Prioritas---</option>
                <option value="1">Low</option>
                <option value="2">Medium</option>
                <option value="3">High</option>
            </select>
            <label class="form-label">Tanggal & Waktu</label>
            <input type="datetime-local" name="due_date" class="form-control" required>
            <button class="btn btn-primary w-100 mt-2" name="add_task">Tambah</button>
        </form>
        <?php } ?>

        <hr>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Task</th>
                    <th>Prioritas</th>
                    <th>Tanggal & Waktu</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($result) > 0) {
                    $no = 1;
                    while($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo $row['task']; ?></td>
                        <td><?php echo ($row['priority'] == 1) ? "Low" : (($row['priority'] == 2) ? "Medium" : "High"); ?></td>
                        <td><?php echo $row['due_date']; ?></td>
                        <td><?php echo ($row['status'] == 0) ? "Belum Selesai" : "Selesai"; ?></td>
                        <td>
                            <?php if ($row['status'] == 0) { ?>
                            <a href="?complete=<?php echo $row['id'] ?>" class="btn btn-success">Selesai</a>
                            <?php } ?>
                            <a href="?edit=<?php echo $row['id'] ?>" class="btn btn-warning">Edit</a>
                            <a href="?delete=<?php echo $row['id'] ?>" class="btn btn-danger">Hapus</a>
                        </td>
                    </tr>
                <?php }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Tidak ada Data</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
