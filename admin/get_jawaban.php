<?php
$koneksi = new mysqli("localhost", "root", "", "db_pretest");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil semua pertanyaan
$pertanyaanList = [];
$q_pertanyaan = $koneksi->query("SELECT * FROM tb_pertanyaan ORDER BY ID ASC");
while ($row = $q_pertanyaan->fetch_assoc()) {
    $pertanyaanList[] = $row['Pertanyaan'];
}

// Ambil semua jawaban
$query = $koneksi->query("SELECT * FROM tb_jawaban ORDER BY Nama ASC, ID ASC");

$dataByNama = [];
while ($row = $query->fetch_assoc()) {
    $dataByNama[$row['Nama']][] = $row['Jawaban'];
}

// Bangun tabel HTML
$no = 1;
$html = "";
foreach ($dataByNama as $nama => $jawabanList) {
    for ($i = 0; $i < count($jawabanList); $i++) {
        $pertanyaan = isset($pertanyaanList[$i]) ? $pertanyaanList[$i] : "Pertanyaan tidak tersedia";
        $html .= "<tr>";
        $html .= "<td>" . $no++ . "</td>";
        $html .= "<td>" . htmlspecialchars($pertanyaan) . "</td>";
        $html .= "<td>" . htmlspecialchars($nama) . "</td>";
        $html .= "<td>" . nl2br(htmlspecialchars($jawabanList[$i])) . "</td>";
        $html .= "</tr>";
    }
}

echo $html;
?>
