<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AstralChan</title>
    <style>
        .gallery-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            padding: 20px;
        }
        .gallery-item img {
            width: 100%;
            height: 100%; /* Fixed height for consistent thumbnails */
            object-fit: cover; /* Ensures images cover the area without stretching */
            border: 1px solid #ccc;
            padding: 5px;
            box-sizing: border-box;
        }
        body {
  /* Fallback color */
  background: #D5DEE7; 
  /* Ensures the gradient covers the entire page */
  height: 100vh; 
  margin: 0;
  background-attachment: fixed; /* Prevents the gradient from repeating if content is short */
}
  .horizontal-list {
  display: flex;
  list-style: none; /* Removes bullet points */
  padding: 0;      /* Removes default indentation */
  margin: 0;
  font-size: 23px;
}

.horizontal-list li {
  margin-right: 20px; /* Adds space between items */
}
 
    </style>
</head>
<body>



<?php
// Define the path to the random_images folder relative to the root directory
$imagesDir = $_SERVER['DOCUMENT_ROOT'] . '/random_images/';

// Get all image files with common extensions (jpg, jpeg, png, gif)
// GLOB_BRACE allows multiple extensions
$images = glob($imagesDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

if ($images) {
    // Select a random image from the array
    $randomImage = $images[array_rand($images)];

    // Get the relative path to the image for the HTML src/url attribute
    // We remove the DOCUMENT_ROOT part from the full path
    $randomImageUrl = str_replace($_SERVER['DOCUMENT_ROOT'], '', $randomImage);
} else {
    $randomImageUrl = ''; // Fallback or handle error if no images found
}
?>
   <header class="header-bg">
        <!-- Your header content goes here -->
         <span style="color: white;"><h1>Welcome to AstralChan</h1></span>
    </header>
    
<div class="boards-list">
    <ul class="horizontal-list">
        <?php
        // Path to the boards folder from the root
        $boardsDir = $_SERVER['DOCUMENT_ROOT'] . '/boards/*';
        
        // Find all directories within the boards folder
        $folders = glob($boardsDir, GLOB_ONLYDIR);
        
        if ($folders) {
            foreach ($folders as $folder) {
                // Get the folder name for display
                $folderName = basename($folder);
                // Create relative link
                echo "<li><a href='/boards/$folderName'>/$folderName/</a></li>";
            }
        } else {
            echo "<li>No boards found.</li>";
        }
        ?>
    </ul>
</div>

    <h1>Catalog</h1>
    <button onclick="window.location.href='index.php'">Return to index view</button>
<hr>
    <div class="gallery-container">
        <?php
        // Define the directory path
        $images_dir = 'uploads/'; 
        
        // Use glob to find all files with specified image extensions (jpg, jpeg, png, gif)
        // GLOB_BRACE allows searching for multiple patterns
        $image_files = glob($images_dir . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF}', GLOB_BRACE);

        // Check if any images were found
        if ($image_files) {
            foreach ($image_files as $image) {
                // Output each image within a gallery item div
                echo '<div class="gallery-item">';
                echo '<img src="' . htmlspecialchars($image) . '" alt="Gallery Image">';
                echo '</div>';
            }
        } else {
            echo '<p>No images found in the upload/section directory.</p>';
        }
        ?>
    </div>
 <style>
   .header-bg {
            /* Use the PHP variable to set the background image URL */
            background-image: url('<?php echo $randomImageUrl; ?>');
            background-size: cover; /* Optional: ensures the image covers the entire header area */
            background-position: center; /* Optional: centers the image */
            height: 160px; /* Optional: set a height for the header */
            width: 440px;
             border: 1px solid black;
        }
 </style>
</body>
</html>