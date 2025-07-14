<?php
session_start();

// Jika sudah login, langsung arahkan ke test.php
if (isset($_SESSION['username'])) {
  header("Location: admin.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Username</title>
<link href="../style.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <form action="" method="post">
      <h2>Masukkan Username</h2>
      <input type="text" name="username" placeholder="Nama Anda" required>
      <button type="submit">Masuk</button>
    </form>
  </div>
</body>
</html>

<?php
// Proses setelah tombol submit ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $_SESSION['username'] = htmlspecialchars($_POST['username']);
  header("Location: admin.php");
  exit;
}
?>
