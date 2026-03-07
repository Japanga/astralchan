<?php
// Configuration
$data_file = 'posts.json';
$upload_dir = 'uploads/';
$admin_password_hash = password_hash('your_admin_password', PASSWORD_DEFAULT); // Change to a secure password

// Ensure upload directory exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Function to load posts
function load_posts($file) {
    if (file_exists($file)) {
        $data = file_get_contents($file);
        return json_decode($data, true) ?: [];
    }
    return [];
}

// Function to save posts
function save_posts($file, $posts) {
    file_put_contents($file, json_encode($posts, JSON_PRETTY_PRINT));
}

// Handle new posts/replies
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_post'])) {
    $posts = load_posts($data_file);
    $post_id = count($posts) > 0 ? max(array_keys($posts)) + 1 : 1;
    $parent_id = isset($_POST['parent_id']) && is_numeric($_POST['parent_id']) ? (int)$_POST['parent_id'] : 0;
    $content = htmlspecialchars($_POST['content']);
     $content2 = htmlspecialchars($_POST['content2']);
     $content3 = htmlspecialchars($_POST['content3']);
    $image_path = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file_info = pathinfo($_FILES['image']['name']);
        $unique_name = uniqid() . '.' . strtolower($file_info['extension']);
        $target_file = $upload_dir . $unique_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    $new_post = [
        'id' => $post_id,
        'parent_id' => $parent_id,
        'content' => $content,
        'content2' => $content2,
        'content3' => $content3,
        'image' => $image_path,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    $posts[$post_id] = $new_post;
    save_posts($data_file, $posts);
    header('Location: index.php'); // Redirect to prevent form resubmission
    exit;
}

  function formatBytes($bytes, $precision = 2) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= (1 << ($pow * 10));
            return round($bytes, $precision) . ' ' . $units[$pow];
        }

// Function to display posts and replies recursively
function display_posts_recursive($posts, $parent_id = 0, $level = 0) {
    foreach ($posts as $post) {
        if ($post['parent_id'] == $parent_id) {
            echo "<div style='margin-left: " . ($level * 20) . "px; border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;'>";
            echo "<p><b><gt>" . nl2br($post['content2']) . "</gt></b> <tc><b>". nl2br($post['content3']) ."</tc></b> <strong>No: " . $post['id'] . "</strong> <small>(" . $post['timestamp'] . ")</small></p>";
            echo "<p>" . nl2br($post['content']) . "</p>";
            if ($post['image']) {
                $size_bytes = filesize($post['image']); //
               $imageType = exif_imagetype($post['image']);
                $extension = image_type_to_extension($imageType); // e.g., '.png'
    $mimeType = image_type_to_mime_type($imageType); // e.g., 'image/png'
               
                echo "<img src='" . htmlspecialchars($post['image']) . "' style='max-width: 200px;'><br>";
                echo "<p><st>"  . htmlspecialchars(formatBytes($size_bytes)) . " " . $extension . "</st></p>";
            }
            
            $postId = $post['id']; // Assuming $post['id'] is available
$hashValue = '#â €' . $postId;
$jsFunctionCall = "document.getElementById('textarea').value = " . json_encode($postId) . ";";
            // Reply form link (optional, can be a full form here)
            echo "<a href='#reply_form_".$post['id']."' onclick='document.getElementById(\"parent_id\").value=".$post['id']."; document.getElementById(\"textarea\").value=". htmlspecialchars($jsFunctionCall, ENT_QUOTES) .";'>Reply to this</a>";
            
            // Admin delete link (must link to admin.php and handle authentication)
            echo " | <a href='admin.php?delete_id=" . $post['id'] . "' onclick=\"return confirm('Are you sure?');\">Delete</a>";
            
            echo "</div>";
            display_posts_recursive($posts, $post['id'], $level + 1);
        }
    }
}

$posts = load_posts($data_file);
// Sort posts so original posts appear first
uasort($posts, function($a, $b) {
    if ($a['parent_id'] == $b['parent_id']) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']); // sort by time within a thread
    }
    return $a['parent_id'] <=> $b['parent_id'];
});

?>

