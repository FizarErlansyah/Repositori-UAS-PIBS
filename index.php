<?php
include "koneksi.php";

// Ambil NIM dari parameter GET, jika tidak ada gunakan default pertama
$selected_nim = isset($_GET['nim']) ? $_GET['nim'] : '';

// Jika tidak ada NIM yang dipilih, ambil data pertama
if (empty($selected_nim)) {
    $first_query = mysqli_query($conn, "SELECT nim FROM profil LIMIT 1");
    if ($first_row = mysqli_fetch_assoc($first_query)) {
        $selected_nim = $first_row['nim'];
    }
}

// Ambil data profil yang dipilih
$profil_data = null;
if (!empty($selected_nim)) {
    $query = mysqli_query($conn, "SELECT * FROM profil WHERE nim='$selected_nim'");
    $profil_data = mysqli_fetch_assoc($query);
}

// Ambil semua profil untuk dropdown
$all_profil = mysqli_query($conn, "SELECT nim, nama FROM profil ORDER BY nama");

// Load data dari JSON berdasarkan NIM
$json_data = null;
$json_file = "Data/" . $selected_nim . ".json";
if (file_exists($json_file)) {
    $json_content = file_get_contents($json_file);
    $json_data = json_decode($json_content, true);
}

// Fungsi helper untuk mendapatkan data dengan fallback
function getJsonData($json_data, $key, $default = '') {
    return isset($json_data[$key]) ? $json_data[$key] : $default;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Profile - <?= $profil_data ? $profil_data['nama'] : 'Student' ?></title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        header {
            position: relative;
            z-index: 1000;
        }
        .profile-selector {
            position: absolute;
            top: 20px;
            right: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1002;
        }
        .account-switcher {
            position: relative;
        }
        .account-button {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px 6px 6px;
            background: rgba(255,255,255,0.95);
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        .account-button:hover {
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .account-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #9a93d1, #e3e1fa);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: bold;
            font-size: 16px;
        }
        .dropdown-arrow {
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #666;
            margin-left: 4px;
        }
        .account-dropdown {
            position: absolute;
            top: 55px;
            right: 0;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.2);
            min-width: 280px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 1003;
        }
        .account-dropdown.show {
            display: block;
        }
        .dropdown-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.3);
            z-index: 999;
            display: none;
        }
        .dropdown-backdrop.show {
            display: block;
        }
        .dropdown-header {
            padding: 12px 16px;
            border-bottom: 1px solid #e0e0e0;
            font-weight: 600;
            font-size: 12px;
            color: #666;
        }
        .dropdown-item {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            color: #333;
        }
        .dropdown-item:hover {
            background: #f5f5f5;
        }
        .dropdown-item.active {
            background: #e3e1fa;
        }
        .dropdown-item-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #9a93d1, #e3e1fa);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }
        .dropdown-item-info {
            flex: 1;
        }
        .dropdown-item-name {
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 2px;
        }
        .dropdown-item-nim {
            font-size: 11px;
            color: #666;
        }
        .dropdown-divider {
            height: 1px;
            background: #e0e0e0;
            margin: 4px 0;
        }
        .manage-link {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            color: #9a93d1;
            font-weight: 600;
            font-size: 13px;
            border-top: 1px solid #e0e0e0;
        }
        .manage-link:hover {
            background: #f5f5f5;
        }
    </style>
