<?php
$target_dir = "photos/";

// 1. Handle File Uploads
if(isset($_FILES['image'])){
    $file_name = time() . "_" . basename($_FILES['image']['name']);
    $target_file = $target_dir . $file_name;
    
    // Basic check: only allow common image formats
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed = array("jpg", "jpeg", "png", "gif");

    if(in_array($imageFileType, $allowed)) {
        if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            echo "<p style='color:green;'>Uploaded: $file_name</p>";
        }
    } else {
        echo "<p style='color:red;'>Error: Only JPG, PNG & GIF allowed.</p>";
    }
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
        <h2>Upload Photo</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="file" name="image" required>
            <input type="submit" value="Upload">
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
</html>
