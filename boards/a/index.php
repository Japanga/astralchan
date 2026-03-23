<?php
// Function to generate a unique numeric ID
function generateUniqueId() {
    return time() . mt_rand(100, 999);
}

// Function to get post data
function getPosts() {
    if (file_exists('posts.json')) {
        $data = file_get_contents('posts.json');
        return json_decode($data, true) ?: [];
    }
    return [];
}

// Handle new post submission
if (isset($_POST['submit_post'])) {
    require_once 'upload_post.php';
    handlePostUpload();
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
    <title>Image Board</title>
    
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Q0MZX600Y4"></script>
    
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-Q0MZX600Y4');
</script>
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
    <h1>Create a New Thread</h1>
    <!-- Form for uploading image and text -->
    <form action="index.php" method="POST" enctype="multipart/form-data">

        <label for="description">Name:</label><br>
         <textarea name="username" id="username" cols="20">Anonymous</textarea><br>
          <label for="description">Subject:</label><br>
           <textarea name="title" id="title" cols="20"></textarea><br>
            <label for="description">Post:</label><br>
        <textarea name="description" id="description" rows="4" cols="50"></textarea><br>
        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image" accept="image/*" required><br>

        <button type="submit" name="submit_post" id="submitbutton" disabled>Post</button>

    </form>
    
      <style>
     /* The pop-up container (hidden by default) */
.popup {
    display: none; /* Hide the popup initially */
    position: fixed; /* Stay in place */
    z-index: 1; /* Sit on top */
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5); /* Black overlay with opacity */
    justify-content: center; /* Center the content */
    align-items: center; /* Center the content */
}

/* Pop-up content box */
.popup-content {
    background-color: #fefefe;
    padding: 20px;
    border: 1px solid #888;
    width: 340px; /* Adjust width as needed */
    border-radius: 5px;
    text-align: center;
}
 #captcha-box { border: 1px solid #ccc; padding: 20px; text-align: center; width: 300px; }
    .captcha-img { 
        font-family: 'Courier New', Courier, monospace;
        font-size: 30px;
        font-weight: bold;
        letter-spacing: 5px; 
        filter: blur(2px); /* Blurred text */
        transform: skew(15deg, 5deg); /* Distorted text */
        background: #eee;
        padding: 10px;
        margin-bottom: 10px;
        display: inline-block;
        user-select: none;
    }
    .hidden { display: none; }
    #progression { margin-bottom: 15px; font-weight: bold; color: #555; }
.footer-text {
    font-size: 0.65rem;
    color: #6b7280; /* Gray text color */
    text-align: center;
    padding: 1rem;
    display: block;
  }
  </style>
    
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
        <button type="submit" name="check_ban" id="banbutton" disabled>Check Ban Status</button>
    </form>

    <br>
    <?php echo $message; ?>
        

    <div 
  class="cf-turnstile" 
  data-sitekey="0x4AAAAAACpzlOrqUUjsRKbz" 
  data-callback="handleSuccess"
  data-theme="light"
></div>


 <button onclick="window.location.href='/boards/a/catalog.php'">Enter catalog view</button>
    
 <button onclick="window.location.href='/index.php'">Return to home page</button>
    
     <button onclick="window.location.href='/boards/a/archive.php'">View thread archives</button>

 <button class="open-button" onclick="openForm()">Change Layout</button>

    <script>
  function handleSuccess(token) {
    console.log("Turnstile success! Token:", token);
    // You can now enable a submit button, submit the form with AJAX, etc.
  const button = document.getElementById("banbutton");

  // Enable the button by setting the disabled property to false
  button.disabled = false;
  }
   
</script>
   
   <!-- The Popup Form -->
    <div class="form-popup" id="myForm">
        <form class="form-container">
            <h2>Change Colors</h2>
            <label for="bgColor"><b>Background Color:</b></label>
            <input type="color" id="bgColor" name="bgColor" value="#ffffff">

            <label for="textColor"><b>Text Color:</b></label>
            <input type="color" id="textColor" name="textColor" value="#000000">

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
   
   
  
  
  
  
  
   <script>
         // Simple random string generator for captcha
    const codes = [
        Math.random().toString(36).substring(2, 7).toUpperCase(),
        Math.random().toString(36).substring(2, 7).toUpperCase(),
        Math.random().toString(36).substring(2, 7).toUpperCase()
    ];

    document.getElementById('img1').innerText = codes[0];
    document.getElementById('img2').innerText = codes[1];
    document.getElementById('img3').innerText = codes[2];

    function checkStage(stage) {
        let input = document.getElementById('input' + stage).value;
        if (input === codes[stage - 1]) {
            if (stage < 3) {
                document.getElementById('stage' + stage).classList.add('hidden');
                document.getElementById('stage' + (stage + 1)).classList.remove('hidden');
                document.getElementById('progression').innerText = "Stage " + (stage + 1) + " of 3";
            } else {
                document.getElementById('stage3').classList.add('hidden');
                document.getElementById('progression').classList.add('hidden');
                document.getElementById('success').classList.remove('hidden');
              
               document.getElementById('exitButton').classList.remove('hidden');
            }
        } else {
            alert('Incorrect, try again.');
          location.reload();
        }
    }
      </script>
  

    <script>
      // Get the modal, buttons, and close functionality
