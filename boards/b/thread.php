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
    
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    
</head>
<body>
<?php
// --- Configuration ---
$threadId = $_GET['id'] ?? 'unknown'; // Get static ID from URL
$archiveBase = 'archives/';
$targetFolder = $archiveBase . $threadId;

// Files/Folders to backup
$filesToBackup = ['thread_archive.php', 'posts.json', 'uploads', 'replies'];

// --- Archiving Function ---
function archiveThread($files, $dest) {
    if (!file_exists($dest)) {
        mkdir($dest, 0777, true);
    }

    foreach ($files as $file) {
        if (file_exists($file)) {
            if (is_dir($file)) {
                // Recursive copy for directories
                $dirDest = $dest . '/' . $file;
                mkdir($dirDest, 0777, true);
                
                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($file, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );
                foreach ($iterator as $item) {
                    if ($item->isDir()) {
                        mkdir($dirDest . '/' . $iterator->getSubPathName(), 0777, true);
                    } else {
                        copy($item, $dirDest . '/' . $iterator->getSubPathName());
                    }
                }
            } else {
                // Copy files
                copy($file, $dest . '/' . basename($file));
            }
        }
    }
    
    function getUrlIdToString($key = 'id') {
    // 1. Check if the parameter exists in the URL (e.g., ?id=123)
    if (isset($_GET[$key])) {
        // 2. Sanitize to ensure it's an integer
        $id = intval($_GET[$key]);
        // 3. Convert to string and return
        return (string)$id;
    }
    return null; // Return null if ID is not in URL
}

    $idString = getUrlIdToString('id');
    $timestamp = date('Y-m-d H-i-s');
        $url = $dest . '/' . 'thread_archive.php?id=' . $idString;
$link_text = "Visit archived thread";
       echo '<img src="https://i.imgur.com/IOUkjLB.png" alt="Description of image" / style="width:41px; height:41px;">';
    return "<st><i>Archived automatically at $timestamp to: $dest <a href='$url'>$link_text</a></i></st>";
}

// --- Execute ---
if ($threadId !== 'unknown') {
    echo archiveThread($filesToBackup, $targetFolder);
} else {
    echo "No thread ID provided.";
}
?>





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


    
    </div>
    
    <a href="index.php">Back to main board</a>
    <h1>Thread #<?php echo htmlspecialchars($postId); ?></h1>

    <!-- Display original post -->
    <div class='original-post'>
        <h3><gt> <?php echo htmlspecialchars($post['username']); ?> </gt></b> <tc> <?php echo htmlspecialchars($post['tripcode']); ?> </tc> Post ID: <?php echo htmlspecialchars($post['id']); ?></h3>
         <h3>Title: <?php echo htmlspecialchars($post['title']); ?></h3>
        <p><?php echo "<p>" . $post['description'] . "</p>" ?></p>
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

    <div id="mydiv" class="draggable-window">
    <div id="mydivheader" class="window-header">Reply to Thread No. <?php echo htmlspecialchars($postId); ?></div>
    <div class="replyform">
    <form action="thread.php?id=<?php echo htmlspecialchars($postId); ?>" method="POST" enctype="multipart/form-data">
 
        <label for="reply_text">Name:</label><br>
        <textarea name="username" id="username" cols="20">Anonymous</textarea><br>
        <label for="reply_text">Reply:</label><br>
        <textarea name="reply_text" id="reply_text" rows="2" cols="30"></textarea><br>
        <label for="reply_image">Upload Image (optional):</label>
        <input type="file" name="reply_image" id="reply_image" accept="image/*"><br>     
            <label>
        <input type="checkbox" name="is_spoiler" value="1">
        [Spoiler?]
    </label><br>
        <button type="submit" name="submit_reply" id="submitbutton" disabled>Reply</button>
    </form>
</div>
</div>
           
        <script>
  function enablePosts() {

  const button = document.getElementById("submitbutton");

  // Enable the button by setting the disabled property to false
  button.disabled = false;
  }
   
</script>
               <?php
    // 1. Get the user's IP address
$ip = $_SERVER['REMOTE_ADDR'];

// 2. Create a unique hash based on the IP (and optional salt for extra security)
$hash = substr(hash('sha256', $ip . 'optional_salt'), 0, 16);

