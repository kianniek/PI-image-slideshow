<?php
$target_dir = "photos/";
$message = "";

// Ensure directory exists
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// 1. Handle File Uploads
if (isset($_FILES['image'])) {
    $file_name = time() . "_" . basename($_FILES['image']['name']);
    $target_file = $target_dir . $file_name;
    
    // Basic check: only allow common image formats
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed = array("jpg", "jpeg", "png", "gif");

    if (in_array($imageFileType, $allowed)) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $message = "<div class='alert success'>Uploaded successfully: $file_name</div>";
        } else {
            $message = "<div class='alert error'>Error uploading your file.</div>";
        }
    } else {
        $message = "<div class='alert error'>Error: Only JPG, PNG & GIF allowed.</div>";
    }
}

// 2. Handle Deletions
if (isset($_GET['delete'])) {
    $file_to_delete = $target_dir . basename($_GET['delete']);
    if (file_exists($file_to_delete)) {
        unlink($file_to_delete);
        $message = "<div class='alert warning'>Deleted: " . htmlspecialchars($_GET['delete']) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Photo Manager</title>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f8fafc;
            --surface: #ffffff;
            --text: #334155;
            --border: #e2e8f0;
        }

        body { 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; 
            background-color: var(--bg);
            color: var(--text);
            padding: 2rem; 
            margin: 0;
            line-height: 1.6; 
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        .alert.success { background: #dcfce7; color: #166534; }
        .alert.error { background: #fee2e2; color: #991b1b; }
        .alert.warning { background: #fef3c7; color: #92400e; }

        /* Header & Navigation */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .back-link {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover { text-decoration: underline; }

        /* Drag & Drop Zone */
        .upload-card {
            background: var(--surface);
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .drop-zone {
            border: 2px dashed var(--primary);
            border-radius: 8px;
            padding: 3rem 2rem;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #f8fafc;
        }
        .drop-zone.highlight {
            background: #e0e7ff;
            border-color: var(--primary-hover);
        }
        .drop-zone p {
            margin: 0;
            font-size: 1.1rem;
            color: #64748b;
        }
        .drop-zone span {
            color: var(--primary);
            font-weight: 600;
        }

        /* Photo Gallery Grid */
        h2 { margin-bottom: 1rem; }
        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1.5rem;
        }
        .photo-card {
            background: var(--surface);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
            position: relative;
            aspect-ratio: 1 / 1;
            group: hover;
        }
        .photo-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.3s ease;
        }
        .photo-card:hover img {
            transform: scale(1.05);
        }
        .photo-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        .photo-card:hover .photo-overlay {
            opacity: 1;
        }
        .delete-btn { 
            color: white; 
            background: #ef4444; 
            padding: 0.5rem 1rem; 
            text-decoration: none; 
            border-radius: 6px; 
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            transition: background 0.2s;
        }
        .delete-btn:hover { background: #dc2626; }
        
        .empty-state {
            color: #94a3b8;
            font-style: italic;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Photo Manager</h2>
        <a href="index.php" class="back-link">← Back to Slideshow</a>
    </div>

    <?= $message ?>

    <div class="upload-card">
        <form id="uploadForm" action="" method="POST" enctype="multipart/form-data">
            <div id="drop-zone" class="drop-zone">
                <p>Drag & Drop an image here or <span>Click to Browse</span></p>
                <input type="file" name="image" id="file-input" accept=".jpg, .jpeg, .png, .gif" required hidden>
            </div>
        </form>
    </div>

    <h2>Current Photos</h2>
    <div class="gallery">
        <?php
        $files = glob($target_dir . "*.{jpg,png,gif,jpeg}", GLOB_BRACE);
        
        if (count($files) > 0) {
            foreach($files as $file) {
                $name = basename($file);
                echo "<div class='photo-card'>";
                echo "<img src='$file' alt='$name' loading='lazy'>";
                echo "<div class='photo-overlay'>";
                echo "<a class='delete-btn' href='?delete=$name' onclick='return confirm(\"Are you sure you want to delete this photo?\")'>Delete</a>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p class='empty-state'>No photos uploaded yet.</p>";
        }
        ?>
    </div>
</div>

<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('file-input');
    const form = document.getElementById('uploadForm');

    // 1. Click to trigger file selection
    dropZone.addEventListener('click', () => fileInput.click());

    // 2. Auto-submit when file is selected via click
    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            form.submit();
        }
    });

    // 3. Prevent default browser behaviors for drag events
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    // 4. Add visual feedback when dragging over
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.add('highlight');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, () => {
            dropZone.classList.remove('highlight');
        }, false);
    });

    // 5. Handle dropped files
    dropZone.addEventListener('drop', (e) => {
        let dt = e.dataTransfer;
        let files = dt.files;

        if (files.length > 0) {
            // Assign dropped files to the hidden input
            fileInput.files = files;
            // Submit form automatically
            form.submit();
        }
    });
</script>

</body>
</html>