# ParkEase — Smart Parking Slot Booking System

ParkEase is a third-year BCA minor project built with PHP, MySQL, Bootstrap, and JavaScript-friendly browser features. It supports vehicle-owner registration, live slot booking, simulated payments, QR passes, entry/exit processing, overtime fines, and administration reports.

## Run with XAMPP

1. Copy the `ParkEase` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`, choose **Import**, and select `database/parkease.sql`.
4. Visit `http://localhost/ParkEase/`.

The default MySQL configuration is `root` with no password. If yours differs, update `includes/db.php`.

## Demo administrator

- Email: `admin@parkease.test`
- Password: `admin123`

## Main features

- Secure registration and password hashing
- Current slot availability and advance booking
- Browser location support to sort slots by distance and open directions to the nearest parking
- Payment simulation and booking QR pass
- Admin QR code lookup, entry/exit recording, and automatic ₹50/hour overtime fine
- Slot management and daily activity reports

QR images use the public QRServer image API. The booking code is also displayed so the admin can enter it manually if the device is offline.

The seeded parking coordinates are demo coordinates in central Surat, Gujarat. Update the `latitude` and `longitude` values in `database/parkease.sql` (or directly in the `slots` table) for the real parking facility before deployment. Location access is requested only after the user clicks **Use my location**. Directions open on Google Maps India.