const openPopup = document.getElementById('openPopup');
const closePopup = document.getElementById('closePopup');
const popup = document.getElementById('popup');
const actionButton = document.getElementById('actionButton');
      const exitButton = document.getElementById('exitButton');

// When the user clicks the "Open Pop-up" button, open the modal
openPopup.addEventListener('click', () => {
    popup.style.display = 'flex'; // Use 'flex' to center the content with CSS
});

// When the user clicks the "Close" button, close the modal
exitButton.addEventListener('click', () => {
    popup.style.display = 'none';
    var x = document.getElementById("myDivClass");
   x.classList.toggle("hidden"); 
});

// Optional: Add functionality to the action button
actionButton.addEventListener('click', () => {
    alert('Action performed!');
    popup.style.display = 'none'; // Close after action
});

// Optional: Close the modal if the user clicks anywhere outside of the content
window.addEventListener('click', (event) => {
    if (event.target === popup) {
        popup.style.display = 'none';
    }
});

  </script> <!-- Link your JavaScript file -->
  

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

    
    

    <h1>Recent Threads</h1>
    <?php
   function getHiddenReplyCount($jsonFilePath) {
    // 1. Read JSON file
    if (!file_exists($jsonFilePath)) return "0 replies";
    $jsonData = file_get_contents($jsonFilePath);
    $posts = json_decode($jsonData, true);

    if (!$posts || !is_array($posts)) return "0 replies";

    // 2. Filter posts where 'hidden' is true
    $hiddenPosts = array_filter($posts, function($post) {
        return isset($post['hidden']) && $post['hidden'] === true;
    });

    $count = count($hiddenPosts);

    // 3. Format pluralization
    $text = ($count === 1) ? "reply has " : "replies have ";
    
    return "$count $text";
}

        function check_spoiler_status($post_id, $reply_id) {
    // Define the file path
    $file_path = './replies/' . $post_id . '.json';

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
             echo "<img src='" . htmlspecialchars($reply['image_path']) . "' width='130'><br>";
             echo '<img src="https://i.imgur.com/xBUBByL.png" alt="Top Image" class="image top-image">';
    echo '</div>';
               echo '</div>';
            return true;
        } else {
            echo "<img src='" . htmlspecialchars($reply['image_path']) . "' width='130'><br>";
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
    
       
 function processTripcodes($text) {
    // Regex breakdown:
    // (?<!#)    - Negative lookbehind: Ensure the first # is not preceded by another #
    // #         - Match the # separator
    // ([^#\s]+) - Capture the text after # (tripcode input), ensuring no spaces or more #
    $pattern = '/(?<!#)#([^#\s]+)/';

    return preg_replace_callback($pattern, function ($matches) {
        // $matches[1] is the text after the #
        $tripcodeText = $matches[1];
        
        // Generate a 10-character hash (like 4chan/anonymous style)
        // Uses base64 and special characters for higher complexity
        $hash = substr(base64_encode(hash('sha256', $tripcodeText, true)), 0, 10);
        
        // Return ! followed by the hash
        return ' !<tc>' . $hash;
    }, $text);
}


   
    function displayLatestReply($postId) {
    $filePath = "./replies/" . $postId . ".json";
    if (file_exists($filePath)) {
        $json = file_get_contents($filePath);
        $replies = json_decode($json, true);

        if (!empty($replies)) {
            // Get last item from the array
            $latest = end($replies);
            // Display safely
            echo '<div class="latest-reply" style="font-size:0.9em; color:#555; padding:5px; margin-top:5px;">';
             echo '<div class="reply-header">';
            echo '<strong><gt>';
            echo processTripcodes($latest['username']);
            echo '</tc></gt> ' . htmlspecialchars($latest['timestamp']) . ' No. ' . htmlspecialchars($latest['id']) . ':</strong> ';
            echo '</div>';
            echo htmlspecialchars($latest['text']);
            echo '</div>';
        }
         if ($latest['image_path']) {  
             check_spoiler_status($postId, $latest['id']);
        }
    }
}
    
    function linkifyHashNumbers($text) {
    // Regex: Matches a # followed by one or more digits (\d+), ensuring a word boundary (\b) after the number.
    // The pattern uses capturing group $1 for the number part.
    $pattern = '/#(\\d+)\\b/'; 
    
    // Replacement: Wraps the match with an anchor tag, using the captured number in the href attribute.
    // Modify the URL in href to point to your desired destination (e.g., search page, specific ID).
    $replacement = '<a href="/boards/a/thread.php?id=$1">#$1</a>';
    
    // Perform the replacement
    $linkedText = preg_replace($pattern, $replacement, $text);
    
    return $linkedText;
}
 
    
    function displayPreviousReplies($postId, $num = 5) {
    $filename = './replies/' . $postId . '.json';
    
    // 1. Check if file exists
    if (!file_exists($filename)) {
        echo "No replies yet.";
        return;
    }

    // 2. Read and decode JSON data
    $json = file_get_contents($filename);
    $replies = json_decode($json, true);

    if (!$replies || count($replies) <= 1) {
        echo "No previous replies.";
        return;
    }

    // 3. Exclude the latest (last) one
    array_pop($replies); 

    // 4. Limit the quantity ($num)
    // Get the last N elements after the newest was popped
    $limitedReplies = array_slice($replies, -$num);

    // 5. Display oldest on top, newest on bottom (if JSON is stored asc)
    // If your JSON is stored newest-first, you might need: $limitedReplies = array_reverse($limitedReplies);
    
    echo '<div class="replies-list">';
    foreach ($limitedReplies as $reply) {
        // Assuming your JSON structure has 'author' and 'message'
        echo '<div class="old-reply" style="font-size:0.9em; color:#555; padding:5px; margin-top:5px;  border-top:1px solid #eee;">';
        echo '<div class="reply-header">';
        echo '<strong><gt>'; 
        echo $reply['username']; 
        echo ':</gt></strong> <strong>' . ($reply['timestamp']) . ' No. ' . ($reply['id']) . ' </strong>';
        echo '</div>';
        echo linkifyHashNumbers($reply['text']);
        echo '</div>';
          }
         echo '<div class = "old-image">';
         if ($reply['image_path']) {  
             check_spoiler_status($postId, $reply['id']);
        
    }
         echo '</div>';
    echo '</div>';
           echo '</div>';

}

// Usage inside your loop
// displayLatestReply($post['id']);
    
  function hideOldPosts($jsonFile) {
    if (!file_exists($jsonFile)) return;

    $data = json_decode(file_get_contents($jsonFile), true);
    $total = count($data);
    
    // Only process if there are more than 4 posts
    if ($total > 4) {
        for ($i = 0; $i < $total - 4; $i++) {
            $data[$i]['hidden'] = true;
        }
        file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    // Display notice
    echo '<st>';
   echo '<img src="https://i.imgur.com/aVrQ7EL.png" alt="Description of image" / style="width:35px; height:25px;">';
    echo 'Old/hidden posts nested automatically at ' . date('H:i:s');
    echo '</st>';
}
    
   

    function updateReplyCounts($postsFile, $repliesDir) {
    // 1. Read and decode the posts.json file
    if (!file_exists($postsFile)) return "Posts file not found.";
    $postsData = json_decode(file_get_contents($postsFile), true);

    // 2. Iterate through each post to update reply_count
    foreach ($postsData as &$post) {
        $replyFile = $repliesDir . $post['id'] . '.json';
        
        // Check if reply file exists, count its contents
        if (file_exists($replyFile)) {
            $replies = json_decode(file_get_contents($replyFile), true);
            // Count replies, assuming it's a list/array
            $post['reply_count'] = is_array($replies) ? count($replies) : 0;
        } else {
            // If no replies file, set count to 0
            $post['reply_count'] = 0;
        }
    }
    unset($post); // Break reference

    // 3. Overwrite the original posts.json with updated data
    file_put_contents($postsFile, json_encode($postsData, JSON_PRETTY_PRINT));
    return "<st>All threads organized by bump order.</st>";
}
    
    function sort_by_reply_count_desc($a, $b)
{
    return $b['reply_count'] <=> $a['reply_count']; // Descending order
}
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
    $posts = getPosts();
    
    usort($posts, 'sort_by_reply_count_desc');
    
    foreach ($posts as $post) {
         echo "<div class='containernew'>";
          echo " <div class='right-section'>";
        echo "<div class='post'>";
        echo updateReplyCounts('posts.json', 'replies/');
        echo "<b><p><gt>" . htmlspecialchars($post['username']) . "</gt></b> <tc>" . htmlspecialchars($post['tripcode']) . "</tc> Post ID: " . htmlspecialchars($post['id']) . "</p>";
          echo "<h2>" . $post['title'] . "</h2>";
        echo "<p>" . $post['description'] . "</p>";
        echo "<a href='thread.php?id=" . htmlspecialchars($post['id']) . "'>";
        echo "<img src='" . htmlspecialchars($post['image_path']) . "' width='200'></a><br>";
                echo "<small>Uploaded: " . htmlspecialchars($post['timestamp']) . " | Size: " . formatBytes($post['file_size']) . "</small>";
        echo "</div>";
        echo "</div>";
         echo " <div class='left-section'>";
                $filePath = 'replies/' . htmlspecialchars($post['id']) . '.json'; 

$count = getHiddenReplyCount($filePath); 
echo "<st> " . $count . " been hidden. </st> <st><a href='thread.php?id=" . htmlspecialchars($post['id']) . "'>Click here";
         echo "</a>";
         echo " to view.</st>";
        hideOldPosts('replies/' . $post['id'] . '.json');
        echo displayPreviousReplies($post['id'], 3);
         echo "</div>";
          echo " <div class='bottom-section'>";
        echo  displayLatestReply($post['id']);
         echo "</div>";
        echo "</div><hr>";
    }
  function displayReplyCount($postId) {
    // Sanitize input
    $safeId = htmlspecialchars($post['id']);
    $filePath = "replies/" . $safeId . ".json";

    // Check if file exists
    if (file_exists($filePath)) {
        // Read file contents
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true); // {Link: decode as array https://codesignal.com/learn/courses/handling-json-files-with-php/lessons/parsing-and-accessing-json-data-with-php}

        // Count items if data is an array
        if (is_array($data)) {
            $count = count($data);
            echo "<p>Total replies: " . $count . "</p>";
        } else {
            echo "<p>Total replies: 0</p>";
        }
    } else {
        echo "<p>No replies found.</p>";
    }
}
    ?>
    
    
        
<script>
   function processSpoilers() {
  // 1. Select all div elements with class "left-section"
  const sections = document.querySelectorAll('.left-section');

  sections.forEach(section => {
    // 2. Regex to find [spoiler]...[/spoiler] and capture the content
    const regex = /\[spoiler\]([\s\S]*?)\[\/spoiler\]/g;
    
    // 3. Replace matches with HTML structure
    if (regex.test(section.innerHTML)) {
      section.innerHTML = section.innerHTML.replace(
        regex, 
        '<div class="spoiler-box">$1</div>'
      );
    }
  });
}

// Run the function after the DOM is fully loaded
document.addEventListener('DOMContentLoaded', processSpoilers);

    </script>   
    
 
   
     <style>
           .reply-header{
           background-color: rgb(182, 196, 210); 
           padding:6px;
           width:400px;
           border: 1px solid rgb(78, 103, 128);

         }
.spoiler-box {
    background-color: #333;
    color: transparent;
    cursor: pointer;
    padding: 0 4px;
    border-radius: 3px;
    width:80px;
    transition: background-color 0.3s, color 0.3s;
}

.spoiler-box:hover {
    background-color: transparent;
    color: inherit;
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
.containernew {
    display: flex; /* Makes the child elements arrange in a row by default */
    min-height: 30vh; /* Ensures the container takes up the full viewport height */
    width: 80%;
}

.left-section, .right-section, .bottom-section {

    box-sizing: border-box; /* Includes padding in the width calculation */
}

.left-section {
    flex: 1; /* Both sections will take up equal horizontal space */
       background-color: rgb(214, 218, 240);
    border: 1px solid #ccc;
}

.right-section {
    flex: 1;
    background-color: transparent;
}
.bottom-section {
    flex: 1;
     background-color: rgb(214, 218, 240);
     width: 80%;
    border: 1px solid #ccc;
}

         /* Optional: Add a media query for responsiveness on smaller screens */
@media (max-width: 600px) {
    .containernew {
        flex-direction: column; /* Stacks the sections vertically on small screens */
    }
}
  .spoiler-container {
    /* Set the container to position: relative so absolute positioning works within it */
    position: relative;
    /* Set a specific width and height for consistency, or the size of your images */
    width: 130px; 
      height: 200px;
    cursor: pointer; /* Optional: adds a hand cursor on hover */
}

.image {
    /* Position both images absolutely to stack them */
    position: absolute;
    top: 0;
    left: 0;
    width: 130px;
        height: 200px;
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
                 textarea {
  resize: none;
}
         gt {
 color:green;
         
}
   tc {
 color:green;
 text-decoration: underline;
 font-weight: normal;
}
        st {
font-size: 0.8em; /* Makes the text size 80% of its parent element's font size */
    color: grey;
         
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
 </style>
 
</body>
</html>
