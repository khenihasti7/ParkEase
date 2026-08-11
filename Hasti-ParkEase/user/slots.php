<?php require_once __DIR__.'/../includes/db.php'; require_once __DIR__.'/../includes/auth.php'; require_login(); $slots=$mysqli->query('SELECT * FROM slots ORDER BY slot_number'); $page_title='Available slots'; require __DIR__.'/../includes/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-0">Parking slots</h2><span class="text-muted">Choose an available slot or find the closest one.</span></div></div>
<section class="card shadow-sm border-0 p-4 mb-4 location-panel">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 align-items-md-center">
        <div><h5 class="mb-1">Available parking near me</h5><p id="location-message" class="text-muted mb-0">Allow location access to see the distance to ParkEase slots.</p></div>
        <button id="find-nearby" class="btn btn-primary">Use my location</button>
    </div>
    <div id="location-result" class="small mt-3 d-none"></div>
</section>
<section class="card shadow-sm border-0 p-3 mb-4">
    <h5 class="mb-3">Your current location and nearby parking</h5>
    <div class="map-container">
        <iframe id="parking-map" title="ParkEase parking location in Surat" src="https://www.google.com/maps?q=21.1702400,72.8310600&z=15&output=embed" loading="lazy" allowfullscreen></iframe>
    </div>
</section>
<div id="slot-list" class="row g-3"><?php while($s=$slots->fetch_assoc()): ?><div class="col-md-4 col-lg-3 slot-item" data-lat="<?=e($s['latitude'] ?? '')?>" data-lng="<?=e($s['longitude'] ?? '')?>" data-available="<?=$s['status']==='available'?'1':'0'?>"><div class="card slot shadow-sm p-3 <?= $s['status']==='available'?'':'bg-light'?>"><div class="d-flex justify-content-between"><b><?=e($s['slot_number'])?></b><span class="badge text-bg-<?=$s['status']==='available'?'success':'secondary'?>"><?=e($s['status'])?></span></div><small class="text-muted mb-auto"><?=e($s['location'])?></small><span class="distance text-primary small mt-2"></span><?php if($s['status']==='available'): ?><a class="btn btn-primary btn-sm mt-3" href="book.php?slot=<?=$s['id']?>">Book this slot</a><?php endif; ?></div></div><?php endwhile; ?></div>
<script>
const findNearby = document.getElementById('find-nearby');
const message = document.getElementById('location-message');
const result = document.getElementById('location-result');
const parkingMap = document.getElementById('parking-map');
const slotItems = [...document.querySelectorAll('.slot-item')];

function distanceInKm(lat1, lon1, lat2, lon2) {
    const radius = 6371;
    const toRadians = value => value * Math.PI / 180;
    const dLat = toRadians(lat2 - lat1);
    const dLon = toRadians(lon2 - lon1);
    const a = Math.sin(dLat / 2) ** 2 + Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) * Math.sin(dLon / 2) ** 2;
    return radius * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

function locateNearbyParking() {
    if (!navigator.geolocation) {
        message.textContent = 'Location services are not supported by this browser.';
        return;
    }
    findNearby.disabled = true;
    findNearby.textContent = 'Finding parking...';
    navigator.geolocation.getCurrentPosition(position => {
        const {latitude, longitude} = position.coords;
        const located = slotItems.map(item => {
            const hasCoordinates = item.dataset.lat !== '' && item.dataset.lng !== '';
            const distance = hasCoordinates ? distanceInKm(latitude, longitude, Number(item.dataset.lat), Number(item.dataset.lng)) : NaN;
            item.querySelector('.distance').textContent = Number.isFinite(distance) ? `${distance.toFixed(1)} km away` : 'Location unavailable';
            return {item, distance};
        }).sort((a, b) => a.distance - b.distance);
        located.forEach(({item}) => document.getElementById('slot-list').appendChild(item));
        const nearest = located.find(entry => entry.item.dataset.available === '1' && Number.isFinite(entry.distance));
        message.textContent = nearest ? `Showing available slots nearest to your current location.` : 'No available parking with configured coordinates was found.';
        // Center the map on the user's device location; directions still point to parking.
        parkingMap.src = `https://www.google.com/maps?q=${latitude},${longitude}&z=15&output=embed`;
        result.className = 'small mt-3';
        result.innerHTML = nearest ? `<a href="https://www.google.co.in/maps/dir/?api=1&destination=${nearest.item.dataset.lat},${nearest.item.dataset.lng}" target="_blank" rel="noopener">Get directions to the nearest parking in Surat</a>` : '';
        findNearby.disabled = false;
        findNearby.textContent = 'Refresh location';
    }, () => {
        message.textContent = 'We could not access your location. Check browser permissions and try again.';
        findNearby.disabled = false;
        findNearby.textContent = 'Try again';
    }, {enableHighAccuracy: true, timeout: 10000});
}

findNearby.addEventListener('click', locateNearbyParking);

// Request the PC, laptop, or phone location as soon as the user opens this page.
locateNearbyParking();
</script>
<?php require __DIR__.'/../includes/footer.php'; ?>
