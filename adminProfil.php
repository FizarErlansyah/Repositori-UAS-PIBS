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
        .form-crud input[type="text"],
        .form-crud input[type="file"] {
            width: 220px;
            padding: 5px;
            border: 1px solid #bbb;
            border-radius: 3px;
        }
        .form-crud input[type="file"] {
            padding: 3px;
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
        .info-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .info-box a {
            color: #0066cc;
            text-decoration: underline;
        }
        .btn-download {
            display: inline-block;
            padding: 6px 12px;
            background: #28a745;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            margin-top: 8px;
        }
        .btn-download:hover {
            background: #218838;
            color: white;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <h2 style="margin-top:0;color:#333;">CRUD Profil Mahasiswa</h2>
    <div class="info-box">
        <strong>📋 Info JSON:</strong> Upload file JSON untuk data lengkap profil (Education, Experience, Skills, dll).<br>
        <a href="Data/template.json" download class="btn-download">⬇ Download Template JSON</a>
    </div>
<?php
include "koneksi.php";

if (isset($_POST['simpan'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    $foto = 'foto.jpg'; // default
    
    // Handle upload foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "";
        $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $new_filename = "foto-" . $nim . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto = $new_filename;
        }
    }
    
    // Handle upload JSON
    if (isset($_FILES['json_file']) && $_FILES['json_file']['error'] == 0) {
        $json_dir = "Data/";
        if (!file_exists($json_dir)) {
            mkdir($json_dir, 0777, true);
        }
        $json_filename = $nim . ".json";
        $json_file = $json_dir . $json_filename;
        
        // Validasi file JSON
        $json_content = file_get_contents($_FILES['json_file']['tmp_name']);
        $json_decoded = json_decode($json_content, true);
        
        if ($json_decoded !== null) {
            move_uploaded_file($_FILES['json_file']['tmp_name'], $json_file);
        } else {
            echo "<script>alert('File JSON tidak valid!');</script>";
        }
    }
    
    mysqli_query($conn, "INSERT INTO profil (nim, nama, prodi, foto) VALUES ('$nim','$nama','$prodi','$foto')");
}

if (isset($_GET['hapus'])) {
    $nim = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM profil WHERE nim='$nim'");
}

if (isset($_POST['ubah'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    
    // Handle upload foto
    $foto_update = "";
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "";
        $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $new_filename = "foto-" . $nim . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto_update = ", foto='$new_filename'";
        }
    }
    
    // Handle upload JSON
    if (isset($_FILES['json_file']) && $_FILES['json_file']['error'] == 0) {
        $json_dir = "Data/";
        if (!file_exists($json_dir)) {
            mkdir($json_dir, 0777, true);
        }
        $json_filename = $nim . ".json";
        $json_file = $json_dir . $json_filename;
        
        // Validasi file JSON
        $json_content = file_get_contents($_FILES['json_file']['tmp_name']);
        $json_decoded = json_decode($json_content, true);
        
        if ($json_decoded !== null) {
            move_uploaded_file($_FILES['json_file']['tmp_name'], $json_file);
        } else {
            echo "<script>alert('File JSON tidak valid!');</script>";
        }
    }
    
    mysqli_query($conn, "UPDATE profil SET nama='$nama', prodi='$prodi'$foto_update WHERE nim='$nim'");
}

$result = mysqli_query($conn, "SELECT * FROM profil");

$edit_nim = "";
$edit_nama = "";
$edit_prodi = "";
$edit_foto = "";
$edit_json_exists = false;
if (isset($_GET['edit'])) {
    $edit_nim = $_GET['edit'];
    $data_edit = mysqli_query($conn, "SELECT * FROM profil WHERE nim='$edit_nim'");
    $row_edit = mysqli_fetch_assoc($data_edit);
    $edit_nama = $row_edit['nama'];
    $edit_prodi = $row_edit['prodi'];
    $edit_foto = isset($row_edit['foto']) ? $row_edit['foto'] : '';
    
    // Cek apakah file JSON sudah ada
    $json_file_path = "Data/" . $edit_nim . ".json";
    $edit_json_exists = file_exists($json_file_path);
}
?>
    <div class="form-crud">
        <form method="post" enctype="multipart/form-data">
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
            <div class="form-row">
                <label>Foto :</label>
                <input type="file" name="foto" accept="image/*">
                <?php if ($edit_foto && $edit_foto != 'foto.jpg') { ?>
                    <small style="display:block;margin-left:96px;color:#666;">Foto saat ini: <?= $edit_foto ?></small>
                <?php } ?>
            </div>
            <div class="form-row">
                <label>Data JSON :</label>
                <input type="file" name="json_file" accept=".json">
                <?php if ($edit_json_exists) { ?>
                    <small style="display:block;margin-left:96px;color:#666;">JSON tersedia: Data/<?= $edit_nim ?>.json</small>
                    <small style="display:block;margin-left:96px;color:#999;font-size:11px;">Upload file baru untuk mengganti</small>
                <?php } else { ?>
                    <small style="display:block;margin-left:96px;color:#999;font-size:11px;">Upload file JSON untuk data lengkap profil</small>
                <?php } ?>
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
            <th>Foto</th>
            <th>JSON</th>
            <th>Kelola</th>
        </tr>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $foto_display = isset($row['foto']) && $row['foto'] != 'foto.jpg' ? $row['foto'] : '-';
            $json_file = "Data/" . $row['nim'] . ".json";
            $json_exists = file_exists($json_file);
            $json_status = $json_exists ? '✓' : '✗';
            $json_color = $json_exists ? 'green' : 'red';
            
            echo "<tr>
                    <td>$no</td>
                    <td>{$row['nim']}</td>
                    <td>{$row['nama']}</td>
                    <td>{$row['prodi']}</td>
                    <td style='font-size:12px;'>$foto_display</td>
                    <td style='font-size:16px;color:$json_color;font-weight:bold;text-align:center;'>";
            
            if ($json_exists) {
                echo "$json_status <a href='$json_file' download style='font-size:11px;color:#0066cc;'>[Download]</a>";
            } else {
                echo $json_status;
            }
            
            echo "</td>
                    <td>
                        <a href='?edit={$row['nim']}'>UBAH</a> |
                        <a href='?hapus={$row['nim']}' onclick=\"return confirm('Hapus data ini?')\">HAPUS</a>";
            
            if ($json_exists) {
                echo " | <a href='index.php?nim={$row['nim']}' target='_blank' style='color:#28a745;'>VIEW</a>";
            }
            
            echo "</td>
                </tr>";
            $no++;
        }
        ?>
    </table>
</div>
</body>
</html>