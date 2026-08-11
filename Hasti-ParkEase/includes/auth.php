<?php
if (session_status() === PHP_SESSION_NONE) session_start();
function e($text) { return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8'); }
function flash($key, $value = null) { if ($value !== null) { $_SESSION['flash'][$key] = $value; return; } $v = $_SESSION['flash'][$key] ?? null; unset($_SESSION['flash'][$key]); return $v; }
function require_login() { if (empty($_SESSION['user'])) { flash('error','Please log in first.'); header('Location: '.BASE_URL.'login.php'); exit; } }
function require_admin() { require_login(); if ($_SESSION['user']['role'] !== 'admin') { header('Location: '.BASE_URL.'user/dashboard.php'); exit; } }
function booking_code() { return 'PE-'.strtoupper(bin2hex(random_bytes(4))); }
function calculate_amount($start, $end) { $hours = max(1, ceil((strtotime($end)-strtotime($start))/3600)); return [$hours, $hours * 30]; }
?>
