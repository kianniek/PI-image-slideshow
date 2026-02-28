# PI Image Slideshow

A lightweight, touch-enabled photo slideshow for Raspberry Pi (Debian Trixie/Wayland). It features a web-based upload portal and a "Setup Mode" QR code.

## Features
- **Auto-Rotation:** Slideshow cycles through images in the `photos/` folder.
- **Touch Support:** Tap screen to skip to the next image.
- **Web Upload:** Upload photos from any device on the same network.
- **Smart Mode:** Shows a QR code for the upload link if no photos exist.

## Prerequisites
- **Web Server:** Apache2 with PHP (`sudo apt install apache2 php libapache2-mod-php`)
- **QR Generator:** `qrencode` (`sudo apt install qrencode`)
- **Browser:** Chromium (for Kiosk Mode)

## Setup
1. Clone this repo to `/var/www/html`.
2. Ensure permissions are correct: 
   `sudo chown -R www-data:www-data /var/www/html`
3. Generate your specific QR code:
   `qrencode -o /var/www/html/qr.png "http://<YOUR_PI_IP>:8080/upload.php"`
4. Configure Apache to listen on port 8080.

## Kiosk Mode Command
```bash
WAYLAND_DISPLAY=wayland-0 chromium --kiosk --incognito --password-store=basic --touch-events=enabled http://localhost:8080/index.php

