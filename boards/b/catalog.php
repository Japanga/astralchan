

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Thread #<?php echo htmlspecialchars($postId); ?></title>
    <link rel="stylesheet" href="styles.css">
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
// Load and decode JSON data
$json_data = file_get_contents('posts.json');
$posts = json_decode($json_data, true);

  function getReplyCount($postId) {
    // Sanitize ID to prevent path traversal
    $safeId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $postId);
    $filePath = "replies/" . $safeId . ".json";

    // Check if file exists
    if (!file_exists($filePath)) {
        return 0;
    }

    // Read and decode JSON
    $jsonData = file_get_contents($filePath);
    $replies = json_decode($jsonData, true); // true for associative array [1]

    // Count items safely
    return is_array($replies) ? count($replies) : 0;
}
function countValidImagePaths($post_id) {
    $file_path = __DIR__ . "/replies/" . $post_id . ".json";
    
    // Check if file exists
    if (!file_exists($file_path)) {
        return 0;
    }

    // Read and decode JSON
    $json_data = file_get_contents($file_path);
    $replies = json_decode($json_data, true); // true for associative array

    if (empty($replies) || !is_array($replies)) {
        return 0;
    }

    // Filter array to only include entries where image_path is set, 
    // not null, and not empty
    $valid_images = array_filter($replies, function($reply) {
        return isset($reply['image_path']) && !empty($reply['image_path']);
    });

    return count($valid_images);
}
function time_ago_in_days($timestamp) {
    // If input is not a timestamp, try to convert it (e.g., from a date string)
    if (!is_numeric($timestamp)) {
        $timestamp = strtotime($timestamp);
    }

    $current_time = new DateTime();
    $past_time = new DateTime();
    $past_time->setTimestamp($timestamp);

    $interval = $current_time->diff($past_time);

    // Get the total number of days
    $days = $interval->days;

    if ($days === 0) {
        return "today";
    } elseif ($days === 1) {
        return "1 day ago";
    } else {
        return $days . " days ago";
    }
}


function getLatestReplyTimestamp($postId) {
    $filePath = __DIR__ . "/replies/{$postId}.json";

    if (file_exists($filePath)) {
        // filemtime returns the Unix timestamp of last modification
        return filemtime($filePath); 
    } else {
        return false; // File not found
    }
}

?>
    <div class = 'replyform'>
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

 <a href="index.php">Back to main board</a>

        
  

   <div class="grid-container">
    <?php foreach ($posts as $post): ?>
        <div class="post-item">
            <a href="thread.php?id=<?php echo $post['id']; ?>">
                <img src="<?php echo $post['image_path']; ?>" alt="Post Image">
                <div class="overlay">
                    <p>by <?php echo $post['username']; ?> <em><?php echo time_ago_in_days($post['timestamp']); ?></em></p>
                    <?php 
                    $timestamp = getLatestReplyTimestamp($post['id']);
                    echo 'Latest reply: ' . $timestampconverted = time_ago_in_days($timestamp);
                    ?>
                </div>
                 </a>
                 <p>R: <?php echo getReplyCount($post['id']); ?> I: <?php echo countValidImagePaths($post['id']); ?></p>
             <strong><?php echo htmlspecialchars($post['title']); ?></strong>
               <p><?php echo $post['description']; ?></p>
           
        </div>
    <?php endforeach; ?>
</div>

    
       <style>
  .grid-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; padding: 15px; }
    .post-item { position: relative; overflow: hidden; border: 1px solid #ccc; }
    .post-item img { width: 100%; height: 200px; object-fit: contain; display: block; }
    
    /* Hover Overlay */
    .overlay {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: rgba(0, 0, 0, 0.7); color: white;
        padding: 10px; opacity: 0; transition: opacity 0.3s;
        font-size: 12px;
    }
    .post-item:hover .overlay { opacity: 1; }
   
.container {
    background-color: var(--bg-color);
    color: var(--text-color);
    padding: 20px;
    border: 1px solid #ccc;
    margin: 50px;
}

.form-popup {
    display: none; /* Hidden by default */
    position: fixed;
    bottom: 20px;
    right: 20px;
    border: 3px solid #f1f1f1;
    z-index: 9;
}

.form-container {
    max-width: 300px;
    padding: 10px;
    background-color: white;
}

.form-container input[type=color] {
    width: 100%;
    padding: 5px;
    margin: 5px 0 20px 0;
    border: none;
    cursor: pointer;
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
   .header-bg {
            /* Use the PHP variable to set the background image URL */
            background-image: url('<?php echo $randomImageUrl; ?>');
            background-size: cover; /* Optional: ensures the image covers the entire header area */
            background-position: center; /* Optional: centers the image */
            height: 160px; /* Optional: set a height for the header */
            width: 440px;
             border: 1px solid black;
        }
   .header-bg {
            /* Use the PHP variable to set the background image URL */
            background-image: url('<?php echo $randomImageUrl; ?>');
            background-size: cover; /* Optional: ensures the image covers the entire header area */
            background-position: center; /* Optional: centers the image */
            height: 160px; /* Optional: set a height for the header */
            width: 440px;
             border: 1px solid black;
        }
               
         gt {
 color:green;
         
}
 bm {
 color:red;
  font-weight: bold; /* Makes the text bold (equivalent to 700) */
  font-style: italic;
}
 .Mod {
 color:purple;
  font-weight: bold; /* Makes the text bold (equivalent to 700) */
}
.Developer {
 color:blue;
  font-weight: bold; /* Makes the text bold (equivalent to 700) */
}
   tc {
 color:green;
 text-decoration: underline;
}
        st {
font-size: 0.8em; /* Makes the text size 80% of its parent element's font size */
    color: grey;
         
}
       </style>
</body>
</html>
