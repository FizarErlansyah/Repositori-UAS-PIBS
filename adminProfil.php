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
            width: 900px;
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
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .form-section {
            padding: 10px;
        }
        .form-section h3 {
            margin-top: 0;
            color: #555;
            font-size: 14px;
            border-bottom: 2px solid #9a93d1;
            padding-bottom: 5px;
        }
        .form-row {
            margin-bottom: 11px;
        }
        .form-crud label {
            display: block;
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 13px;
        }
        .form-crud input[type="text"],
        .form-crud input[type="file"],
        .form-crud input[type="url"],
        .form-crud textarea {
            width: 100%;
            padding: 6px;
            border: 1px solid #bbb;
            border-radius: 3px;
            box-sizing: border-box;
            font-size: 13px;
        }
        .form-crud textarea {
            min-height: 60px;
            resize: vertical;
            font-family: Arial, sans-serif;
        }
        .form-crud input[type="file"] {
            padding: 3px;
        }
        .form-crud input[type="submit"],
        .form-crud button[type="button"] {
            margin-top: 8px;
            padding: 8px 22px;
            background: #e3e1fa;
            border: 1px solid #9a93d1;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
        }
        .form-buttons {
            grid-column: 1 / -1;
            text-align: center;
        }
        .add-more-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 5px;
        }
        .add-more-btn:hover {
            background: #218838;
        }
        .remove-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 3px 8px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 11px;
            margin-left: 5px;
        }
        .dynamic-item {
            margin-bottom: 8px;
            padding: 8px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        .skill-item {
            display: grid;
            grid-template-columns: 150px 1fr 40px;
            gap: 8px;
            align-items: center;
        }
        .hobby-item {
            display: grid;
            grid-template-columns: 120px 1fr 40px;
            gap: 8px;
            align-items: center;
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

// ============================================
// CREATE - Tambah Data Mahasiswa Baru
// ============================================
if (isset($_POST['simpan'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    $foto = 'foto.jpg';
    
    // Upload foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "";
        $file_extension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $new_filename = "foto-" . $nim . "." . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            $foto = $new_filename;
        }
    }
    
    // Build JSON dari form fields
    $json_data = [
        'biodata' => [
            'nim' => $nim,
            'nama' => $nama,
            'tempat_lahir' => $_POST['tempat_lahir'] ?? '',
            'tanggal_lahir' => $_POST['tanggal_lahir'] ?? '',
            'alamat' => $_POST['alamat'] ?? ''
        ],
        'education' => [],
        'experience' => [],
        'skills' => [],
        'hobbies' => [],
        'publication' => $_POST['publication'] ?? '',
        'social_links' => [
            'instagram' => $_POST['instagram'] ?? '',
            'whatsapp' => $_POST['whatsapp'] ?? '',
            'youtube' => $_POST['youtube'] ?? '',
            'linkedin' => $_POST['linkedin'] ?? ''
        ]
    ];
    
    // Education
    if (isset($_POST['edu_tahun'])) {
        foreach ($_POST['edu_tahun'] as $i => $tahun) {
            if (!empty($tahun)) {
                $json_data['education'][] = [
                    'tahun' => $tahun,
                    'institusi' => $_POST['edu_institusi'][$i] ?? '',
                    'deskripsi' => $_POST['edu_deskripsi'][$i] ?? ''
                ];
            }
        }
    }
    
    // Experience
    if (isset($_POST['experience'])) {
        foreach ($_POST['experience'] as $exp) {
            if (!empty($exp)) {
                $json_data['experience'][] = $exp;
            }
        }
    }
    
    // Skills
    if (isset($_POST['skill_kategori'])) {
        foreach ($_POST['skill_kategori'] as $i => $kategori) {
            if (!empty($kategori)) {
                $json_data['skills'][$kategori] = $_POST['skill_value'][$i] ?? '';
            }
        }
    }
    
    // Hobbies
    if (isset($_POST['hobby_icon'])) {
        foreach ($_POST['hobby_icon'] as $i => $icon) {
            if (!empty($icon)) {
                $json_data['hobbies'][] = [
                    'icon' => $icon,
                    'name' => $_POST['hobby_name'][$i] ?? ''
                ];
            }
        }
    }
    
    // Simpan JSON
    $json_dir = "Data/";
    if (!file_exists($json_dir)) {
        mkdir($json_dir, 0777, true);
    }
    file_put_contents($json_dir . $nim . ".json", json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Insert ke database
    mysqli_query($conn, "INSERT INTO profil (nim, nama, prodi, foto) VALUES ('$nim','$nama','$prodi','$foto')");
    header("Location: adminProfil.php");
    exit;
}

// ============================================
// READ - Tampilkan Data Mahasiswa (Edit Mode)
// ============================================
$edit_nim = "";
$edit_nama = "";
$edit_prodi = "";
$edit_foto = "";
$edit_json_data = [];
if (isset($_GET['edit'])) {
    $edit_nim = $_GET['edit'];
    $data_edit = mysqli_query($conn, "SELECT * FROM profil WHERE nim='$edit_nim'");
    $row_edit = mysqli_fetch_assoc($data_edit);
    $edit_nama = $row_edit['nama'];
    $edit_prodi = $row_edit['prodi'];
    $edit_foto = isset($row_edit['foto']) ? $row_edit['foto'] : '';
    
    // Baca JSON
    $json_file_path = "Data/" . $edit_nim . ".json";
    if (file_exists($json_file_path)) {
        $edit_json_data = json_decode(file_get_contents($json_file_path), true);
    }
}

// ============================================
// UPDATE - Ubah Data Mahasiswa
// ============================================
if (isset($_POST['ubah'])) {
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $prodi = $_POST['prodi'];
    
    // Upload foto baru (opsional)
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
    
    // Build JSON dari form fields
    $json_data = [
        'biodata' => [
            'nim' => $nim,
            'nama' => $nama,
            'tempat_lahir' => $_POST['tempat_lahir'] ?? '',
            'tanggal_lahir' => $_POST['tanggal_lahir'] ?? '',
            'alamat' => $_POST['alamat'] ?? ''
        ],
        'education' => [],
        'experience' => [],
        'skills' => [],
        'hobbies' => [],
        'publication' => $_POST['publication'] ?? '',
        'social_links' => [
            'instagram' => $_POST['instagram'] ?? '',
            'whatsapp' => $_POST['whatsapp'] ?? '',
            'youtube' => $_POST['youtube'] ?? '',
            'linkedin' => $_POST['linkedin'] ?? ''
        ]
    ];
    
    // Education
    if (isset($_POST['edu_tahun'])) {
        foreach ($_POST['edu_tahun'] as $i => $tahun) {
            if (!empty($tahun)) {
                $json_data['education'][] = [
                    'tahun' => $tahun,
                    'institusi' => $_POST['edu_institusi'][$i] ?? '',
                    'deskripsi' => $_POST['edu_deskripsi'][$i] ?? ''
                ];
            }
        }
    }
    
    // Experience
    if (isset($_POST['experience'])) {
        foreach ($_POST['experience'] as $exp) {
            if (!empty($exp)) {
                $json_data['experience'][] = $exp;
            }
        }
    }
    
    // Skills
    if (isset($_POST['skill_kategori'])) {
        foreach ($_POST['skill_kategori'] as $i => $kategori) {
            if (!empty($kategori)) {
                $json_data['skills'][$kategori] = $_POST['skill_value'][$i] ?? '';
            }
        }
    }
    
    // Hobbies
    if (isset($_POST['hobby_icon'])) {
        foreach ($_POST['hobby_icon'] as $i => $icon) {
            if (!empty($icon)) {
                $json_data['hobbies'][] = [
                    'icon' => $icon,
                    'name' => $_POST['hobby_name'][$i] ?? ''
                ];
            }
        }
    }
    
    // Update JSON
    $json_dir = "Data/";
    if (!file_exists($json_dir)) {
        mkdir($json_dir, 0777, true);
    }
    file_put_contents($json_dir . $nim . ".json", json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // Update database
    mysqli_query($conn, "UPDATE profil SET nama='$nama', prodi='$prodi'$foto_update WHERE nim='$nim'");
    header("Location: adminProfil.php");
    exit;
}

// ============================================
// DELETE - Hapus Data Mahasiswa
// ============================================
if (isset($_GET['hapus'])) {
    $nim = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM profil WHERE nim='$nim'");
}

// ============================================
// READ - Tampilkan Semua Data Mahasiswa
// ============================================
$result = mysqli_query($conn, "SELECT * FROM profil");
?>
    <div class="form-crud">
        <form method="post" enctype="multipart/form-data">
            
            <!-- COLUMN 1: Data Inti & Biodata -->
            <div class="form-section">
                <h3>📋 Data Inti</h3>
                <div class="form-row">
                    <label>NIM:</label>
                    <input type="text" name="nim" value="<?= $edit_nim ?>" <?= $edit_nim ? 'readonly' : '' ?> required>
                </div>
                <div class="form-row">
                    <label>Nama:</label>
                    <input type="text" name="nama" value="<?= $edit_nama ?>" required>
                </div>
                <div class="form-row">
                    <label>Kode Prodi:</label>
                    <input type="text" name="prodi" value="<?= $edit_prodi ?>" required>
                </div>
                <div class="form-row">
                    <label>Foto:</label>
                    <input type="file" name="foto" accept="image/*">
                    <?php if ($edit_foto && $edit_foto != 'foto.jpg') { ?>
                        <small style="color:#666;font-size:11px;">Saat ini: <?= $edit_foto ?></small>
                    <?php } ?>
                </div>

                <h3 style="margin-top:20px;">🏠 Biodata</h3>
                <div class="form-row">
                    <label>Tempat Lahir:</label>
                    <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($edit_json_data['biodata']['tempat_lahir'] ?? '') ?>">
                </div>
                <div class="form-row">
                    <label>Tanggal Lahir:</label>
                    <input type="text" name="tanggal_lahir" value="<?= htmlspecialchars($edit_json_data['biodata']['tanggal_lahir'] ?? '') ?>" placeholder="DD Month YYYY">
                </div>
                <div class="form-row">
                    <label>Alamat:</label>
                    <textarea name="alamat" rows="2"><?= htmlspecialchars($edit_json_data['biodata']['alamat'] ?? '') ?></textarea>
                </div>

                <h3 style="margin-top:20px;">📢 Social Links</h3>
                <div class="form-row">
                    <label>Instagram:</label>
                    <input type="url" name="instagram" value="<?= htmlspecialchars($edit_json_data['social_links']['instagram'] ?? '') ?>" placeholder="https://instagram.com/username">
                </div>
                <div class="form-row">
                    <label>WhatsApp:</label>
                    <input type="url" name="whatsapp" value="<?= htmlspecialchars($edit_json_data['social_links']['whatsapp'] ?? '') ?>" placeholder="https://wa.me/628xxx">
                </div>
                <div class="form-row">
                    <label>YouTube:</label>
                    <input type="url" name="youtube" value="<?= htmlspecialchars($edit_json_data['social_links']['youtube'] ?? '') ?>" placeholder="https://youtube.com/@channel">
                </div>
                <div class="form-row">
                    <label>LinkedIn:</label>
                    <input type="url" name="linkedin" value="<?= htmlspecialchars($edit_json_data['social_links']['linkedin'] ?? '') ?>" placeholder="https://linkedin.com/in/username">
                </div>
            </div>

            <!-- COLUMN 2: Education, Experience, Skills, Hobbies -->
            <div class="form-section">
                <h3>🎓 Education</h3>
                <div id="educationContainer">
                    <?php
                    $educations = $edit_json_data['education'] ?? [['tahun' => '', 'institusi' => '', 'deskripsi' => '']];
                    foreach ($educations as $i => $edu) {
                    ?>
                    <div class="dynamic-item">
                        <input type="text" name="edu_tahun[]" value="<?= htmlspecialchars($edu['tahun'] ?? '') ?>" placeholder="2020-2023" style="width:100%;margin-bottom:5px;">
                        <input type="text" name="edu_institusi[]" value="<?= htmlspecialchars($edu['institusi'] ?? '') ?>" placeholder="Nama Institusi" style="width:100%;margin-bottom:5px;">
                        <textarea name="edu_deskripsi[]" rows="2" placeholder="Deskripsi singkat" style="width:100%;"><?= htmlspecialchars($edu['deskripsi'] ?? '') ?></textarea>
                        <?php if ($i > 0) { ?>
                        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✖ Hapus</button>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
                <button type="button" class="add-more-btn" onclick="addEducation()">+ Tambah Education</button>

                <h3 style="margin-top:20px;">💼 Experience</h3>
                <div id="experienceContainer">
                    <?php
                    $experiences = $edit_json_data['experience'] ?? [''];
                    foreach ($experiences as $i => $exp) {
                    ?>
                    <div class="dynamic-item">
                        <input type="text" name="experience[]" value="<?= htmlspecialchars($exp) ?>" placeholder="Posisi - Organisasi (Tahun)" style="width:calc(100% - 50px);">
                        <?php if ($i > 0) { ?>
                        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✖</button>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
                <button type="button" class="add-more-btn" onclick="addExperience()">+ Tambah Experience</button>

                <h3 style="margin-top:20px;">⚡ Skills</h3>
                <div id="skillsContainer">
                    <?php
                    $skills = $edit_json_data['skills'] ?? ['Programming' => ''];
                    $idx = 0;
                    foreach ($skills as $kategori => $value) {
                    ?>
                    <div class="dynamic-item skill-item">
                        <input type="text" name="skill_kategori[]" value="<?= htmlspecialchars($kategori) ?>" placeholder="Kategori">
                        <input type="text" name="skill_value[]" value="<?= htmlspecialchars($value) ?>" placeholder="Daftar skill">
                        <?php if ($idx > 0) { ?>
                        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✖</button>
                        <?php } else { ?>
                        <span></span>
                        <?php } ?>
                    </div>
                    <?php $idx++; } ?>
                </div>
                <button type="button" class="add-more-btn" onclick="addSkill()">+ Tambah Skill</button>

                <h3 style="margin-top:20px;">🎮 Hobbies</h3>
                <div id="hobbiesContainer">
                    <?php
                    $hobbies = $edit_json_data['hobbies'] ?? [['icon' => 'fa-gamepad', 'name' => '']];
                    foreach ($hobbies as $i => $hobby) {
                    ?>
                    <div class="dynamic-item hobby-item">
                        <input type="text" name="hobby_icon[]" value="<?= htmlspecialchars($hobby['icon'] ?? '') ?>" placeholder="fa-gamepad">
                        <input type="text" name="hobby_name[]" value="<?= htmlspecialchars($hobby['name'] ?? '') ?>" placeholder="Nama Hobby">
                        <?php if ($i > 0) { ?>
                        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✖</button>
                        <?php } else { ?>
                        <span></span>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
                <button type="button" class="add-more-btn" onclick="addHobby()">+ Tambah Hobby</button>

                <h3 style="margin-top:20px;">📝 Publication</h3>
                <div class="form-row">
                    <textarea name="publication" rows="3" placeholder="Daftar publikasi atau penelitian"><?= htmlspecialchars($edit_json_data['publication'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="form-buttons">
                <input type="submit" name="<?= $edit_nim ? 'ubah' : 'simpan' ?>" value="<?= $edit_nim ? '💾 SIMPAN PERUBAHAN' : '✅ TAMBAH DATA' ?>">
                <?php if ($edit_nim) { ?>
                    <a href="adminProfil.php" style="margin-left:16px;padding:8px 22px;background:#ffebee;border:1px solid #e57373;border-radius:4px;color:#d32f2f;text-decoration:none;font-weight:bold;display:inline-block;">❌ BATAL</a>
                <?php } ?>
            </div>
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

<script>
function addEducation() {
    const container = document.getElementById('educationContainer');
    const item = document.createElement('div');
    item.className = 'dynamic-item';
    item.innerHTML = `
        <input type="text" name="edu_tahun[]" placeholder="2020-2023" style="width:100%;margin-bottom:5px;">
        <input type="text" name="edu_institusi[]" placeholder="Nama Institusi" style="width:100%;margin-bottom:5px;">
        <textarea name="edu_deskripsi[]" rows="2" placeholder="Deskripsi singkat" style="width:100%;"></textarea>
        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✖ Hapus</button>
    `;
    container.appendChild(item);
}

function addExperience() {
    const container = document.getElementById('experienceContainer');
    const item = document.createElement('div');
    item.className = 'dynamic-item';
    item.innerHTML = `
        <input type="text" name="experience[]" placeholder="Posisi - Organisasi (Tahun)" style="width:calc(100% - 50px);">
        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✖</button>
    `;
    container.appendChild(item);
}

function addSkill() {
    const container = document.getElementById('skillsContainer');
    const item = document.createElement('div');
    item.className = 'dynamic-item skill-item';
    item.innerHTML = `
        <input type="text" name="skill_kategori[]" placeholder="Kategori">
        <input type="text" name="skill_value[]" placeholder="Daftar skill">
        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✖</button>
    `;
    container.appendChild(item);
}

function addHobby() {
    const container = document.getElementById('hobbiesContainer');
    const item = document.createElement('div');
    item.className = 'dynamic-item hobby-item';
    item.innerHTML = `
        <input type="text" name="hobby_icon[]" placeholder="fa-gamepad">
        <input type="text" name="hobby_name[]" placeholder="Nama Hobby">
        <button type="button" class="remove-btn" onclick="this.parentElement.remove()">✖</button>
    `;
    container.appendChild(item);
}
</script>

</body>
</html>