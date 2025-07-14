<?php
session_start();

// Jika sudah login, langsung arahkan ke test.php
if (isset($_SESSION['username'])) {
  header("Location: test.php");
  exit;
}

// Koneksi database
$koneksi = new mysqli("localhost", "root", "", "db_pretest");
if ($koneksi->connect_error) {
  die("Koneksi gagal: " . $koneksi->connect_error);
}

$error = "";

// Proses setelah tombol submit ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  
  // Cek ke database
  $stmt = $koneksi->prepare("SELECT * FROM tb_peserta WHERE Peserta = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Username ditemukan, simpan di session
    $_SESSION['username'] = $username;
    header("Location: test.php");
    exit;
  } else {
    // Username tidak ditemukan
    $error = "Nama tidak terdaftar sebagai peserta.";
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Username</title>
  <link href="style.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <form action="" method="post">
      <h2>Masukkan Username</h2>
      <?php if ($error): ?>
        <p style="color:red;"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>
      <input type="text" name="username" placeholder="Nama Anda" required>
      <button type="submit">Masuk</button>
    </form>
  </div>
</body>
</html>
