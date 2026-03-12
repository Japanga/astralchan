<?php
$postId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$postId) die('Invalid post ID.');

// Function to find a specific post
function findPost($id) {
    $posts = json_decode(file_get_contents('posts.json'), true) ?: [];
    foreach ($posts as $post) {
        if ($post['id'] == $id) return $post;
    }
    return null;
}

// Function to get replies for a post
function getReplies($postId) {
    $repliesFile = 'replies/' . $postId . '.json';
    if (file_exists($repliesFile)) {
        $data = file_get_contents($repliesFile);
        return json_decode($data, true) ?: [];
    }
    return [];
}

$post = findPost($postId);
if (!$post) die('Post not found.');

// Handle new reply submission
if (isset($_POST['submit_reply'])) {
    require_once 'upload_reply.php';
    handleReplyUpload($postId);
}
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);

    $bytes /= pow(1024, $pow);

    return round($bytes, $precision) . ' ' . $units[$pow];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Thread #<?php echo htmlspecialchars($postId); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php
// Get the file creation time
$file_created = date("F d, Y H:i:s", filectime(__FILE__));
// Get the referrer to return to
$referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
?>

<!-- Overlay HTML -->
<div class="overlay-bar">
    <span>Created on: <?php echo $file_created; ?></span>
    <a href="<?php echo $referrer; ?>" class="return-link">Return to Normal</a>
</div>

<!-- CSS for the Overlay -->
<style>
.overlay-bar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
   background-color: rgba(0, 0, 0, 0.5);
    color: white;
    text-align: center;
    padding: 10px 0;
    font-family: sans-serif;
    z-index: 9999;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
}
.overlay-bar a {
    color: #4CAF50;
    margin-left: 20px;
    text-decoration: none;
    font-weight: bold;
}
body { padding-top: 40px; } /* Prevent overlay from covering content */
</style>


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

<script>
        // Simple client-side SHA256 simulation for demonstration
        // In production, use crypto.subtle.digest for better security
        async function generateTripcode() {
            const password = document.getElementById('password').value;
            if (!password) {
                document.getElementById('tripcode').value = '';
                return;
            }

            // Using SubtleCrypto API for SHA-256
            const msgBuffer = new TextEncoder().encode(password);
            const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
            const hashArray = Array.from(new Uint8Array(hashBuffer));
            // Convert to base64, slice to get a 10-char string
            const hashBase64 = btoa(String.fromCharCode(...hashArray));
            
            // Generate a 10 character tripcode
            const tripcode = '!' + hashBase64.substring(0, 8);
            document.getElementById('tripcode').value = tripcode;
        }
    </script>
    
    </div>
    <div class = 'replyform'>
    <a href="index.php">Back to main board</a>
        </div>
    <h1>Thread #<?php echo htmlspecialchars($postId); ?></h1>

    <!-- Display original post -->
    <div class='original-post'>
        <h3><gt> <?php echo htmlspecialchars($post['username']); ?> </gt></b> <tc> <?php echo htmlspecialchars($post['tripcode']); ?> </tc> Post ID: <?php echo htmlspecialchars($post['id']); ?></h3>
         <h3>Title: <?php echo htmlspecialchars($post['title']); ?></h3>
        <p><?php echo htmlspecialchars($post['description']); ?></p>
           <?php echo $replies = findPostReplies($post['id'], $post['id']);?>
         <?php echo "<p>" . $replies . "</p>";?>
        <img src='<?php echo htmlspecialchars($post['image_path']); ?>' width='400'><br>
    <div class = 'replyform'>
                <button onclick="setReplyId('<?php echo htmlspecialchars($post['id']); ?>')">Reply to # </button>
        </div>
        <small>Uploaded: <?php echo htmlspecialchars($post['timestamp']); ?> | Size: <?php echo formatBytes($post['file_size']); ?> </small>
    </div>





    <hr>
    <h2>Replies</h2>

    <!-- Form for adding a reply -->
    <div class="replyform">
    <form action="thread.php?id=<?php echo htmlspecialchars($postId); ?>" method="POST" enctype="multipart/form-data">
            <div class="container2">
        <label for="description">Tripcode:</label><br>
        <input type="password" id="password" placeholder="Enter password" oninput="generateTripcode()">
        <input type="text" name="content4" id="tripcode" placeholder="Generated Tripcode" readonly>
        </div>
        <label for="reply_text">Name:</label><br>
        <textarea name="username" id="username" cols="20">Anonymous</textarea><br>
        <label for="reply_text">Reply:</label><br>
        <textarea name="reply_text" id="reply_text" rows="2" cols="50"></textarea><br>
        <label for="reply_image">Upload Image (optional):</label>
        <input type="file" name="reply_image" id="reply_image" accept="image/*"><br>
        <button type="submit" name="submit_reply">Reply</button>
    </form>
    
    
 <button class="open-button" onclick="openForm()">Change Layout</button>
 </div>
   
   <!-- The Popup Form -->
    <div class="form-popup" id="myForm">
        <form class="form-container">
            <h2>Change Colors</h2>
            <label for="bgColor"><b>Background Color:</b></label>
            <input type="color" id="bgColor" name="bgColor" value="#ffffff">

            <label for="textColor"><b>Text Color:</b></label>
            <input type="color" id="textColor" name="textColor">

            <button type="button" class="btn" onclick="applyChanges()">Apply</button>
            <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
        </form>
    </div>

    <script>
  // Function to open the pop-up form
