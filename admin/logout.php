<?php
session_start();
session_unset();      // Hapus semua data session
session_destroy();    // Hancurkan session
header("Location: index.php"); // Kembali ke form login
exit;
?>
