<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['jawaban'], $_POST['id_pertanyaan'])) {
    $nama = $_SESSION['username'];
    $jawaban = htmlspecialchars($_POST['jawaban']);
    $id_pertanyaan = (int) $_POST['id_pertanyaan'];

    $koneksi = new mysqli("localhost", "root", "", "db_pretest");
    if ($koneksi->connect_error) {
        die("Connection failed: " . $koneksi->connect_error);
    }

    $stmt = $koneksi->prepare("INSERT INTO tb_jawaban (ID_Pertanyaan, Nama, Jawaban) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_pertanyaan, $nama, $jawaban);
    $stmt->execute();
    $stmt->close();

    // Redirect kembali ke form agar tidak dobel saat refresh
    header("Location: test.php");
    exit;
}
?>
