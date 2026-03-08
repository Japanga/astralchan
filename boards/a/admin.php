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
    <a href="?logout=1">Logout</a>
    
    
    </section>
    
    <link rel="stylesheet" type="text/css" href="adminpage.css">
</body>
</html>
<?php if(isset($_GET['logout'])) { session_destroy(); header("Location: ".$_SERVER['PHP_SELF']); } ?>