<!DOCTYPE html>
<html>
<head>
    <title>AstralChan</title>
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

  <style>
  .container2 { max-width: 400px; margin: auto;   margin-left: -20px; padding: 20px; }
        #tripcode { font-weight: bold; color: blue; }
    </style>
    
    
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
    <h1>Posts</h1>

    <!-- Post/Reply Form -->
    <h2 id="reply_form_0">New Post / Reply</h2>
    <form method="POST" enctype="multipart/form-data" action="index.php">
   <div class="container2">
        <input type="password" id="password" placeholder="Enter password" oninput="generateTripcode()">
        <input type="text" name="content3" id="tripcode" placeholder="Generated Tripcode" readonly>
        </div>
     <textarea name="content2" id="textarea2" cols="30" required>Anonymous</textarea><br>
        <textarea name="content" id="textarea" rows="4" cols="50" required>#</textarea><br>
        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image"><br>
        <label for="parent_id">Reply to Post ID (leave 0 for new post):</label>
        <input type="number" name="parent_id" id="parent_id" value="0"><br>
         <div id="myDivClass" class="hidden">
       <input type="submit" name="submit_post" value="Post">
</div>
    </form>
    
     <button id="openPopup">Get captcha</button>
 <button class="open-button" onclick="openForm()">Change Layout</button>
 <button onclick="window.location.href='catalog.php'">Enter catalog view</button>
 <button onclick="window.location.href='/index.php'">Return to home page</button>
   
   
   
   
    <div id="popup" class="popup">
        <div class="popup-content">
          
<div id="captcha-box">
    <h3>Verification</h3>
    <div id="progression">Stage 1 of 3</div>
    
    <!-- Stage 1 -->
    <div id="stage1">
        <div class="captcha-img" id="img1"></div><br>
        <input type="text" id="input1" placeholder="Enter text">
        <button onclick="checkStage(1)">Submit</button>
    </div>

    <!-- Stage 2 -->
    <div id="stage2" class="hidden">
        <div class="captcha-img" id="img2"></div><br>
        <input type="text" id="input2" placeholder="Enter text">
        <button onclick="checkStage(2)">Submit</button>
    </div>

    <!-- Stage 3 -->
    <div id="stage3" class="hidden">
        <div class="captcha-img" id="img3"></div><br>
        <input type="text" id="input3" placeholder="Enter text">
        <button onclick="checkStage(3)">Submit</button>
    </div>

    <div id="success" class="hidden" style="color: green;">Verified Successfully!</div>
   <button id="exitButton" class="hidden">Exit captcha</button>
</div>
        </div>
    </div>
  
  
  
  
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
    <hr>

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
    <!-- Display Posts -->
        <div id="content-container">
    <h2>Latest Activity</h2>
    <?php display_posts_recursive($posts); ?>
 </div>
   <hr>
    <footer>
  <span class="footer-text">Â© 2026 AstralChan imageboard software by <a href="https://github.com/Japanga/astralchan">Japanga</a> developed in .PHP and hosted by <a href="https://aeonfree.com/">AeonFree</a></span>
</footer>
 
 <script>
  // Function to check and modify the input value
        function checkAndAddHash() {
            // Get the input element
            const inputElement = document.getElementById('textarea');
            // Get the current value of the input field
            let currentValue = inputElement.value;

            // Check if the current value contains at least one digit (0-9)
            // The /\d/ regular expression tests for the presence of any digit.
            if (/\d/.test(currentValue)) {
                // Check if a '#' is already at the beginning of the string to prevent adding multiple
                if (!currentValue.startsWith('#')) {
                    // Add '#' to the front of the value
                    inputElement.value = '#' + currentValue;
                }
            }
        }

        // Use setInterval() to call the function every 2 seconds (2000 milliseconds).
        // Store the interval ID to potentially stop it later with clearInterval(intervalId)
        const intervalId = setInterval(checkAndAddHash, 2000); 

        // Optional: Example of how to stop the interval (e.g., after 20 seconds)
        // setTimeout(() => {
        //     clearInterval(intervalId);
        //     console.log("Interval stopped.");
        // }, 20000);
 </script>
 
 <script src="tagging.js"></script>
     <style>
       
body {
  /* Fallback color */
  background: #D5DEE7; 
  /* Ensures the gradient covers the entire page */
  height: 100vh; 
  margin: 0;
  background-attachment: fixed;
  font-family: Arial, sans-serif;/* Prevents the gradient from repeating if content is short */
}
       
         gt {
 color:green;
         
}
  bm {
 color:red;
         font-weight: bold;
        font-style: italic;
}
   tc {
 color:green;
 text-decoration: underline;
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
