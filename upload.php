<?php

$target_dir = "photos/";


// 1. Handle File Uploads (support multiple files)
if(isset($_FILES['image'])){
    $allowed = array("jpg", "jpeg", "png", "gif");
    $files = $_FILES['image'];
    $count = is_array($files['name']) ? count($files['name']) : 1;
    $success = 0;
    $fail = 0;
    $messages = [];
    for($i = 0; $i < $count; $i++) {
        $file_name = is_array($files['name']) ? basename($files['name'][$i]) : basename($files['name']);
        $tmp_name = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if(in_array($imageFileType, $allowed)) {
            if(move_uploaded_file($tmp_name, $target_file)) {
                $success++;
                $messages[] = "<span style='color:green;'>Uploaded: $file_name</span>";
            } else {
                $fail++;
                $messages[] = "<span style='color:red;'>Failed: $file_name</span>";
            }
        } else {
            $fail++;
            $messages[] = "<span style='color:red;'>Invalid type: $file_name</span>";
        }
    }
    if($success > 0) echo "<p>" . implode("<br>", $messages) . "</p>";
    else echo "<p>" . implode("<br>", $messages) . "</p>";
}

// 2. Handle Deletions
if(isset($_GET['delete'])){
    $file_to_delete = $target_dir . basename($_GET['delete']);
    if(file_exists($file_to_delete)) {
        unlink($file_to_delete);
        echo "<p style='color:orange;'>Deleted: " . $_GET['delete'] . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: sans-serif; padding: 20px; line-height: 1.6; }
        .photo-item { 
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px; border-bottom: 1px solid #ddd; 
        }
        .delete-btn { color: white; background: #ff4444; padding: 5px 10px; text-decoration: none; border-radius: 5px; }
        .upload-section { background: #f4f4f4; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
        img { border-radius: 5px; }
    </style>
</head>
<body>


    <div class="upload-section">
        <h2>Upload Photos</h2>
        <form id="uploadForm" action="" method="POST" enctype="multipart/form-data">
            <div id="drop-area">
                <p>Drag & drop images here or click to select</p>
                <input type="file" id="fileElem" name="image[]" multiple accept="image/*" style="display:none" required>
                <button type="button" id="fileSelectBtn">Select Images</button>
                <div id="gallery"></div>
            </div>
            <input type="submit" value="Upload" style="margin-top:15px;">
        </form>
        <br>
        <a href="index.php">← Back to Slideshow</a>
    </div>


    <h2>Current Photos</h2>
    <?php
    $files = glob($target_dir . "*.{jpg,png,gif,jpeg}", GLOB_BRACE);
    foreach($files as $file) {
        $name = basename($file);
        echo "<div class='photo-item'>";
        echo "<span><img src='$file' width='50' height='50' style='object-fit:cover; vertical-align:middle; margin-right:10px;'> $name</span>";
        echo "<a class='delete-btn' href='?delete=$name' onclick='return confirm(\"Delete this photo?\")'>Delete</a>";
        echo "</div>";
    }
    ?>

</body>
<script>
// Drag & Drop and Preview logic
const dropArea = document.getElementById('drop-area');
const fileElem = document.getElementById('fileElem');
const fileSelectBtn = document.getElementById('fileSelectBtn');
const gallery = document.getElementById('gallery');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false)
});

function preventDefaults (e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, () => dropArea.classList.add('highlight'), false)
});
['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, () => dropArea.classList.remove('highlight'), false)
});

dropArea.addEventListener('drop', handleDrop, false);
fileSelectBtn.addEventListener('click', () => fileElem.click());
fileElem.addEventListener('change', updateGallery);

function handleDrop(e) {
    let dt = e.dataTransfer;
    let files = dt.files;
    fileElem.files = files;
    updateGallery();
}

function updateGallery() {
    gallery.innerHTML = '';
    const files = fileElem.files;
    if (!files.length) return;
    for (let i = 0; i < files.length; i++) {
        let file = files[i];
        if (!file.type.startsWith('image/')) continue;
        let reader = new FileReader();
        reader.onload = (e) => {
            let img = document.createElement('img');
            img.src = e.target.result;
            img.style.width = '60px';
            img.style.height = '60px';
            img.style.objectFit = 'cover';
            img.style.margin = '5px';
            gallery.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
}
</script>
</html>
