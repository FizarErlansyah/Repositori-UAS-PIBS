<!DOCTYPE html>
<html>
<head>
    <title>CRUD Profil Mahasiswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fafcff;
        }
        .wrapper {
            width: 470px;
            margin: 40px auto 0 auto;
            padding: 18px 22px 28px 22px;
            background: #fff;
            box-shadow: 0 4px 22px rgba(0,0,0,0.09);
            border-radius: 8px;
        }
        .form-crud {
            padding: 12px 14px;
            border: 1px solid #dbdbdb;
            background: #f8fbfc;
            margin-bottom: 24px;
            border-radius: 6px;
        }
        .form-row {
            margin-bottom: 11px;
        }
        .form-crud label {
            display: inline-block;
            width: 96px;
            font-weight: bold;
        }
        .form-crud input[type="text"] {
            width: 220px;
            padding: 5px;
            border: 1px solid #bbb;
            border-radius: 3px;
        }
        .form-crud input[type="submit"] {
            margin-top: 8px;
            padding: 7px 22px;
            background: #e3e1fa;
            border: 1px solid #9a93d1;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 6px;
            font-size: 16px;
        }
        table th, table td {
            border: 1px solid #b7b7b7;
            padding: 9px 10px;
            text-align: left;
        }
        table th {
            background: #eff0fa;
        }
        a {
            color: #800080;
            text-decoration: none;
            margin: 0 4px;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="wrapper">
<?php
include "koneksi.php";

if (isset($_POST['simpan'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    mysqli_query($conn, "INSERT INTO profil VALUES ('$nim','$nama','$prodi')");
}

if (isset($_GET['hapus'])) {
    $nim = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM profil WHERE nim='$nim'");
}

if (isset($_POST['ubah'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    mysqli_query($conn, "UPDATE profil SET nama='$nama', prodi='$prodi' WHERE nim='$nim'");
}

$result = mysqli_query($conn, "SELECT * FROM profil");

$edit_nim = "";
$edit_nama = "";
$edit_prodi = "";
if (isset($_GET['edit'])) {
    $edit_nim = $_GET['edit'];
    $data_edit = mysqli_query($conn, "SELECT * FROM profil WHERE nim='$edit_nim'");
    $row_edit = mysqli_fetch_assoc($data_edit);
    $edit_nama = $row_edit['nama'];
    $edit_prodi = $row_edit['prodi'];
}
?>
    <div class="form-crud">
        <form method="post">
            <div class="form-row">
                <label>NIM :</label>
                <input type="text" name="nim" value="<?= $edit_nim ?>" <?= $edit_nim ? 'readonly' : '' ?> required>
            </div>
            <div class="form-row">
                <label>Nama :</label>
                <input type="text" name="nama" value="<?= $edit_nama ?>" required>
                </div>
            <div class="form-row">
                <label>Kode Prodi :</label>
                <input type="text" name="prodi" value="<?= $edit_prodi ?>" required>
            </div>
            <input type="submit" name="<?= $edit_nim ? 'ubah' : 'simpan' ?>" value="<?= $edit_nim ? 'UBAH' : 'SIMPAN' ?>">
            <?php if ($edit_nim) { ?>
                <a href="adminProfil.php" style="margin-left:16px;padding:7px 18px;background:#ffebee;border:1px solid #e57373;border-radius:4px;color:#d32f2f;text-decoration:none;font-weight:bold;">BATAL</a>
            <?php } ?>
        </form>
    </div>
    <table>
        <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama</th>
            <th>Prodi</th>
            <th>Kelola</th>
        </tr>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>$no</td>
                    <td>{$row['nim']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['prodi']}</td>
                    <td>
                        <a href='?edit={$row['nim']}'>UBAH</a> |
                        <a href='?hapus={$row['nim']}' onclick=\"return confirm('Hapus data ini?')\">HAPUS</a>
                    </td>
                </tr>";
            $no++;
        }
        ?>
    </table>
</div>
</body>
</html>