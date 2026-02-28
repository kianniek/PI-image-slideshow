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

## Automation (Autostart after 1 Minute)
To make the slideshow launch automatically 60 seconds after the desktop loads, edit your Wayfire configuration:

1. Open the config file:
   `nano ~/.config/wayfire.ini`
2. Add the following under the `[autostart]` section:
   ```ini
   [autostart]
   slideshow = bash -c "sleep 60 && chromium --kiosk --incognito --noerrdialogs --ozone-platform=wayland --password-store=basic --touch-events=enabled http://localhost:8080/index.php"

```

## Screen Power Management (15-Minute Timeout)

To save your screen, you can configure the display to turn off after 15 minutes of inactivity.

1. In the same `~/.config/wayfire.ini` file, find or add the `[idle]` section:
```ini
[idle]
dpms_timeout = 900
screensaver_timeout = 900

```


*(Note: 900 seconds = 15 minutes)*.
2. To ensure the screen **wakes up on touch**, ensure your touchscreen drivers are active (default on most Pi displays).

## Manual Kiosk Command

If you need to run it manually via SSH:

```bash
WAYLAND_DISPLAY=wayland-0 chromium --kiosk --incognito --password-store=basic --touch-events=enabled http://localhost:8080/index.php

```
