<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}
?>
<?php
$koneksi = new mysqli("localhost", "root", "", "db_pretest");
if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}
?>

<?php
// Proses form jika tombol diklik
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST['pertanyaan'])) {
    $pertanyaan = htmlspecialchars($_POST['pertanyaan']);
    $stmt = $koneksi->prepare("INSERT INTO tb_pertanyaan (Pertanyaan) VALUES (?)");
    $stmt->bind_param("s", $pertanyaan);
    $stmt->execute();
    $stmt->close();

    // Redirect setelah submit agar tidak dobel saat reload
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reset'])) {
    $koneksi->query("TRUNCATE TABLE tb_jawaban") or die(mysqli_error($koneksi));
    $koneksi->query("TRUNCATE TABLE tb_pertanyaan") or die(mysqli_error($koneksi));

    // Redirect agar tidak mengeksekusi ulang saat reload
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="jawaban_export.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['No', 'Pertanyaan', 'Nama', 'Jawaban']);

    // Ambil semua pertanyaan
    $pertanyaanList = [];
    $q_pertanyaan = $koneksi->query("SELECT * FROM tb_pertanyaan ORDER BY ID ASC");
    while ($row = $q_pertanyaan->fetch_assoc()) {
        $pertanyaanList[] = $row['Pertanyaan'];
    }

    // Ambil semua jawaban
    $query = $koneksi->query("SELECT * FROM tb_jawaban ORDER BY Nama ASC, ID ASC") or die(mysqli_error($koneksi));

    // Kelompokkan jawaban berdasarkan nama
    $dataByNama = [];
    while ($row = $query->fetch_assoc()) {
        $dataByNama[$row['Nama']][] = $row['Jawaban'];
    }

    $no = 1;
    foreach ($dataByNama as $nama => $jawabanList) {
        for ($i = 0; $i < count($jawabanList); $i++) {
            $pertanyaan = isset($pertanyaanList[$i]) ? $pertanyaanList[$i] : "Pertanyaan tidak tersedia";
            fputcsv($output, [$no++, $pertanyaan, $nama, $jawabanList[$i]]);
        }
    }

    fclose($output);
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Form ADMINS</title>
    <link href="../style.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h2>Pertanyaan</h2>

        <!-- Form input -->
        <form method="post" action="">
            <textarea name="pertanyaan" id="pertanyaan" required placeholder="Tulis pertanyaan Anda..."></textarea>
            <button type="submit">OK</button>
        </form>

        <hr style="margin: 2rem 0;">

        <!-- Daftar pertanyaan -->
        <h3>Daftar Pertanyaan</h3>
        <?php
        $query = $koneksi->query("SELECT * FROM tb_pertanyaan ORDER BY ID DESC") or die(mysqli_error($koneksi));
        while ($row = $query->fetch_assoc()) {
            echo "<h5>" . $row['ID'] . ". " . htmlspecialchars($row['Pertanyaan']) . "</h5>";
        }
        ?>
    </div>



    </div>



    <div class="container">
        <h2>Halo, <?= htmlspecialchars($_SESSION['username']) ?>!</h2><br>
        <h2>Rekap Jawaban Mahasiswa</h2><br>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pertanyaan</th>
                        <th>Nama</th>
                        <th>Jawaban</th>
                    </tr>
                </thead>
                <tbody id="jawaban-body">
                    <?php
                    // Ambil semua pertanyaan (berurutan ID)
                    $pertanyaanList = [];
                    $q_pertanyaan = $koneksi->query("SELECT * FROM tb_pertanyaan ORDER BY ID ASC");
                    while ($row = $q_pertanyaan->fetch_assoc()) {
                        $pertanyaanList[] = $row['Pertanyaan'];
                    }

                    // Ambil semua jawaban
                    $query = $koneksi->query("SELECT * FROM tb_jawaban ORDER BY Nama ASC, ID ASC") or die(mysqli_error($koneksi));

                    // Kelompokkan jawaban berdasarkan nama
                    $dataByNama = [];
                    while ($row = $query->fetch_assoc()) {
                        $dataByNama[$row['Nama']][] = $row['Jawaban'];
                    }

                    // Tampilkan ke tabel
                    $no = 1;
                    foreach ($dataByNama as $nama => $jawabanList) {
                        for ($i = 0; $i < count($jawabanList); $i++) {
                            $pertanyaan = isset($pertanyaanList[$i]) ? $pertanyaanList[$i] : "Pertanyaan tidak tersedia";
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($pertanyaan) . "</td>";
                            echo "<td>" . htmlspecialchars($nama) . "</td>";
                            echo "<td>" . nl2br(htmlspecialchars($jawabanList[$i])) . "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                </tbody>



            </table>
            <script>
setInterval(() => {
  fetch('get_jawaban.php')
    .then(res => res.text())
    .then(html => {
      document.getElementById("jawaban-body").innerHTML = html;
    });
}, 3000); // update tiap 3 detik
</script>


<form method="post" onsubmit="return confirm('Yakin ingin menghapus semua data pertanyaan dan jawaban?')">
    <button type="submit" name="reset" style="background-color:red; color:white; padding:0.5rem 1rem; border:none; border-radius:6px; cursor:pointer;">
        🔁 Reset Semua Data
    </button>
</form>
    <!-- Tombol Export -->
<form method="post">
    <button type="submit" name="export" style="margin-top: 1rem; background-color:green; color:white; padding:0.5rem 1rem; border:none; border-radius:6px; cursor:pointer;">
        📥 Export Jawaban
    </button>


</form>





        </div>

        <div class="logout">
            <a href="logout.php">Keluar</a>
        </div>
    </div>

</body>

</html>