<?php
$directory = 'photos/';
// Look for images
$images = glob($directory . "*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}", GLOB_BRACE);
$images_json = json_encode($images);
$has_images = count($images) > 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pi Photo Frame</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background-color: black;
            color: white;
            font-family: sans-serif;
            overflow: hidden;
        }
        #slideshow, #setup-mode {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        img.main-slide {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .qr-code {
            width: 250px;
            height: 250px;
            border: 10px solid white;
            border-radius: 10px;
            margin-top: 20px;
        }
        h1 { font-size: 2.5em; margin-bottom: 10px; }
        p { font-size: 1.2em; color: #ccc; }
    </style>
</head>
<body onclick="nextSlide()">

    <?php if (!$has_images): ?>
        <div id="setup-mode">
            <h1>No Photos Found</h1>
            <p>Scan the code to upload your first photo</p>
            <img src="qr.png" class="qr-code" onerror="this.alt='QR Code Missing - run qrencode command'">
        </div>
    <?php else: ?>
        <div id="slideshow">
            <img id="slide" class="main-slide" src="" alt="Loading...">
        </div>
    <?php endif; ?>

    <script>
        const images = <?php echo $images_json; ?>;
        let currentIndex = 0;
        const hasImages = <?php echo $has_images ? 'true' : 'false'; ?>;

        function showSlide() {
            if (!hasImages) return;
            const slideElement = document.getElementById('slide');
            slideElement.src = images[currentIndex] + '?t=' + new Date().getTime();
        }

        function nextSlide() {
            if (!hasImages) return;
            currentIndex = (currentIndex + 1) % images.length;
            showSlide();
        }

        if (hasImages) {
            // Change slide every 10 seconds
            let slideInterval = setInterval(nextSlide, 10000);

            // Auto-refresh page every 5 minutes to check for new photos
            // This is key so the Pi "wakes up" once you upload something
            setTimeout(() => { window.location.reload(); }, 300000);
            
            showSlide();
        } else {
            // If in setup mode, check for photos more frequently (every 30 seconds)
            setTimeout(() => { window.location.reload(); }, 30000);
        }
    </script>
</body>
</html>
