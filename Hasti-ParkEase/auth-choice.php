<?php
require_once __DIR__.'/includes/db.php';
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/includes/auth.php';

if (!empty($_SESSION['user'])) {
    header('Location: '.BASE_URL.'user/slots.php');
    exit;
}

$page_title='Start booking';
require __DIR__.'/includes/header.php';
?>
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body p-4 p-md-5">
                <h2>Ready to park?</h2>
                <p class="text-muted mb-4">Log in to book a parking slot, or create an account to get started.</p>
                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                    <a class="btn btn-primary btn-lg px-4" href="<?=BASE_URL?>login.php">Log in</a>
                    <a class="btn btn-outline-primary btn-lg px-4" href="<?=BASE_URL?>register.php">Create an account</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>