// 3. Create a "shadowmask" (e.g., mask part of the IP)
// This masks the last two octets of an IPv4 address
$maskedIp = preg_replace('/(\d+)\.(\d+)\.(\d+)\.(\d+)/', '$1.$2.XXX.XXX', $ip);

// 4. Combine mask and hash for the final ID
$shadowMaskId = "ID-" . $maskedIp . "-" . strtoupper($hash);

$banFile = $_SERVER['DOCUMENT_ROOT'] . '/banlist/banned.json';
$message = "";
$isBanned = false;

// Handle button click
if (isset($_POST['check_ban'])) {
    if (file_exists($banFile)) {
        $jsonContent = file_get_contents($banFile);
        $bannedUsers = json_decode($jsonContent, true);

        // Check if ID exists in JSON
        if (isset($bannedUsers[$shadowMaskId])) {
            $isBanned = true;
            $data = $bannedUsers[$shadowMaskId];
               $url = "banned.php";
$link_text = "You are banned.";
            $message = "<div style='color: white; background-color: red; padding: 10px; border-radius: 5px;'>
            
                           <a href='$url' style='color:white;'>$link_text</a><br>
                         
                        </div>";
        } else {
            $message = "<div style='color: white; background-color: green; padding: 10px; border-radius: 5px;'>
                           You are not banned!
                        </div>";
            echo '<script>';
// Pass PHP data to JavaScript using json_encode for safety
echo 'enablePosts();';
echo '</script>';
        }
    } else {
        $message = "Error: Ban list not found.";
    }
}
?>
  <form method="post">
        <button type="submit" name="check_ban" id="banbutton">Check Ban Status</button>
    </form>

    <br>
    <?php echo $message; ?>

 
 <button class="open-button" onclick="openForm()">Change Layout</button>
   
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
        
        function check_spoiler_status($post_id, $reply_id) {
    // Define the file path
    $file_path = 'replies/' . $post_id . '.json';

    // 1. Read the JSON file content into a string
    if (!file_exists($file_path)) {
        echo "Error: JSON file not found for post ID $post_id";
        return false;
    }
    $json_string = file_get_contents($file_path);
    if ($json_string === false) {
        echo "Error: Could not read the JSON file";
        return false;
    }

    // 2. Decode the JSON string into a PHP associative array
    $data = json_decode($json_string, true);
    if ($data === null) {
        echo "Error: Could not decode the JSON data";
        return false;
    }

    // 3. Find the specific reply by ID
    $found_reply = null;
    // Assuming the JSON structure is an array of reply objects
    foreach ($data as $reply) {
        // You would need to know the key for the reply ID in your JSON structure (e.g., 'id')
        if (isset($reply['id']) && $reply['id'] == $reply_id) {
            $found_reply = $reply;
            break; // Stop searching once found
        }
    }

    // 4. Check if the reply was found and if "spoilerStatus" is 1
    if ($found_reply !== null) {
        // Use isset() to ensure the key exists before checking the value
        if (isset($found_reply['spoilerStatus']) && $found_reply['spoilerStatus'] == 1) {
            // Perform the function/action here
            echo '<div class="spoiler-container">';
             echo "<img src='" . htmlspecialchars($reply['image_path']) . "' width='200'><br>";
             echo '<img src="https://i.imgur.com/xBUBByL.png" alt="Top Image" class="image top-image">';
    echo '</div>';
               echo '</div>';
            return true;
        } else {
            echo "<img src='" . htmlspecialchars($reply['image_path']) . "' width='200'><br>";
            return false;
        }
    } else {
        echo "Reply ID $reply_id not found in the file";
        return false;
    }
}