</head>
<body>

    <div class="dropdown-backdrop" id="dropdownBackdrop" onclick="closeDropdown()"></div>

    <header>
        <div class="profile-selector">
            <div class="account-switcher">
                <div class="account-button" onclick="toggleDropdown()">
                    <div class="account-icon"><?= $profil_data ? strtoupper(substr($profil_data['nama'], 0, 1)) : 'U' ?></div>
                    <div class="dropdown-arrow"></div>
                </div>
                <div class="account-dropdown" id="accountDropdown">
                    <div class="dropdown-header">GANTI AKUN</div>
                    <?php 
                    mysqli_data_seek($all_profil, 0);
                    while ($row = mysqli_fetch_assoc($all_profil)) { 
                        $is_active = $row['nim'] == $selected_nim;
                    ?>
                        <a href="index.php?nim=<?= $row['nim'] ?>" class="dropdown-item <?= $is_active ? 'active' : '' ?>">
                            <div class="dropdown-item-icon"><?= strtoupper(substr($row['nama'], 0, 1)) ?></div>
                            <div class="dropdown-item-info">
                                <div class="dropdown-item-name"><?= $row['nama'] ?></div>
                                <div class="dropdown-item-nim"><?= $row['nim'] ?></div>
                            </div>
                        </a>
                    <?php } ?>
                    <div class="dropdown-divider"></div>
                    <a href="adminProfil.php" class="manage-link">
                        <i class="fas fa-cog"></i> Kelola Profil
                    </a>
                </div>
            </div>
        </div>
        <?php 
        $foto_profil = 'foto.jpg'; // default
        
        if ($profil_data) {
            // Cek apakah ada kolom foto di database
            if (isset($profil_data['foto']) && !empty($profil_data['foto'])) {
                $foto_profil = $profil_data['foto'];
            } 
            // Jika tidak ada di database, cek file foto-{nim}.jpg
            else if (file_exists('foto-' . $profil_data['nim'] . '.jpg')) {
                $foto_profil = 'foto-' . $profil_data['nim'] . '.jpg';
            }
            // Bisa juga cek format lain
            else if (file_exists('foto-' . $profil_data['nim'] . '.png')) {
                $foto_profil = 'foto-' . $profil_data['nim'] . '.png';
            }
        }
        ?>
        <img src="<?= $foto_profil ?>" alt="Profile Picture" class="profile-pic">
        <div class="header-text">
            <h1><?= strtoupper($profil_data ? $profil_data['nama'] : 'NAMA MAHASISWA') ?></h1>
            <p><?= $profil_data ? $profil_data['prodi'] : 'Program Studi' ?> | Jaya Development University</p>
        </div>
    </header>

    <main class="main-container">
        <nav class="side-navbar">
            <ul>
                <li><a href="#" onclick="showContent('biodata')">Biodata</a></li>
                <li><a href="#" onclick="showContent('education')">Education</a></li>
                <li><a href="#" onclick="showContent('experience')">Experience</a></li>
                <li><a href="#" onclick="showContent('skills')">Skills</a></li>
                <li><a href="#" onclick="showContent('publication')">Publicatizon</a></li>
            </ul>
        </nav>

        <section class="main-content">
            <div id="biodata" class="content-item">
                <h2>Biodata</h2>
                <div class="grid-container">
                    <div class="grid-item">
                        <span>NAME</span>
                        <h4><?= $profil_data ? $profil_data['nama'] : 'Fizar Erlansyah' ?></h4>
                    </div>
                    <div class="grid-item">
                        <span>STUDY PROGRAM</span>
                        <h4><?= $profil_data ? $profil_data['prodi'] : 'Information System' ?></h4>
                    </div>
                    <div class="grid-item">
                        <span>PLACE OF BIRTH</span>
                        <h4><?= $json_data && isset($json_data['biodata']['tempat_lahir']) ? $json_data['biodata']['tempat_lahir'] : 'Brebes, Central Java' ?></h4>
                    </div>
                    <div class="grid-item">
                        <span>DATE OF BIRTH</span>
                        <h4><?= $json_data && isset($json_data['biodata']['tanggal_lahir']) ? $json_data['biodata']['tanggal_lahir'] : 'June 25, 2006' ?></h4>
                    </div>
                     <div class="grid-item">
                        <span>ADDRESS</span>
                        <h4><?= $json_data && isset($json_data['biodata']['alamat']) ? $json_data['biodata']['alamat'] : 'BSD City, South Tangerang' ?></h4>
                    </div>
                    <div class="grid-item">
                        <span>UNIVERSITY</span>
                        <h4>Jaya Development University</h4>
                    </div>
                </div>
            </div>

            <div id="education" class="content-item hidden">
                <h2>Education History</h2>
                <div class="timeline">
                    <?php
                    $default_education = [
                        ['tahun' => '2012 - 2018', 'institusi' => 'MIS Raudlatul Irfan', 'deskripsi' => 'Completed elementary school education.'],
                        ['tahun' => '2018 - 2021', 'institusi' => 'MTsN 5 Tangerang', 'deskripsi' => 'Completed junior high school education.'],
                        ['tahun' => '2021 - 2024', 'institusi' => 'SMK Pustek Serpong', 'deskripsi' => 'Completed senior high school education.'],
                        ['tahun' => '2024 - 2028', 'institusi' => 'Jaya Development University', 'deskripsi' => 'Active student of the Information Systems study program.']
                    ];
                    
                    $education = ($json_data && isset($json_data['education'])) ? $json_data['education'] : $default_education;
                    
                    foreach ($education as $edu) {
                        echo '<div class="timeline-item">';
                        echo '<div class="timeline-date">' . htmlspecialchars($edu['tahun']) . '</div>';
                        echo '<div class="timeline-content">';
                        echo '<h3>' . htmlspecialchars($edu['institusi']) . '</h3>';
                        echo '<p>' . htmlspecialchars($edu['deskripsi']) . '</p>';
                        echo '</div></div>';
                    }
                    ?>
                </div>
            </div>

            <div id="experience" class="content-item hidden">
                <h2>Experience</h2>
                <div class="experience-list">
                    <?php
                    $default_experience = [
                        'Tax Administration Intern - KPP Pratama Tigaraksa (2023)',
                        'Secretary DE\'GREEN EXPO Committee (2024)',
                        'UI Designer (2025)',
                        'Secretary - Company Visit Committee (2025)',
                        'Sponsorship Division - GIDHUB Event (2025)',
                        'Event Chairperson - Information System Department Gathering (2025)'
                    ];
                    
                    $experience = ($json_data && isset($json_data['experience'])) ? $json_data['experience'] : $default_experience;
                    
                    foreach ($experience as $exp) {
                        echo '<div class="experience-item">';
                        echo '<p>' . htmlspecialchars($exp) . '</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
            
            <div id="skills" class="content-item hidden">
                <h2>Skills</h2>
                <div class="grid-container">
                    <?php
                    $default_skills = [
                        'Web Development' => 'HTML, CSS, JavaScript',
                        'Programming' => 'Python, PHP, Java',
                        'Database' => 'MySQL',
                        'Design' => 'Figma & Canva',
                        'Tools' => 'Git & VS Code',
                        'Soft Skills' => 'Teamwork & Problem Solving'
                    ];
                    
                    $skills = ($json_data && isset($json_data['skills'])) ? $json_data['skills'] : $default_skills;
                    
                    foreach ($skills as $skill_name => $skill_desc) {
                        echo '<div class="skill-card">';
                        echo '<h5>' . htmlspecialchars($skill_name) . '</h5>';
                        echo '<p>' . htmlspecialchars($skill_desc) . '</p>';
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <div id="publication" class="content-item hidden">
                <h2>Publication</h2>
                <?php
                $publication = ($json_data && isset($json_data['publication'])) ? $json_data['publication'] : 'No publications at this time.';
                
                if (is_array($publication)) {
                    echo '<div class="experience-list">';
                    foreach ($publication as $pub) {
                        echo '<div class="experience-item"><p>' . htmlspecialchars($pub) . '</p></div>';
                    }
                    echo '</div>';
                } else {
                    echo '<p>' . htmlspecialchars($publication) . '</p>';
                }
                ?>
            </div>
        </section>

        <aside class="side-aside">
            <h3>Hobbies & Interests</h3>
            <div class="hobbies-container">
                <?php
                $default_hobbies = [
                    ['icon' => 'fa-gamepad', 'name' => 'Gaming'],
                    ['icon' => 'fa-car', 'name' => 'Automotive'],
                    ['icon' => 'fa-code', 'name' => 'Programming'],
                    ['icon' => 'fa-palette', 'name' => 'Design'],
                    ['icon' => 'fa-film', 'name' => 'Watching Movies']
                ];
                
                $hobbies = ($json_data && isset($json_data['hobbies'])) ? $json_data['hobbies'] : $default_hobbies;
                
                foreach ($hobbies as $hobby) {
                    if (is_array($hobby) && isset($hobby['icon']) && isset($hobby['name'])) {
                        echo '<div class="hobby-card">';
                        echo '<i class="fa-solid ' . htmlspecialchars($hobby['icon']) . '"></i>';
                        echo '<span>' . htmlspecialchars($hobby['name']) . '</span>';
                        echo '</div>';
                    } else if (is_string($hobby)) {
                        echo '<div class="hobby-card">';
                        echo '<i class="fa-solid fa-heart"></i>';
                        echo '<span>' . htmlspecialchars($hobby) . '</span>';
                        echo '</div>';
                    }
                }
                ?>
            </div>
        </aside>
    </main>

    <footer>
        <div class="social-links">
            <a href="https://www.instagram.com/itzfizar?igsh=cDNvdjI2bGk0bWt4" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="https://wa.me/qr/4LSDIVD5XSCDL1" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
            <a href="https://youtube.com/@fizar.erlansyah?si=bdkdsrPai4PMbOX1" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            <a href="https://www.linkedin.com/in/fizar-erlansyah-311984372?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
        </div>
        <div class="copyright">
            <p>Copyright &copy; 2025. All Rights Reserved</p>
        </div>
        <div class="footer-info">
            <h4>FIZARS WEB</h4>
            <p>Try to be strong</p>
        </div>
    </footer>

    <script>
        function showContent(id) {
            const contentItems = document.querySelectorAll('.content-item');
            contentItems.forEach(item => {
                item.classList.add('hidden');
            });

            const selectedContent = document.getElementById(id);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }
        }

        function toggleDropdown() {
            const dropdown = document.getElementById('accountDropdown');
            const backdrop = document.getElementById('dropdownBackdrop');
            dropdown.classList.toggle('show');
            backdrop.classList.toggle('show');
        }

        function closeDropdown() {
            const dropdown = document.getElementById('accountDropdown');
            const backdrop = document.getElementById('dropdownBackdrop');
            dropdown.classList.remove('show');
            backdrop.classList.remove('show');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const switcher = document.querySelector('.account-switcher');
            const dropdown = document.getElementById('accountDropdown');
            const backdrop = document.getElementById('dropdownBackdrop');
            
            if (switcher && !switcher.contains(event.target) && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
                backdrop.classList.remove('show');
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            showContent('biodata');
        });
    </script>

</body>
</html>