<?php
// Configuration
$password_key = "secret_password";
$replies_dir = "replies/";
$main_posts_file = "posts.json";

// Password protection
session_start();
if (isset($_POST['password']) && $_POST['password'] === $password_key) {
    $_SESSION['authenticated'] = true;
}
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    echo ' <section class="content-section"><form method="post"><input type="password" name="password"><input type="submit" value="Login"></form></section>';
     echo '  <link rel="stylesheet" type="text/css" href="adminpage.css">';
    exit;
}

// Function to remove ID from JSON file
function removeIdFromJson($filePath, $idToRemove) {
    if (!file_exists($filePath)) return "File not found.";
    $data = json_decode(file_get_contents($filePath), true);
    if (!is_array($data)) return "Invalid JSON.";
    
    // Assuming JSON structure is a list of objects with an 'id' key
    $newData = array_filter($data, function($item) use ($idToRemove) {
        return $item['id'] != $idToRemove;
    });
    
    file_put_contents($filePath, json_encode(array_values($newData), JSON_PRETTY_PRINT));
    return "ID $idToRemove processed.";
}

// Handle Form Submissions
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_reply']) && !empty($_POST['filename']) && !empty($_POST['id'])) {
        $file = $replies_dir . basename($_POST['filename']);
        $message = removeIdFromJson($file, $_POST['id']);
    }
    if (isset($_POST['remove_main']) && !empty($_POST['id'])) {
        $message = removeIdFromJson($main_posts_file, $_POST['id']);
    }
}
?>

<?php
$message2 = "";
$repliesDir = 'replies/';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filename2 = $_POST['filename2'] ?? '';
    $targetId = $_POST['targetId'] ?? '';
    $filePath = $repliesDir . $filename2 . '.json';

    if (!empty($filename2) && file_exists($filePath)) {
        $message = "<p style='color:green;'>File '$filename.json' found successfully!</p>";

        if (!empty($targetId)) {
            // Read, modify, and save JSON
            $jsonData = json_decode(file_get_contents($filePath), true);
            $found = false;

            if (is_array($jsonData)) {
                foreach ($jsonData as &$item) {
                    if (isset($item['id']) && $item['id'] == $targetId) {
                        // Append red, bolded text
                        echo $item['text'] .= " <bm>(USER WAS BANNED FOR THIS POST.)</bm>";
                        $found = true;
                        break;
                    }
                }
            }

            if ($found) {
                file_put_contents($filePath, json_encode($jsonData, JSON_PRETTY_PRINT));
                $message2 .= "<p style='color:red;'>ID $targetId banned.</p>";
            } else {
                $message2 .= "<p style='color:orange;'>ID $targetId not found in file.</p>";
            }
        }
    } elseif (!empty($filename)) {
        $message2 = "<p style='color:red;'>File '$filename.json' not found.</p>";
    }
}
$message3 = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $filename3 = 'replies/' . $_POST['filename3'] . '.json';
    $replyId = $_POST['replyId'];
    $color = $_POST['color'];

    if (file_exists($filename3)) {
        // Read and decode JSON file
        $jsonContent = file_get_contents($filename3);
        $data = json_decode($jsonContent, true);

        // Find the reply ID and update color
        $found = false;
        foreach ($data as &$reply) {
            if ($reply['id'] == $replyId) {
                // Apply color class to username
                $reply['username'] = '<gt>Anonymous </gt> <span class="' . $color . '">##' . $color . '</span>';
                $found = true;
                break;
            }
        }

        if ($found) {
            // Save back to file
            file_put_contents($filename3, json_encode($data, JSON_PRETTY_PRINT));
            $message3 = "Username updated successfully!";
        } else {
            $message3 = "Reply ID not found.";
        }
    } else {
        $message3 = "File does not exist.";
    }
}
// Define the subdirectory
$repliesDir = 'replies/';
$message4 = '';
$error = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileName = filter_input(INPUT_POST, 'file_name', FILTER_SANITIZE_STRING);
    $replyId = filter_input(INPUT_POST, 'reply_id', FILTER_SANITIZE_STRING);
    $prependMessage = filter_input(INPUT_POST, 'prepend_message', FILTER_SANITIZE_STRING);
    
    // Construct the full file path
    $filePath = $repliesDir . $fileName;

    // Validate inputs and file path
    if (empty($fileName) || empty($replyId)) {
        
    } elseif (!file_exists($filePath)) {
        $error = "File not found: " . htmlspecialchars($filePath);
    } else {
        // Read the JSON file content
        $jsonContent = file_get_contents($filePath);
        $data = json_decode($jsonContent, true); // Decode as associative array

        if ($data === null) {
            $error = "Error decoding JSON from file: " . htmlspecialchars($filePath);
        } else {
            $found = false;
            // Iterate through the data to find the matching ID.
            // Use & in foreach to modify the original array in place
            foreach ($data as &$item) {
                // Assuming the JSON structure has an 'id' key for each item
                if (isset($item['id']) && (string)$item['id'] === $replyId) {
                    // Assuming the JSON structure has a 'message' key to update
                    if (isset($item['id'])) {
                        $item['id'] = $item['id']. '<img src="https://cdn-icons-png.flaticon.com/512/11073/11073124.png" alt="Description" width="20" height="20">';
                        $found = true;
                        break; // Stop iterating once the ID is found
                    } else {
                        $error = "Key 'message' not found for reply ID: " . htmlspecialchars($replyId);
                        break;
                    }
                }
            }
            unset($item); // Break the reference

            if ($found) {
                // Encode the updated array back to a JSON string
                $newJsonContent = json_encode($data, JSON_PRETTY_PRINT);

                // Write the new JSON data back to the file
                if (file_put_contents($filePath, $newJsonContent, LOCK_EX) !== false) {
                    $message4 = "Reply ID " . htmlspecialchars($replyId) . " in file " . htmlspecialchars($fileName) . " updated successfully.";
                } else {
                    $error = "Error writing to file.";
                }
            } elseif (empty($error)) {
                $error = "Reply ID " . htmlspecialchars($replyId) . " not found in the file.";
            }
        }
    }
}
 
 if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['url'])) {
    $url = $_POST['url'];
    $html = file_get_contents($url);

    if ($html) {
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        // 1. Remove Scripts
   

        // 2. Add New CSS/Styling
        $head = $dom->getElementsByTagName('head')->item(0);
        if ($head) {
            $style = $dom->createElement('style', 'body {  background-image: linear-gradient(to bottom, #444444 0%, #555555 10%, #222222 10%, #333333 40%, #111111 100%);
  color: #D6D6D6;
  /* Ensures the gradient covers the entire page */
  height: 100vh; 
  margin: 0;
  background-attachment: fixed;
  font-family: Arial, sans-serif; } .snapshot-header { position: fixed; top: 0; left: 0; width: 100%; background: rgba(0,0,0,0.7); color: white; padding: 10px; z-index: 9999; text-align: center; }  .replyform {display:none;}  hiddenr {display:none;}  ');
            $head->appendChild($style);
        }

        // 3. Add Timestamp Overlay
        $body = $dom->getElementsByTagName('body')->item(0);
        if ($body) {
            $div = $dom->createElement('div', 'Snapshot taken: ' . date('Y-m-d H:i:s'));
            $div->setAttribute('class', 'snapshot-header');
            $body->insertBefore($div, $body->firstChild);
        }

        // 4. Save New Webpage
        $filename = 'snapshot_' . time() . '.html';
        file_put_contents($filename, $dom->saveHTML());
        echo "Snapshot saved as <a href='$filename'>$filename</a>";
    } else {
        echo "Failed to fetch URL.";
    }
}
?>

