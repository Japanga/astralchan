


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
            $message = "<div style='color: white; background-color: red; padding: 10px; border-radius: 5px;'>
                            You have been banned from all boards on AstralChan for the following reason: <br>
                            Reason: {$data['reason']}<br>
                           The IP you were posting with was  <strong>$shadowMaskId</strong><br>
                            Your ban was applied on:
                            Date: {$data['date']}
                        </div>";
        } else {
            $message = "<div style='color: white; background-color: green; padding: 10px; border-radius: 5px;'>
                           You are not banned!
                        </div>";
        }
    } else {
        $message = "Error: Ban list not found.";
    }
}
?>


            <h2>Ban Status System</h2>
    
       <p>Checking ID: <strong><?php echo $shadowMaskId; ?></strong></p>
    
    <form method="post">
        <button type="submit" name="check_ban">Check Ban Status</button>
    </form>

    <br>
    <?php echo $message; ?>

              <img src="https://i.imgur.com/ctSeroJ.png" alt="Description of image" / style="width:300px; height:200px;">

    
       <style>

   
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