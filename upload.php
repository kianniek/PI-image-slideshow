<?php
$target_dir = "photos/";
if (!file_exists($target_dir)) { mkdir($target_dir, 0775, true); }

// 1. Handle File Uploads
if(isset($_FILES['image'])){
    $files = $_FILES['image'];
    $count = count($files['name']);
    for($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] === 0) {
            $file_name = time() . "_" . basename($files['name'][$i]); // Added timestamp to prevent overwriting
            $target_file = $target_dir . $file_name;
            move_uploaded_file($files['tmp_name'][$i], $target_file);
        }
    }
    header("Location: upload.php"); // Refresh to clear POST data
    exit;
}

// 2. Handle Deletions
if(isset($_GET['delete'])){
    $file_to_delete = $target_dir . basename($_GET['delete']);
    if(file_exists($file_to_delete)) { unlink($file_to_delete); }
    header("Location: upload.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: system-ui, sans-serif; padding: 15px; background: #eee; }
        .card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 20px; }
        #drop-area { border: 2px dashed #ccc; border-radius: 10px; padding: 20px; text-align: center; transition: 0.3s; cursor: pointer; }
        #drop-area.highlight { border-color: #007bff; background: #e7f1ff; }
        .photo-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #eee; background: white; }
        .delete-btn { color: white; background: #ff4444; padding: 8px 12px; text-decoration: none; border-radius: 6px; font-weight: bold; }
        .btn-primary { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 6px; width: 100%; font-size: 16px; margin-top: 10px; }
        img.preview { width: 60px; height: 60px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>

    <div class="card">
        <h2>Upload Photos</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <div id="drop-area" onclick="document.getElementById('fileElem').click()">
                <p>Tap to select or Drag & Drop</p>
                <input type="file" id="fileElem" name="image[]" multiple accept="image/*" style="display:none" onchange="handleFiles(this.files)">
                <div id="gallery"></div>
            </div>
            <button type="submit" class="btn-primary">Start Upload</button>
        </form>
        <p style="text-align:center;"><a href="index.php">View Slideshow</a></p>
    </div>

    <h3>Manage Library</h3>
    <?php
    $files = glob($target_dir . "*.{jpg,png,gif,jpeg}", GLOB_BRACE);
    foreach($files as $file) {
        $name = basename($file);
        echo "<div class='photo-item'>";
        echo "<span><img src='$file' class='preview' style='vertical-align:middle; margin-right:10px;'> $name</span>";
        echo "<a class='delete-btn' href='?delete=$name' onclick='return confirm(\"Delete?\")'>Delete</a>";
        echo "</div>";
    }
    ?>

    <script>
        let dropArea = document.getElementById('drop-area');
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(e => dropArea.addEventListener(e, (ev) => { ev.preventDefault(); ev.stopPropagation(); }));
        ['dragenter', 'dragover'].forEach(e => dropArea.addEventListener(e, () => dropArea.classList.add('highlight')));
        ['dragleave', 'drop'].forEach(e => dropArea.addEventListener(e, () => dropArea.classList.remove('highlight')));

        function handleFiles(files) {
            let gallery = document.getElementById('gallery');
            gallery.innerHTML = "";
            [...files].forEach(file => {
                let reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onloadend = () => {
                    let img = document.createElement('img');
                    img.src = reader.result;
                    img.className = "preview";
                    img.style.margin = "5px";
                    gallery.appendChild(img);
                }
            });
        }
    </script>
</body>
</html>