function openForm() {
    document.getElementById("myForm").style.display = "block";
}

// Function to close the pop-up form
function closeForm() {
    document.getElementById("myForm").style.display = "none";
}

// Function to apply changes from the form to the CSS variables
function applyChanges() {
    const bgColor = document.getElementById("bgColor").value;
    const textColor = document.getElementById("textColor").value;
    
    // Get the root element to set the CSS variables
    const root = document.documentElement;

    // Set the new values for the CSS variables
    document.body.style.background = bgColor;
     document.body.style.color = textColor;

    // Optional: close the form after applying changes
    closeForm();
}

  </script>

    <hr>
    <!-- Display replies -->
    <div class="replies-section">
    <?php
    $replies = getReplies($postId);
    foreach ($replies as $reply) {
        echo "<div class='reply' id='" . $reply['id'] . "'>";
    echo '<div class="reply-header">';
     echo '<div class="dropdown">';
echo '  <button onclick="toggleDropdown(' . ($reply['id']) . ')" class="dropbtn">...</button>';
echo '  <div id="dropdownContent' . ($reply['id']) . '" class="dropdown-content">';
 echo '  <button onclick="document.getElementById(\'' . $reply['id'] . '\').style.display = \'none\';">Hide this post</button>';
echo '  </div>';
        echo '  </div>';
        
echo '<script>
    function toggleDropdown(id) {
        document.getElementById("dropdownContent" + id).classList.toggle("show");
    }
    function action(type, id) {
        alert(type + " clicked for ID: " + id);
        // Close menu after action
        document.getElementById("dropdownContent" + id).classList.remove("show");
    }
    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches(".dropbtn")) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains("show")) {
                    openDropdown.classList.remove("show");
                }
            }
        }
    }
</script>';
         echo "<div class = 'title-sect'>";
        echo "<b><p><gt>" . $reply['username'] . "</gt></b> <tc>" . htmlspecialchars($reply['tripcode']) ."</tc> Post ID: #" . ($reply['id']) . "</p></b>";
          echo "</div>";
          echo "</div>";
        echo $replies = findPostReplies($post['id'], $reply['id']);
        echo "<p>" . $replies . "</p>";
        echo "<p>" . $reply['text'] . "</p>";        if ($reply['image_path']) {
            echo "<img src='" . htmlspecialchars($reply['image_path']) . "' width='200'><br>";
         
        }
        $replyId = htmlspecialchars($reply['id'], ENT_QUOTES, 'UTF-8');
        
echo " <hiddenr><button onclick=\"setReplyId('$replyId')\">Reply to # </button></hiddenr>";
        echo "<small>Uploaded: " . htmlspecialchars($reply['timestamp']) . " | Size: " . formatBytes($reply['file_size']) . "</small>";
        echo "</div><hr>";
    }
