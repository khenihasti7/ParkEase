<?php require_once __DIR__.'/../includes/db.php'; require_once __DIR__.'/../includes/auth.php'; require_login(); $id=(int)($_GET['slot']??0); $stmt=$mysqli->prepare("SELECT * FROM slots WHERE id=? AND status='available'"); $stmt->bind_param('i',$id); $stmt->execute(); $slot=$stmt->get_result()->fetch_assoc(); if(!$slot){ flash('error','That slot is no longer available.'); header('Location: slots.php'); exit; }
function round_to_next_half_hour($timestamp) {
    $h = (int) date('H', $timestamp);
    $m = (int) date('i', $timestamp);
    if ($m === 0 || $m === 30) {
        return sprintf('%02d:%02d', $h, $m);
    }
    if ($m < 30) {
        return sprintf('%02d:30', $h);
    }
    return sprintf('%02d:00', ($h + 1) % 24);
}

function format_time_options() {
    $options = [];
    for ($h = 0; $h < 24; $h++) {
        foreach (['00', '30'] as $m) {
            $value = sprintf('%02d:%s', $h, $m);
            $hour = $h % 12 === 0 ? 12 : $h % 12;
            $ampm = $h < 12 ? 'AM' : 'PM';
            $label = sprintf('%02d:%s %s', $hour, $m, $ampm);
            $options[$value] = $label;
        }
    }
    return $options;
}

$start_date = '';
$start_time = '';
$end_date = '';
$end_time = '';
$default_start = round_to_next_half_hour(time());
$default_end = round_to_next_half_hour(strtotime('+1 hour'));
if($_SERVER['REQUEST_METHOD']==='POST'){
    $start_date=trim($_POST['start_date'] ?? '');
    $start_time=trim($_POST['start_time'] ?? '');
    $end_date=trim($_POST['end_date'] ?? '');
    $end_time=trim($_POST['end_time'] ?? '');
    $start="{$start_date} {$start_time}";
    $end="{$end_date} {$end_time}";
    if(!$start_date||!$start_time||!$end_date||!$end_time){
        flash('error','Complete both date and time for arrival and exit.');
    } elseif(strtotime($end)<=strtotime($start)){
        flash('error','End date/time must be after arrival date/time.');
    } else {
        [$hours,$amount]=calculate_amount($start,$end);
        $code=booking_code();
        $mysqli->begin_transaction();
        try {
            $stmt=$mysqli->prepare("INSERT INTO bookings(user_id,slot_id,booking_code,start_time,end_time,parking_amount,status) VALUES(?,?,?,?,?,?, 'pending_payment')");
            $uid=$_SESSION['user']['id'];
            $stmt->bind_param('iisssd',$uid,$id,$code,$start,$end,$amount);
            $stmt->execute();
            $bid=$mysqli->insert_id;
            $stmt=$mysqli->prepare("UPDATE slots SET status='reserved' WHERE id=? AND status='available'");
            $stmt->bind_param('i',$id);
            $stmt->execute();
            if(!$stmt->affected_rows) throw new Exception();
            $mysqli->commit();
            header('Location: payment.php?id='.$bid);
            exit;
        }catch(Throwable $e){
            $mysqli->rollback();
            flash('error','Unable to create booking. Please try again.');
        }
    }
} else {
    $current = time();
    $start_date = date('Y-m-d', $current);
    $start_time = $default_start;
    $end_timestamp = strtotime($start_date.' '.$start_time.' +1 hour');
    $end_date = date('Y-m-d', $end_timestamp);
    $end_time = $default_end;
}
$page_title='Book '.$slot['slot_number']; require __DIR__.'/../includes/header.php'; ?>
<div class="row justify-content-center"><div class="col-lg-7"><div class="card shadow-sm border-0"><div class="card-body p-4"><h2>Book slot <?=e($slot['slot_number'])?></h2><p class="text-muted">₹30 per hour. Overtime is charged at ₹50 per additional hour on exit.</p><form method="post" class="row g-3"><div class="col-md-3"><label class="form-label">Arrival date</label><input class="form-control" type="date" name="start_date" min="<?=date('Y-m-d')?>" value="<?=e($start_date)?>" required></div><div class="col-md-3"><label class="form-label">Arrival time</label><div class="input-group"><select id="start_time" class="form-select" name="start_time" required><?php foreach(format_time_options() as $value => $label): ?><option value="<?=e($value)?>"<?= $start_time=== $value ? ' selected' : ''?>><?=e($label)?></option><?php endforeach; ?></select><button id="use_now" class="btn btn-outline-secondary" type="button" title="Set time to the next available slot">Now</button></div></div><div class="col-md-3"><label class="form-label">Exit date</label><input id="end_date" class="form-control" type="date" name="end_date" min="<?=date('Y-m-d')?>" value="<?=e($end_date)?>" required></div><div class="col-md-3"><label class="form-label">Exit time</label><select id="end_time" class="form-select" name="end_time" required><?php foreach(format_time_options() as $value => $label): ?><option value="<?=e($value)?>"<?= $end_time=== $value ? ' selected' : ''?>><?=e($label)?></option><?php endforeach; ?></select></div><div class="col-12"><button class="btn btn-primary">Continue to payment</button><a class="btn btn-outline-secondary" href="slots.php">Cancel</a></div></form></div></div></div></div>
<script>
(function(){
    function roundToNextHalfHour(date){
        const m = date.getMinutes();
        if(m===0 || m===30) return date;
        if(m < 30) date.setMinutes(30);
        else { date.setHours(date.getHours() + 1); date.setMinutes(0); }
        date.setSeconds(0);
        return date;
    }
    const nowButton = document.getElementById('use_now');
    const startTimeSelect = document.getElementById('start_time');
    const endDateInput = document.getElementById('end_date');
    const endTimeSelect = document.getElementById('end_time');
    if(!nowButton || !startTimeSelect || !endDateInput || !endTimeSelect) return;
    nowButton.addEventListener('click', function(){
        const now = roundToNextHalfHour(new Date());
        const next = new Date(now.getTime() + 60*60*1000);
        const pad = v => String(v).padStart(2, '0');
        startTimeSelect.value = pad(now.getHours()) + ':' + pad(now.getMinutes());
        document.querySelector('input[name="start_date"]').value = now.toISOString().slice(0,10);
        endTimeSelect.value = pad(next.getHours()) + ':' + pad(next.getMinutes());
        endDateInput.value = next.toISOString().slice(0,10);
    });
})();
</script>
<?php require __DIR__.'/../includes/footer.php'; ?>
