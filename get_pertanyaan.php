<?php
session_start();
$koneksi = new mysqli("localhost", "root", "", "db_pretest");

$nama = $_SESSION['username'] ?? '';

$response = [
    'id' => 0,
    'isi' => 'Belum ada pertanyaan.',
    'sudahMenjawab' => false
];

$q = $koneksi->query("SELECT * FROM tb_pertanyaan ORDER BY ID DESC LIMIT 1");
if ($row = $q->fetch_assoc()) {
    $id_pertanyaan = $row['ID'];
    $isi = $row['Pertanyaan'];

    // Cek apakah user sudah menjawab pertanyaan ini
    $cek = $koneksi->prepare("SELECT COUNT(*) FROM tb_jawaban WHERE ID_Pertanyaan = ? AND Nama = ?");
    $cek->bind_param("is", $id_pertanyaan, $nama);
    $cek->execute();
    $cek->bind_result($jumlah);
    $cek->fetch();
    $cek->close();

    $response['id'] = $id_pertanyaan;
    $response['isi'] = $jumlah > 0 ? "Terimakasih telah menjawab, Tunggu pertanyaan selanjutnya..." : $isi;
    $response['sudahMenjawab'] = $jumlah > 0;
}

header('Content-Type: application/json');
echo json_encode($response);