<!DOCTYPE html>
<html>
<body>
<section class="content-section">
    <h2>Admin Panel</h2>
    <p><?php echo $message; ?></p>
    
    <h3>Remove from Reply File</h3>
     <p>(For each thread, replies are stored in that thread's .json file in the replies folder I.E. 1772938579389. To delete the thread itself, use the second input form value below instead. To delete replies, list the ID .json of the main thread first and then the IDs of whichever replies you wish to remove</p>
    <form method="post">
        Filename: <input type="text" name="filename" placeholder="123.json">
        ID: <input type="text" name="id">
        <input type="submit" name="remove_reply" value="Remove Reply">
    </form>
    
    <h3>Remove entire thread from Base Posts</h3>
   
    <form method="post">
        ID: <input type="text" name="id">
        <input type="submit" name="remove_main" value="Remove thread">
    </form>

    <h2>Public ban a post ID</h2>
    
    <?php echo $message2; ?>
    
      <form method="post" action="">
        <label>JSON File Name (without .json):</label><br>
        <input type="text" name="filename2" required><br><br>
        <label>ID to Ban:</label><br>
        <input type="text" name="targetId" required><br><br>
        <input type="submit" value="Process Request">
    </form>
    
    <h1>Publicly delete/"can" a post <img src="https://cdn-icons-png.flaticon.com/512/11073/11073124.png" alt="Description" width="20" height="20"></h1>

    <?php if ($message4): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div>
            <label for="file_name">JSON File Name (in 'replies' subfolder, e.g., `data.json`):</label>
            <input type="text" id="file_name" name="file_name" required value="<?php echo isset($_POST['file_name']) ? htmlspecialchars($_POST['file_name']) : ''; ?>">
        </div>
        <br>
        <div>
            <label for="reply_id">Reply ID to Update:</label>
            <input type="text" id="reply_id" name="reply_id" required value="<?php echo isset($_POST['reply_id']) ? htmlspecialchars($_POST['reply_id']) : ''; ?>">
        </div>
        <br>

        <br>
        <button type="submit">Update Reply</button>
    </form>
    
     <h2>Archive/snapshot a thread</h2>
    
    <form method="post">
    <input type="url" name="url" placeholder="Enter URL" required>
    <button type="submit">Create Snapshot</button>
</form>

    
    
    <h2>Apply capcode to post</h2>
<p><?php echo $message3; ?></p>

<form method="post" action="">
    <label>JSON File (no .json):</label><br>
    <input type="text" name="filename3" required><br><br>

    <label>Reply ID:</label><br>
    <input type="text" name="replyId" required><br><br>

    <label>Select Username Color:</label><br>
    <select name="color">
        <option value="Mod">##Mod</option>
        <option value="Developer">##Developer</option>
        <option value="Admin">##Admin</option>
    </select><br><br>

    <input type="submit" value="Update Username">
</form>


    
        <a href="?logout=1">Logout</a>
    
    
    </section>
    
    <link rel="stylesheet" type="text/css" href="adminpage.css">
</body>
</html>
<?php if(isset($_GET['logout'])) { session_destroy(); header("Location: ".$_SERVER['PHP_SELF']); } ?>
