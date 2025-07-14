<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$koneksi = new mysqli("localhost", "root", "", "db_pretest");
if ($koneksi->connect_error) {
    die("Connection failed: " . $koneksi->connect_error);
}

// Ambil pertanyaan terakhir
$q = $koneksi->query("SELECT * FROM tb_pertanyaan ORDER BY ID DESC LIMIT 1") or die(mysqli_error($koneksi));
$pertanyaan = $q->fetch_assoc();

// Cek apakah user sudah menjawab pertanyaan terakhir
$sudahMenjawab = false;
$id_pertanyaan = 0;
$isiPertanyaan = "Belum ada pertanyaan.";

if ($pertanyaan) {
    $id_pertanyaan = $pertanyaan['ID'];
    $nama = $_SESSION['username'];

    // Cek apakah user sudah menjawab pertanyaan ini
    $cek = $koneksi->prepare("SELECT COUNT(*) FROM tb_jawaban WHERE ID_Pertanyaan = ? AND Nama = ?");
    $cek->bind_param("is", $id_pertanyaan, $nama);
    $cek->execute();
    $cek->bind_result($jumlah);
    $cek->fetch();
    $cek->close();

    $sudahMenjawab = $jumlah > 0;

    if ($sudahMenjawab) {
        $isiPertanyaan = "Terimakasih telah menjawab, Tunggu pertanyaan selanjutnya...";
    } else {
        $isiPertanyaan = $pertanyaan['Pertanyaan'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Form Jawaban</title>
    <link href="style.css" rel="stylesheet">
</head>

<body>

    <div class="container">
        <h2>Halo, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>

        <form action="proses.php" method="post">
            <label>Pertanyaan:</label>
            <p><strong><?= htmlspecialchars($isiPertanyaan) ?></strong></p>

            <!-- Kirim ID pertanyaan ke proses.php -->
            <input type="hidden" name="id_pertanyaan" value="<?= $id_pertanyaan ?>">

            <label for="jawaban">Silakan tulis jawaban Anda:</label>
            <textarea name="jawaban" id="jawaban" required></textarea>

            <button type="submit">OK</button>
        </form>

        <div class="logout">
            <a href="logout.php">Keluar</a>
        </div>
    </div>

</body>


</html>