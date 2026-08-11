<?php
require_once __DIR__.'/includes/db.php';
$available = 0;
$result = $mysqli->query("SELECT COUNT(*) c FROM slots WHERE status='available'");
if ($result) {
    $row = $result->fetch_assoc();
    $available = $row['c'] ?? 0;
}
$page_title='Smart parking made simple';
require __DIR__.'/includes/header.php';
?>
<section class="hero p-5 mb-5"><div class="row align-items-center"><div class="col-lg-8"><h1 class="display-5 fw-bold">Reserve your parking spot before you arrive.</h1><p class="lead">Book a slot, pay online, and use a QR code for quick entry and exit.</p><a class="btn btn-light btn-lg" href="<?=empty($_SESSION['user'])?BASE_URL.'auth-choice.php':BASE_URL.'user/slots.php'?>">Book a slot</a></div><div class="col-lg-4 text-center display-1">🅿️</div></div></section>
<div class="row g-4"><div class="col-md-4"><div class="card stat-card shadow-sm p-4"><div class="text-muted">Slots available now</div><div class="display-5 fw-bold text-primary"><?=$available?></div></div></div><div class="col-md-4"><div class="card stat-card shadow-sm p-4"><div class="fw-semibold">QR-based entry</div><p class="mb-0 text-muted">No paper tickets or waiting lines.</p></div></div><div class="col-md-4"><div class="card stat-card shadow-sm p-4"><div class="fw-semibold">Fair, automatic charges</div><p class="mb-0 text-muted">₹30/hour with an overtime fine of ₹50/hour.</p></div></div></div>
<?php require __DIR__.'/includes/footer.php'; ?>
