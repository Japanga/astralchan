<?php
function handleReplyUpload($postId) {
    $id = time() . mt_rand(100, 999); // Unique numeric ID
    $replyText = filter_input(INPUT_POST, 'reply_text', FILTER_SANITIZE_STRING);
      $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
       $tripcode = filter_input(INPUT_POST, 'content4', FILTER_SANITIZE_STRING);
    $timestamp = date('Y-m-d H:i:s');
    $imagePath = null;
    $fileSize = 0;

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
        'file_size' => $fileSize
    ];

    // Read existing replies for this post, add new one, and save back to JSON file
    $repliesFile = 'replies/' . $postId . '.json';
    $replies = file_exists($repliesFile) ? json_decode(file_get_contents($repliesFile), true) : [];
    $replies[] = $newReply;
    file_put_contents($repliesFile, json_encode($replies, JSON_PRETTY_PRINT));
}
?>