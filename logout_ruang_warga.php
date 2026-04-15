<?php
session_start();
unset($_SESSION['rw_account_id'], $_SESSION['rw_nik'], $_SESSION['rw_nama']);
unset($_SESSION['penjual_id'], $_SESSION['penjual_nama_toko']);
header('Location: ruang_warga.php');
exit;