// A placeholder function to be called when the condition is met
function perform_spoiler_action($reply_id) {
   
}
   

    foreach ($replies as $reply) {
        echo "<div class='reply' id='" . $reply['id'] . "'>";
    echo '<div class="reply-header">';
     echo '<div class="dropdown">';
echo '  <button onclick="toggleDropdown(' . ($reply['id']) . ')" class="dropbtn">...</button>';
echo '  <div id="dropdownContent' . ($reply['id']) . '" class="dropdown-content">';
 echo '  <button onclick="document.getElementById(\'' . $reply['id'] . '\').style.display = \'none\';">Hide this post</button>';
 echo '<button><a href="report_form.php?post_id=' . $reply['id'] . '">Report post</a></button>'; 
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
        echo "<p>" . $reply['text'] . "</p>";       if ($reply['image_path']) {  
             check_spoiler_status($post['id'], $reply['id']);
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
// Example JS function to make element draggable
function dragElement(elmnt) {
    let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
    const header = document.getElementById(elmnt.id + "header");
    if (header) header.onmousedown = dragMouseDown;
    else elmnt.onmousedown = dragMouseDown;

    function dragMouseDown(e) {
        e.preventDefault();
        pos3 = e.clientX; pos4 = e.clientY;
        document.onmouseup = closeDragElement;
        document.onmousemove = elementDrag;
    }

    function elementDrag(e) {
        e.preventDefault();
        pos1 = pos3 - e.clientX; pos2 = pos4 - e.clientY;
        pos3 = e.clientX; pos4 = e.clientY;
        elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
        elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
    }

    function closeDragElement() {
        document.onmouseup = null; document.onmousemove = null;
    }
}
dragElement(document.getElementById("mydiv"));

    </script>
  
      <script> 
function generateTripcode() {
  // 1. Simple consistent hashing function
  const hashCode = (str) => {
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
      hash = ((hash << 5) - hash) + str.charCodeAt(i);
      hash |= 0; // Convert to 32bit integer
    }
    // Convert to a mix of alphanumeric and special characters
    return Math.abs(hash).toString(36) + "x" + (hash % 1000).toString(16);
  };

  // 2. Select all paragraph elements
  const paragraphs = document.querySelectorAll('p');

  paragraphs.forEach(p => {
    // 3. Regex to find "word#code" - matches letters/numbers before # and after
    const regex = /(\w+)#(\w+)/g;
    
    if (regex.test(p.innerHTML)) {
      p.innerHTML = p.innerHTML.replace(regex, (match, word, code) => {
        // 4. Create the tripcode
        const tripcode = "!" + hashCode(code);
        return `${word} </b><tc>${tripcode}</tc>`;
      });
    }
  });
}

// Run on load
window.onload = generateTripcode;

    </script>

  
  
      
  
    
        <style>
            textarea {
  resize: none;
}
            .draggable-window {
    position: fixed; /* Stays in place during scroll */
    z-index: 9;
    background-color: #f1f1f1;
    border: 1px solid #d3d3d3;
    top: 50px;
    left: 50px;
                width: 250px;
}
.window-header {
    padding: 10px;
    cursor: move;
     background-color: rgba(238, 170, 136);
    color: darkred;
      border: 2px solid darkred;
}
 .spoiler-container {
    /* Set the container to position: relative so absolute positioning works within it */
    position: relative;
    /* Set a specific width and height for consistency, or the size of your images */
    width: 400px; 
    height: 300px;
    cursor: pointer; /* Optional: adds a hand cursor on hover */
}

.image {
    /* Position both images absolutely to stack them */
    position: absolute;
    top: 0;
    left: 0;
    width: 200px;
    height: 100%;
    /* Ensure they are the same size */
}

.top-image {
    /* The top image is fully visible by default */
    opacity: 1;
    /* Add a smooth transition effect for the opacity change */
    transition: opacity 0.5s ease;
    /* Ensure it is above the bottom image */
    z-index: 2;
}

/* When hovering over the container, change the opacity of the top image */
.spoiler-container:hover .top-image {
    opacity: 0;
}

             .greentext { color: green; }
   .reply-header{
          background-color: rgb(234, 214, 203);
           padding:2px;
           width:400px;
           border: 1px solid rgb(157, 127, 111);

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
    
            .spoiler-content {
    background-color: #333;
    color: transparent;
    cursor: pointer;
    padding: 0 4px;
    border-radius: 3px;
    transition: background-color 0.3s, color 0.3s;
}

.spoiler-content:hover {
    background-color: transparent;
    color: inherit;
}
 .dropdown { position: relative; display: inline-block; }
    .dropbtn { background-color: rgb(234, 214, 203); color: rgb(78, 103, 128); padding: 3px;  border: 1px solid rgb(157, 127, 111); cursor: pointer; font-weight: bold; font-size:14px; }  
    .dropdown-content { display: none; position: absolute; background-color: #f9f9f9; min-width: 160px; box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2); z-index: 1; }
    .dropdown-content button { color: black; padding: 12px 16px; text-decoration: none; display: block; border: none; background: none; width: 100%; text-align: left; }
    .dropdown-content button:hover { background-color: #f1f1f1; }
    .show { display: block; }
       </style>
</body>
</html>