function findPostReplies($postId, $targetId) {
    $filename = "replies/" . $postId . ".json";

    if (file_exists($filename)) {
        
        $jsonContent = file_get_contents($filename);
        $replies = json_decode($jsonContent, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($replies)) {
            $found = false;

            // Assuming JSON structure is an array of objects/arrays
            foreach ($replies as $reply) {
                // Check if targetId is in the text (case-insensitive check)
                if (isset($reply['text']) && stripos($reply['text'], $targetId) !== false) {
                    echo "<i><st>Replies: #{$reply['id']}\n</st></i>";
                    $found = true;
                }
            }
            
            if (!$found) {
                echo "";
            }
        } else {
            echo "Error: Failed to parse JSON or invalid format.\n";
        }
    } else {
        echo "Error: File not found for post $postId.\n";
    }
}

// Example usage:
// Create a folder named 'replies' and a file named '123.json' with sample data
// findPostReplies('123', 'postA');
?>

    </div>
    
     <script>
function setReplyId(id) {
    document.getElementById('reply_text').value = "#" + id;
}
  </script>
  
  
 <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Select elements to check (e.g., all divs or a specific container)
            const lines = document.querySelectorAll("p");
            
            lines.forEach(line => {
                if (line.textContent.trim().startsWith(">")) {
                    line.classList.add("greentext");
                }
            });
        });
    </script>
    
<script src="tagging.js"></script>
    
<script src="spoilers.js"></script>


    <script>
  window.addEventListener('pageshow', function(event) {
    // Check if the page was loaded from the bfcache
    if (event.persisted) {
      clearForms();
    }
    // Also run on normal page loads to ensure consistency
    clearForms();
  });

  function clearForms() {
    var forms = document.querySelectorAll('form');
    forms.forEach(function(form) {
      form.reset(); // The built-in HTML form reset method
    });

    // For inputs not within a formal <form> tag, you can use selectors:
    // document.querySelectorAll('input, textarea, select').forEach(function(el) {
    //   if (el.type === 'checkbox' || el.type === 'radio') {
    //     el.checked = false;
    //   } else {
    //     el.value = '';
    //   }
    // });
  }
</script>
    
       <style>
           body {  background-image: linear-gradient(to bottom, #444444 0%, #555555 10%, #222222 10%, #333333 40%, #111111 100%);
  color: #D6D6D6;
  /* Ensures the gradient covers the entire page */
  height: 100vh; 
  margin: 0;
  background-attachment: fixed;
  font-family: Arial, sans-serif; } .snapshot-header { position: fixed; top: 0; left: 0; width: 100%; background: rgba(0,0,0,0.7); color: white; padding: 10px; z-index: 9999; text-align: center; }  .replyform {display:none;}  hiddenr {display:none;} 
             .greentext { color: green; }
    .reply-header{
           background-image: linear-gradient(135deg, #2b2b2b 0%, #060606 100%);
           padding:2px;
           width:400px;
           border: 2px solid rgb(107, 107, 107);
 border-radius: 15px;
         }
         .title-sect{
              margin-left: 30px;
  margin-top: -40px;
         }
               
   
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
.Admin {
 color:red;
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
            .spoiler {
            background-color: black;
            color: black;
            cursor: pointer; /* Indicates the text is interactive */
            transition: color 0.3s, background-color 0.3s;
        }

        /* Style for when the spoiler is hovered over or clicked (revealed) */
        .spoiler:hover, .spoiler.revealed {
            background-color: transparent; /* Makes background transparent */
            color: initial; /* Reverts text color to default page color */
        }
 .dropdown { position: relative; display: inline-block; }
    .dropbtn { background-color: rgb(59, 59, 59); color: black; padding: 3px;  border: 1px solid black; cursor: pointer; font-weight: bold; font-size:14px; }  
    .dropdown-content { display: none; position: absolute; background-color: #f9f9f9; min-width: 160px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; }
    .dropdown-content button { color: black; padding: 12px 16px; text-decoration: none; display: block; border: none; background: none; width: 100%; text-align: left; }
    .dropdown-content button:hover { background-color: #f1f1f1; }
    .show { display: block; }
       </style>
</body>
</html>