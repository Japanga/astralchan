<?php
function handleReplyUpload($postId) {
    
        
           // 1. Get the user's IP address
$ip = $_SERVER['REMOTE_ADDR'];

// 2. Create a unique hash based on the IP (and optional salt for extra security)
$hash = substr(hash('sha256', $ip . 'optional_salt'), 0, 16);

// 3. Create a "shadowmask" (e.g., mask part of the IP)
// This masks the last two octets of an IPv4 address
$maskedIp = preg_replace('/(\d+)\.(\d+)\.(\d+)\.(\d+)/', '$1.$2.XXX.XXX', $ip);

// 4. Combine mask and hash for the final ID
$shadowMaskId = "ID-" . $maskedIp . "-" . strtoupper($hash);
    
    $id = time() . mt_rand(100, 999); // Unique numeric ID
    $replyText = filter_input(INPUT_POST, 'reply_text', FILTER_SANITIZE_STRING);
      $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
       $tripcode = filter_input(INPUT_POST, 'content4', FILTER_SANITIZE_STRING);
    $timestamp = date('Y-m-d H:i:s');
    $imagePath = null;
    $fileSize = 0;
    $shadowmask = $shadowMaskId;
    $isSpoiler = isset($_POST['is_spoiler']) ? 1 : 0;

    // Handle image upload if one is provided
    if (isset($_FILES['reply_image']) && $_FILES['reply_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/replies/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageFileType = strtolower(pathinfo($_FILES['reply_image']['name'], PATHINFO_EXTENSION));
        $uniqueFileName = 'reply_' . $id . '.' . $imageFileType;
        $targetFilePath = $uploadDir . $uniqueFileName;

        if (move_uploaded_file($_FILES['reply_image']['tmp_name'], $targetFilePath)) {
            $imagePath = $targetFilePath;
            $fileSize = $_FILES['reply_image']['size'];
        } else {
            echo "Error uploading reply image.";
        }
    }

    $newReply = [
        'id' => $id,
        'text' => $replyText,
        'username' => $username,
        'tripcode' => $tripcode,
        'image_path' => $imagePath,
        'timestamp' => $timestamp,
        'file_size' => $fileSize,
        'shadowmask' => $shadowmask,
        'spoilerStatus' => $isSpoiler
    ];

    // Read existing replies for this post, add new one, and save back to JSON file
    $repliesFile = 'replies/' . $postId . '.json';
    $replies = file_exists($repliesFile) ? json_decode(file_get_contents($repliesFile), true) : [];
    $replies[] = $newReply;
    file_put_contents($repliesFile, json_encode($replies, JSON_PRETTY_PRINT));
}
?><?php
function handleReplyUpload($postId) {
    
        
           // 1. Get the user's IP address
$ip = $_SERVER['REMOTE_ADDR'];

// 2. Create a unique hash based on the IP (and optional salt for extra security)
$hash = substr(hash('sha256', $ip . 'optional_salt'), 0, 16);

// 3. Create a "shadowmask" (e.g., mask part of the IP)
// This masks the last two octets of an IPv4 address
$maskedIp = preg_replace('/(\d+)\.(\d+)\.(\d+)\.(\d+)/', '$1.$2.XXX.XXX', $ip);

// 4. Combine mask and hash for the final ID
$shadowMaskId = "ID-" . $maskedIp . "-" . strtoupper($hash);
    
    $id = time() . mt_rand(100, 999); // Unique numeric ID
    $replyText = filter_input(INPUT_POST, 'reply_text', FILTER_SANITIZE_STRING);
      $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
       $tripcode = filter_input(INPUT_POST, 'content4', FILTER_SANITIZE_STRING);
    $timestamp = date('Y-m-d H:i:s');
    $imagePath = null;
    $fileSize = 0;
    $shadowmask = $shadowMaskId;
    $isSpoiler = isset($_POST['is_spoiler']) ? 1 : 0;

    // Handle image upload if one is provided
    if (isset($_FILES['reply_image']) && $_FILES['reply_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/replies/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageFileType = strtolower(pathinfo($_FILES['reply_image']['name'], PATHINFO_EXTENSION));
        $uniqueFileName = 'reply_' . $id . '.' . $imageFileType;
        $targetFilePath = $uploadDir . $uniqueFileName;

        if (move_uploaded_file($_FILES['reply_image']['tmp_name'], $targetFilePath)) {
            $imagePath = $targetFilePath;
            $fileSize = $_FILES['reply_image']['size'];
        } else {
            echo "Error uploading reply image.";
        }
    }

    $newReply = [
        'id' => $id,
        'text' => $replyText,
        'username' => $username,
        'tripcode' => $tripcode,
        'image_path' => $imagePath,
        'timestamp' => $timestamp,
        'file_size' => $fileSize,
        'shadowmask' => $shadowmask,
        'spoilerStatus' => $isSpoiler
    ];

    // Read existing replies for this post, add new one, and save back to JSON file
    $repliesFile = 'replies/' . $postId . '.json';
    $replies = file_exists($repliesFile) ? json_decode(file_get_contents($repliesFile), true) : [];
    $replies[] = $newReply;
    file_put_contents($repliesFile, json_encode($replies, JSON_PRETTY_PRINT));
}
?>
