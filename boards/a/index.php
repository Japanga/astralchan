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
       <div class="container2">
        <label for="description">Tripcode:</label><br>
        <input type="password" id="password" placeholder="Enter password" oninput="generateTripcode()">
        <input type="text" name="content3" id="tripcode" placeholder="Generated Tripcode" readonly>
        </div>
        <label for="description">Name:</label><br>
         <textarea name="username" id="username" cols="20">Anonymous</textarea><br>
          <label for="description">Subject:</label><br>
           <textarea name="title" id="title" cols="20"></textarea><br>
            <label for="description">Post:</label><br>
        <textarea name="description" id="description" rows="4" cols="50"></textarea><br>
        <label for="image">Upload Image:</label>
        <input type="file" name="image" id="image" accept="image/*" required><br>
        
            <div id="myDivClass" class="hidden">
        <button type="submit" name="submit_post">Post</button>
   </div>
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
    
    
         <button id="openPopup">Get captcha</button>
 <button onclick="window.location.href='/index.php'">Return to home page</button>
    
     <button onclick="window.location.href='/boards/a/archive.php'">View thread archives</button>

 <button class="open-button" onclick="openForm()">Change Layout</button>

   
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
  


    <h1>Recent Threads</h1>
    <?php
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
    foreach ($posts as $post) {
        echo "<div class='post'>";
        echo "<b><p><gt>" . htmlspecialchars($post['username']) . "</gt></b> <tc>" . htmlspecialchars($post['tripcode']) . "</tc> Post ID: " . htmlspecialchars($post['id']) . "</p>";
          echo "<h2>" . htmlspecialchars($post['title']) . "</h2>";
        echo "<p>" . htmlspecialchars($post['description']) . "</p>";
        echo "<a href='thread.php?id=" . htmlspecialchars($post['id']) . "'>";
        echo "<img src='" . htmlspecialchars($post['image_path']) . "' width='200'></a><br>";
        echo "<small>Uploaded: " . htmlspecialchars($post['timestamp']) . " | Size: " . formatBytes($post['file_size']) . "</small>";
        echo "<p><a href='thread.php?id=" . htmlspecialchars($post['id']) . "'>View thread, total replies:", getReplyCount(htmlspecialchars($post['id']));
        echo "</a></p>";
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
